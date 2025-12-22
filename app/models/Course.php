<?php
/**
 * Course
 *
 * Modelklasse voor het beheren van cursussen.
 * Bevat methodes voor CRUD, ratings, views, filters, categorieën en FAQ's.
 */
include_once __DIR__ . '/../helpers/generateUUID.php';
class Course
{
    /**
     * @var mysqli $conn De actieve databaseverbinding.
     */
    private $conn;

    /**
     * Constructor
     *
     * @param mysqli $db De databaseverbinding.
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Haalt de meest bekeken cursussen op.
     *
     * @param int $limit Maximaal aantal cursussen.
     * @return array Associatieve array van cursussen.
     */
    public function getFeaturedCourses($limit = 8)
    {
        $sql = "
        SELECT 
            c.Id,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            d.Rating,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        JOIN Cursusdetails d ON c.id = d.cursus_id
        LEFT JOIN CursusCategorie cc ON c.Id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
        where c.active = 1
        GROUP BY c.Id
        ORDER BY c.Views DESC
        LIMIT ?
        ";

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            die('Prepare failed: ' . mysqli_error($this->conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        $courses = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $courses[] = $row;
        }

        return $courses;
    }


    /**
     * Haalt detailinformatie van een cursus op, inclusief FAQ's.
     *
     * @param string $courseId ID van de cursus.
     * @return array Associatieve array met cursusdata.
     */
    public function getCourseDetail($courseId)
    {
        $sql = "
            SELECT 
                c.id,
                c.Titel,
                c.FotoURL,
                c.Link,
                c.Active,
                d.KorteBeschrijving,
                d.Beschrijving,
                d.LaatstBijgewerkt,
                d.Rating,
                d.Taal,
                d.Prijs,
                d.Materiaal,
                d.Documenten,
                d.LeerJaarID,
                cc.categorie_id AS CategorieID,
                GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
            FROM Cursus c
            JOIN Cursusdetails d ON c.id = d.cursus_id
            LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
            LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
            WHERE c.id = ?
            GROUP BY c.id
        ";

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            die("SQL ERROR (CourseDetail): " . mysqli_error($this->conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $courseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $course = mysqli_fetch_assoc($result);

        // ✅ Haal FAQ’s op met juiste kolomnaam (cursus_id)
        $sqlFaq = "SELECT id AS FAQID, Vraag, Antwoord FROM CursusFAQ WHERE cursus_id = ?";
        $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
        mysqli_stmt_bind_param($stmtFaq, "s", $courseId);
        mysqli_stmt_execute($stmtFaq);
        $resultFaq = mysqli_stmt_get_result($stmtFaq);
        $faqs = mysqli_fetch_all($resultFaq, MYSQLI_ASSOC);

        $course['Faqs'] = $faqs;

        // ✅ Haal documenten op
        $sqlDocs = "SELECT id, Naam, BestandURL, Bestandstype, UploadedAt 
        FROM CursusDocumenten 
        WHERE cursus_id = ?";
        $stmtDocs = mysqli_prepare($this->conn, $sqlDocs);
        mysqli_stmt_bind_param($stmtDocs, "s", $courseId);
        mysqli_stmt_execute($stmtDocs);
        $resultDocs = mysqli_stmt_get_result($stmtDocs);
        $documents = mysqli_fetch_all($resultDocs, MYSQLI_ASSOC);

        $course['DocumentenLijst'] = $documents;

        // ✅ haal cursus materialen op
        $sqlMat = "
                   SELECT m.Id, m.Naam, m.FotoURL 
                   FROM Materialen m
                   JOIN CursusMaterialen cm ON m.Id = cm.materiaal_id
                   WHERE cm.cursus_id = ?";

        $stmtMat = mysqli_prepare($this->conn, $sqlMat);
        mysqli_stmt_bind_param($stmtMat, "s", $courseId);
        mysqli_stmt_execute($stmtMat);
        $course['Materialen'] = mysqli_fetch_all(mysqli_stmt_get_result($stmtMat), MYSQLI_ASSOC);


        return $course;
    }


    public function getUserRating($courseId, $userId)
    {
        $sql = "SELECT rating FROM CursusRating WHERE cursus_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $courseId, $userId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($res)) {
            return (int) $row['rating'];
        }
        return 0;
    }


    /**
     * Voeg een rating toe aan een cursus en werk het gemiddelde bij.
     *
     * @param string $courseId
     * @param int $rating
     * @return bool
     */
    public function addOrUpdateRating($courseId, $userId, $rating)
    {
        $id = generateUUID();

        $sql = "INSERT INTO CursusRating (id, user_id, cursus_id, rating)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                rating = VALUES(rating),
                updated_at = NOW()";

        $stmt = mysqli_prepare($this->conn, $sql);

        if (!$stmt) {
            error_log("SQL PREPARE ERROR: " . mysqli_error($this->conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sssi", $id, $userId, $courseId, $rating);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            error_log("SQL EXEC ERROR: " . mysqli_stmt_error($stmt));
            return false;
        }

        // 2️⃣ Bereken nieuw gemiddelde
        $sqlAvg = "
            SELECT ROUND(AVG(rating), 1) AS avgRating
            FROM CursusRating
            WHERE cursus_id = ?
        ";

        $stmtAvg = mysqli_prepare($this->conn, $sqlAvg);
        mysqli_stmt_bind_param($stmtAvg, "s", $courseId);
        mysqli_stmt_execute($stmtAvg);

        $avg = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtAvg))['avgRating'];

        // 3️⃣ Update Cursusdetails
        $sqlUpdate = "
    UPDATE Cursusdetails
    SET Rating = ?
    WHERE cursus_id = ?
";

        $stmtUpdate = mysqli_prepare($this->conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "ds", $avg, $courseId);
        mysqli_stmt_execute($stmtUpdate);

        return true;

        return true;
    }



    /**
     * Verhoog het aantal views voor een cursus.
     *
     * @param string $courseId
     */
    public function addView($courseId)
    {
        $sql = "UPDATE Cursus SET Views = Views + 1 WHERE Id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
        mysqli_stmt_execute($stmt);
    }



    /**
     * Zoek cursussen op basis van titel.
     *
     * @param string $query
     * @return mysqli_result
     */
    public function searchCourses($query)
    {
        $sql = "
        SELECT 
            c.Id,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            d.Rating,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        JOIN Cursusdetails d ON c.id = d.cursus_id
        LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
        WHERE c.Titel LIKE CONCAT('%', ?, '%') AND c.Active = 1
        GROUP BY c.Id
        ORDER BY c.Views DESC
        ";

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            die('Prepare failed: ' . mysqli_error($this->conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $query);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Belangrijk: geef het mysqli_result direct terug
        return $result;
    }

    /**
     * Haal totaal aantal cursussen op.
     *
     * @return int
     */
    public function getAllCount()
    {
        $sql = "SELECT COUNT(DISTINCT c.Id) AS total
        FROM Cursus c
        ";
        $stmt = mysqli_prepare($this->conn, $sql);

        if (!$stmt) {
            die('Prepare failed: ' . mysqli_error($this->conn));
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        $total = mysqli_fetch_assoc($result)['total'] ?? 0;
        return $total;
    }


    public function getAllCourses()
    {
        $sql = "
        SELECT 
            c.Id,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            c.CreatedAt,
            c.Active,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
        WHERE c.Active = 1
        GROUP BY c.id
        ORDER BY c.Titel ASC";

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            die('Prepare failed: ' . mysqli_error($this->conn));
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        return $result;
    }

    public function getCoursesAdmin($filters = [], $limit = 10, $page = 1)
    {
        $search = $filters['search'] ?? '';
        $status = $filters['status'] ?? '';
        $sort = $filters['sort'] ?? '';
        $category = $filters['category'] ?? '';

        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];
        $types = '';

        // ✅ SEARCH
        if (!empty($search)) {
            $where[] = "c.Titel LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }

        // ✅ CATEGORY
        if (!empty($category)) {
            $where[] = "cc.categorie_id = ?";
            $params[] = $category;
            $types .= "s";
        }

        // ✅ STATUS FILTER (actief / pending)
        if ($status === "active") {
            $where[] = "c.Active = 1";
        } elseif ($status === "pending") {
            $where[] = "c.Active = 0";
        }

        // ✅ WHERE SQL
        $whereSql = "";
        if (count($where) > 0) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        // ✅ SORTERING
        switch ($sort) {
            case "newest":
                $orderBy = "ORDER BY c.CreatedAt DESC";
                break;

            case "oldest":
                $orderBy = "ORDER BY c.CreatedAt ASC";
                break;

            case "active":
                $orderBy = "ORDER BY c.Active DESC";
                break;

            case "pending":
                $orderBy = "ORDER BY c.Active ASC";
                break;

            default:
                $orderBy = "ORDER BY c.CreatedAt DESC";
        }

        // ✅ COUNT (totaal voor paginatie)
        $countSql = "
        SELECT COUNT(DISTINCT c.Id) AS total
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
        LEFT JOIN Cursusdetails d ON c.id = d.cursus_id
        $whereSql
     ";

        $countStmt = mysqli_prepare($this->conn, $countSql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($countStmt, $types, ...$params);
        }
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $total = mysqli_fetch_assoc($countResult)['total'] ?? 0;

        // ✅ MAIN QUERY
        $sql = "
        SELECT 
            c.Id, c.Titel, c.FotoURL, c.Link, c.Views, c.CreatedAt, c.Active,
            d.Rating,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN Cursusdetails d ON c.id = d.cursus_id
        LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
        $whereSql
        GROUP BY c.Id
        $orderBy
        LIMIT ? OFFSET ?
     ";

        $stmt = mysqli_prepare($this->conn, $sql);

        // ✅ Bind filters + limit + offset
        if (!empty($params)) {
            $types .= "ii";
            $params[] = $limit;
            $params[] = $offset;
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return [
            'result' => $result,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ];
    }



    public function getFilteredCourses($filters = [], $limit = 10, $page = 1)
    {
        $search = $filters['search'] ?? '';
        $category = $filters['category'] ?? '';
        $sort = $filters['sort'] ?? '';

        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];
        $types = '';

        $where[] = "c.Active = 1";

        // ✅ SEARCH
        if (!empty($search)) {
            $where[] = "c.Titel LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }

        // ✅ CATEGORY
        if (!empty($category)) {
            $where[] = "cc.categorie_id = ?";
            $params[] = $category;
            $types .= "s";
        }

        // ✅ WHERE SQL
        $whereSql = "";
        if (count($where) > 0) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        // ✅ SORTERING
        switch ($sort) {
            case "recent":
                $orderBy = "ORDER BY c.CreatedAt DESC";
                break;

            case "rating":
                $orderBy = "ORDER BY d.Rating DESC";
                break;

            case "views":
                $orderBy = "ORDER BY c.Views DESC";
                break;

            default:
                $orderBy = "ORDER BY c.CreatedAt DESC";
        }

        // ✅ COUNT (totaal voor paginatie)
        $countSql = "
            SELECT COUNT(*) AS total
            FROM Cursus c
            LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
            LEFT JOIN Cursusdetails d ON c.id = d.cursus_id
            $whereSql
        ";

        $countStmt = mysqli_prepare($this->conn, $countSql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($countStmt, $types, ...$params);
        }
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $total = mysqli_fetch_assoc($countResult)['total'] ?? 0;

        // ✅ MAIN QUERY
        $sql = "
            SELECT 
                c.Id, c.Titel, c.FotoURL, c.Link, c.Views, c.CreatedAt, c.Active,
                d.Rating,
                GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
            FROM Cursus c
            LEFT JOIN Cursusdetails d ON c.id = d.cursus_id
            LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
            LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
            $whereSql
            GROUP BY c.Id
            $orderBy
            LIMIT ? OFFSET ?
        ";

        $stmt = mysqli_prepare($this->conn, $sql);

        // ✅ Bind filters + limit + offset
        if (!empty($params)) {
            $types .= "ii";
            $params[] = $limit;
            $params[] = $offset;
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return [
            'result' => $result,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ];
    }


    /**
     * Haal alle actieve cursussen op.
     *
     * @return mysqli_result
     */
    public function getActivatedCourses()
    {
        $sql = "
        SELECT 
            c.Id,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.Id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id  = cat.id
        WHERE c.Active = 1
        GROUP BY c.Id
        ORDER BY c.Titel ASC
        ";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        return $result;
    }


    /**
     * Haal inactieve cursussen op.
     *
     * @return mysqli_result
     */
    public function getInactiveCourses()
    {
        $sql = "
        SELECT * 
        FROM Cursus 
        WHERE Active = 0
        ";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        return $result;
    }



    public function createCourse($data)
    {
        $Id = generateUUID();
        // 1️ Insert in Cursus
        $sql = "INSERT INTO Cursus (Id , Titel, FotoURL, Link, Views, Featured, Active)
            VALUES (?, ?, ?, ?, 0, 0, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $Id, $data['Titel'], $data['FotoURL'], $data['Link'], $data['Active']);
        mysqli_stmt_execute($stmt);


        $CursusDetailId = $Id;
        // 2️ Insert in Cursusdetails
        $sql2 = "INSERT INTO Cursusdetails (Id, cursus_id, KorteBeschrijving, Beschrijving, Rating, Taal, Prijs, LaatstBijgewerkt, Materiaal, Documenten, LeerJaarID)
             VALUES (?, ?, ?, ?, 0, 'Nederlands', 0, NOW(), ?, ?, ?)";
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "sssssss", $CursusDetailId, $Id, $data['KorteBeschrijving'], $data['Beschrijving'], $data['Materiaal'], $data['Documenten'], $data['LeerJaarID']);
        mysqli_stmt_execute($stmt2);
        if (mysqli_error($this->conn)) {
            die("❌ Fout bij Cursusdetails: " . mysqli_error($this->conn));
        }

        // 3️ Koppel categorie
        $sql3 = "INSERT INTO CursusCategorie (cursus_id, categorie_id) VALUES (?, ?)";
        $stmt3 = mysqli_prepare($this->conn, $sql3);
        mysqli_stmt_bind_param($stmt3, "ss", $Id, $data['CategorieID']);
        mysqli_stmt_execute($stmt3);
        if (mysqli_error($this->conn)) {
            die("❌ Fout bij CursusCategorie: " . mysqli_error($this->conn));
        }


        // 4️ Voeg FAQ’s toe (indien aanwezig)
        if (!empty($data['faqs']) && is_array($data['faqs'])) {
            foreach ($data['faqs'] as $faq) {
                $vraag = $faq['vraag'];
                $antwoord = $faq['antwoord'];
                $faqId = generateUUID();

                $sqlFaq = "INSERT INTO CursusFAQ (Id, cursus_id, Vraag, Antwoord) VALUES (?, ?, ?, ?)";
                $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
                mysqli_stmt_bind_param($stmtFaq, "ssss", $faqId, $Id, $vraag, $antwoord);
                mysqli_stmt_execute($stmtFaq);
            }
        }

        // Material koppelen
        if (!empty($data['material_ids'])) {
            foreach ($data['material_ids'] as $matId) {
                $linkId = generateUUID();

                $sqlMat = "INSERT INTO CursusMaterialen (Id, cursus_id, materiaal_id) VALUES (?, ?, ?)";
                $stmtMat = mysqli_prepare($this->conn, $sqlMat);

                mysqli_stmt_bind_param($stmtMat, "sss", $linkId, $Id, $matId);
                mysqli_stmt_execute($stmtMat);
            }
        }

        return $Id;
    }


    /**
     * Haal laatst bijgewerkte cursussen op.
     *
     * @param int $limit
     * @return mysqli_result
     */
    public function getLatestUpdatedCourses($limit = 5)
    {
        $sql = "
             SELECT 
                 c.Id,
                 c.Titel,
                 c.FotoURL,
                 c.CreatedAt,
                 c.Active,
                 d.LaatstBijgewerkt
             FROM Cursus c
             LEFT JOIN Cursusdetails d ON c.Id = d.Id
             ORDER BY d.LaatstBijgewerkt DESC, c.CreatedAt DESC
             LIMIT ?
             ";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }


    public function deleteCourse($courseId)
    {
        mysqli_begin_transaction($this->conn);

        try {
            // 1️⃣ FAQ
            $stmtFaq = mysqli_prepare(
                $this->conn,
                "DELETE FROM CursusFAQ WHERE cursus_id = ?"
            );
            mysqli_stmt_bind_param($stmtFaq, "s", $courseId);
            mysqli_stmt_execute($stmtFaq);

            // 2️⃣ Categorie
            $stmtCat = mysqli_prepare(
                $this->conn,
                "DELETE FROM CursusCategorie WHERE cursus_id = ?"
            );
            mysqli_stmt_bind_param($stmtCat, "s", $courseId);
            mysqli_stmt_execute($stmtCat);

            // 3️⃣ Details
            $stmtDetails = mysqli_prepare(
                $this->conn,
                "DELETE FROM Cursusdetails WHERE cursus_id = ?"
            );
            mysqli_stmt_bind_param($stmtDetails, "s", $courseId);
            mysqli_stmt_execute($stmtDetails);

            // 4️⃣ Foto ophalen + verwijderen
            $stmtFoto = mysqli_prepare(
                $this->conn,
                "SELECT FotoURL FROM Cursus WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmtFoto, "s", $courseId);
            mysqli_stmt_execute($stmtFoto);

            $foto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtFoto));
            if ($foto && file_exists($foto['FotoURL'])) {
                unlink($foto['FotoURL']);
            }

            // 5️⃣ Hoofdcursus verwijderen
            $stmtMain = mysqli_prepare(
                $this->conn,
                "DELETE FROM Cursus WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmtMain, "s", $courseId);
            mysqli_stmt_execute($stmtMain);

            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return false;
        }
    }




    public function updateCourse($courseId, $data)
    {
        mysqli_begin_transaction($this->conn);

        try {
            // 1️⃣ Update Cursus
            if (!empty($data['FotoURL'])) {
                $sql = "UPDATE Cursus 
                    SET Titel=?, FotoURL=?, Link=?, Active=?
                    WHERE id=?";
                $stmt = mysqli_prepare($this->conn, $sql);
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssis",
                    $data['Titel'],
                    $data['FotoURL'],
                    $data['Link'],
                    $data['Active'],
                    $courseId
                );
            } else {
                $sql = "UPDATE Cursus 
                    SET Titel=?, Link=?, Active=?
                    WHERE id=?";
                $stmt = mysqli_prepare($this->conn, $sql);
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssis",
                    $data['Titel'],
                    $data['Link'],
                    $data['Active'],
                    $courseId
                );
            }
            mysqli_stmt_execute($stmt);

            // 2️⃣ Update details
            $sql2 = "UPDATE Cursusdetails
                 SET KorteBeschrijving=?, Beschrijving=?, Materiaal=?, Documenten=?, LeerJaarID=?
                 WHERE cursus_id=?";
            $stmt2 = mysqli_prepare($this->conn, $sql2);
            mysqli_stmt_bind_param(
                $stmt2,
                "ssiiss",
                $data['KorteBeschrijving'],
                $data['Beschrijving'],
                $data['Materiaal'],
                $data['Documenten'],
                $data['LeerJaarID'],
                $courseId
            );
            mysqli_stmt_execute($stmt2);

            // 3️⃣ Categorie opnieuw koppelen
            $stmtDelCat = mysqli_prepare(
                $this->conn,
                "DELETE FROM CursusCategorie WHERE cursus_id = ?"
            );
            mysqli_stmt_bind_param($stmtDelCat, "s", $courseId);
            mysqli_stmt_execute($stmtDelCat);


            if (!empty($data['CategorieID'])) {
                $stmtCat = mysqli_prepare(
                    $this->conn,
                    "INSERT INTO CursusCategorie (cursus_id, categorie_id) VALUES (?, ?)"
                );
                mysqli_stmt_bind_param($stmtCat, "ss", $courseId, $data['CategorieID']);
                mysqli_stmt_execute($stmtCat);
            }


            // 6️⃣ Documenten verwerken
            // Verwijder geselecteerde documenten
            if (!empty($data['DeletedDocumentIDs'])) {
                foreach ($data['DeletedDocumentIDs'] as $docId) {
                    $stmtPath = mysqli_prepare($this->conn, "SELECT BestandURL FROM CursusDocumenten WHERE Id = ?");
                    mysqli_stmt_bind_param($stmtPath, "s", $docId);
                    mysqli_stmt_execute($stmtPath);
                    $resPath = mysqli_stmt_get_result($stmtPath);
                    $doc = mysqli_fetch_assoc($resPath);
                    if ($doc && file_exists($doc['BestandURL'])) {
                        unlink($doc['BestandURL']);
                    }

                    $stmtDel = mysqli_prepare($this->conn, "DELETE FROM CursusDocumenten WHERE Id = ?");
                    mysqli_stmt_bind_param($stmtDel, "s", $docId);
                    mysqli_stmt_execute($stmtDel);
                }
            }

            // Nieuwe documenten toevoegen
            if (!empty($data['UploadedDocuments'])) {
                foreach ($data['UploadedDocuments'] as $doc) {

                    $docId = generateUUID();
                    $bestandUrl = $doc['path'];
                    $bestandNaam = $doc['name'];
                    $bestandType = $doc['type'];

                    $stmtAdd = mysqli_prepare(
                        $this->conn,
                        "INSERT INTO CursusDocumenten (Id, cursus_id, Naam, BestandURL, Bestandstype) 
                         VALUES (?, ?, ?, ?, ?)"
                    );

                    if (!$stmtAdd) {
                        throw new Exception("Prepare failed: " . mysqli_error($this->conn));
                    }

                    mysqli_stmt_bind_param(
                        $stmtAdd,
                        "sssss",
                        $docId,
                        $courseId,
                        $bestandNaam,   // juiste kolom!
                        $bestandUrl,
                        $bestandType    // juiste kolom!
                    );

                    mysqli_stmt_execute($stmtAdd);
                }
            }


            //materiaal koppeling verwijderen
            if (!empty($data['DeletedMaterialIDs'])) {
                foreach ($data['DeletedMaterialIDs'] as $matId) {
                    $stmtDel = mysqli_prepare(
                        $this->conn,
                        "DELETE FROM CursusMaterialen WHERE cursus_id=? AND materiaal_id=?"
                    );
                    mysqli_stmt_bind_param($stmtDel, "ss", $courseId, $matId);
                    mysqli_stmt_execute($stmtDel);
                }
            }

            foreach ($data['SelectedMaterialIds'] as $matId) {
                $linkId = generateUUID();
                $stmtAdd = mysqli_prepare(
                    $this->conn,
                    "INSERT INTO CursusMaterialen (Id, cursus_id, materiaal_id) VALUES (?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmtAdd, "sss", $linkId, $courseId, $matId);
                mysqli_stmt_execute($stmtAdd);
            }

            // 4️⃣ Update FAQ’s
            if (!empty($data['Faqs'])) {
                foreach ($data['Faqs'] as $faq) {

                    if (!empty($faq['FAQID'])) {
                        // UPDATE bestaand
                        $sqlU = "UPDATE CursusFAQ 
                             SET Vraag=?, Antwoord=?
                             WHERE id=? AND cursus_id=?";
                        $stmtU = mysqli_prepare($this->conn, $sqlU);
                        mysqli_stmt_bind_param(
                            $stmtU,
                            "ssss",
                            $faq['vraag'],
                            $faq['antwoord'],
                            $faq['FAQID'],
                            $courseId
                        );
                        mysqli_stmt_execute($stmtU);

                    } else {
                        // INSERT nieuw
                        $faqId = generateUUID();
                        $sqlI = "INSERT INTO CursusFAQ (Id, cursus_id, Vraag, Antwoord) VALUES (?, ?, ?, ?)";
                        $stmtI = mysqli_prepare($this->conn, $sqlI);
                        mysqli_stmt_bind_param(
                            $stmtI,
                            "ssss",
                            $faqId,
                            $courseId,
                            $faq['vraag'],
                            $faq['antwoord']
                        );
                        mysqli_stmt_execute($stmtI);
                    }
                }
            }

            // 5️⃣ Verwijder FAQ’s
            if (!empty($data['DeletedFaqIDs'])) {
                $ids = array_map(function ($id) {
                    return "'" . mysqli_real_escape_string($this->conn, $id) . "'";
                }, $data['DeletedFaqIDs']);

                $idList = implode(',', $ids);

                mysqli_query(
                    $this->conn,
                    "DELETE FROM CursusFAQ WHERE id IN ($idList) AND cursus_id='$courseId'"
                );
            }

            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return false;
        }
    }


}


?>