<?php
include_once __DIR__ . '/../helpers/generateUUID.php';
class Course
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Haal de meest bekeken cursussen op
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


    // Haal cursus + detailinformatie op
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

        return $course;
    }

    public function addRating($courseId, $rating)
    {
        $id = generateUUID();

        // 1) Nieuwe rating opslaan
        $sql = "INSERT INTO CursusRating (id, cursus_id, rating) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $id, $courseId, $rating);
        mysqli_stmt_execute($stmt);

        // 2) Nieuw gemiddelde berekenen
        $sql2 = "SELECT AVG(rating) AS avgRating FROM CursusRating WHERE cursus_id = ?";
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "s", $courseId);
        mysqli_stmt_execute($stmt2);
        $result = mysqli_stmt_get_result($stmt2);
        $avg = mysqli_fetch_assoc($result)['avgRating'];

        // 3) Gemiddelde opslaan in Cursusdetails tabel
        $sql3 = "UPDATE Cursusdetails SET Rating = ? WHERE cursus_id = ?";
        $stmt3 = mysqli_prepare($this->conn, $sql3);
        mysqli_stmt_bind_param($stmt3, "ds", $avg, $courseId);
        mysqli_stmt_execute($stmt3);

        return true;
    }


    public function addView($courseId)
    {
        $sql = "UPDATE Cursus SET Views = Views + 1 WHERE Id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
        mysqli_stmt_execute($stmt);
    }



    // Zoek cursussen op titel
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

    public function getAllCount(){
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




    public function getCategoriesByCourse($courseId)
    {
        $sql = "
        SELECT cat.id AS CategorieID, cat.Naam
        FROM Categorie cat
        INNER JOIN CursusCategorie cc ON cat.id = cc.categorie_id
        WHERE cc.cursus_id = ?";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        return $categories;
    }

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

        return $Id;
    }


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
            mysqli_query(
                $this->conn,
                "DELETE FROM CursusCategorie WHERE cursus_id = ?"
            );

            if (!empty($data['CategorieID'])) {
                $stmtCat = mysqli_prepare(
                    $this->conn,
                    "INSERT INTO CursusCategorie (cursus_id, categorie_id) VALUES (?, ?)"
                );
                mysqli_stmt_bind_param($stmtCat, "ss", $courseId, $data['CategorieID']);
                mysqli_stmt_execute($stmtCat);
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