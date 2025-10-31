<?php
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
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.Id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
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
            cc.CategorieID,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        JOIN Cursusdetails d ON c.Id = d.id
        LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.CategorieID = cat.id
        WHERE c.id = ?
        GROUP BY c.id
        ";


        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $courseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $course = mysqli_fetch_assoc($result);

        // Haal FAQ’s apart op
        $sqlFaq = "SELECT FAQID, Vraag, Antwoord FROM CursusFAQ WHERE id = ?";
        $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
        mysqli_stmt_bind_param($stmtFaq, "i", $courseId);
        mysqli_stmt_execute($stmtFaq);
        $resultFaq = mysqli_stmt_get_result($stmtFaq);
        $faqs = mysqli_fetch_all($resultFaq, MYSQLI_ASSOC);

        $course['Faqs'] = $faqs;

        return $course;
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
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
        WHERE c.Titel LIKE CONCAT('%', ?, '%')
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


    public function getFilteredCourses($filters = [], $limit = 10, $page = 1)
    {
        $search = $filters['search'] ?? '';
        $status = $filters['status'] ?? '';
        $sort = $filters['sort'] ?? '';

        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];
        $types = '';

        // Zoekfilter
        if (!empty($search)) {
            $where[] = "c.Titel LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }

        // Statusfilter
        if ($status === 'active') {
            $where[] = "c.Active = 1";
        } elseif ($status === 'pending') {
            $where[] = "c.Active = 0";
        }

        $whereSql = '';
        if (count($where) > 0) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        // Sorteren
        $orderBy = "ORDER BY c.CreatedAt DESC";
        if ($sort === 'oldest') {
            $orderBy = "ORDER BY c.CreatedAt ASC";
        } elseif ($sort === 'active') {
            $orderBy = "ORDER BY c.Active DESC";
        } elseif ($sort === 'pending') {
            $orderBy = "ORDER BY c.Active ASC";
        }

        // Totaal aantal records (voor paginatie)
        $countSql = "SELECT COUNT(*) as total FROM Cursus c $whereSql";
        $countStmt = mysqli_prepare($this->conn, $countSql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($countStmt, $types, ...$params);
        }
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $total = mysqli_fetch_assoc($countResult)['total'] ?? 0;

        // Hoofdquery
        $sql = "
        SELECT 
            c.Id, c.Titel, c.FotoURL, c.Link, c.Views, c.CreatedAt, c.Active,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.id = cc.cursus_id
        LEFT JOIN Categorie cat ON cc.categorie_id = cat.id
        $whereSql
        GROUP BY c.Id
        $orderBy
        LIMIT ? OFFSET ?
    ";

        $stmt = mysqli_prepare($this->conn, $sql);

        // Bind params
        if (!empty($params)) {
            $types .= "ii";
            $params[] = $limit;
            $params[] = $offset;
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        } else {
            if (!$stmt) {
                die('Prepare failed: ' . mysqli_error($this->conn) . "\nSQL: " . $sql);
            }
            
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
        mysqli_stmt_bind_param($stmt, "i", $courseId);
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
    // 1️ Insert in Cursus
    $sql = "INSERT INTO Cursus (Titel, FotoURL, Link, Views, Featured, Active)
            VALUES (?, ?, ?, 0, 0, ?)";
    $stmt = mysqli_prepare($this->conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $data['Titel'], $data['FotoURL'], $data['Link'], $data['Active']);
    mysqli_stmt_execute($stmt);
    $Id = mysqli_insert_id($this->conn);

    if (!$Id) {
        die("❌ Fout bij Cursus: " . mysqli_error($this->conn));
    }

    // 2️ Insert in Cursusdetails
    $sql2 = "INSERT INTO Cursusdetails (CursusID, KorteBeschrijving, Beschrijving, Rating, Taal, Prijs, LaatstBijgewerkt, Materiaal, Documenten, LeerJaarID)
             VALUES (?, ?, ?, 0, 'Nederlands', 0, NOW(), ?, ?, ?)";
    $stmt2 = mysqli_prepare($this->conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "issssi", $Id, $data['KorteBeschrijving'], $data['Beschrijving'], $data['Materiaal'], $data['Documenten'], $data['LeerJaarID']);
    mysqli_stmt_execute($stmt2);
    if (mysqli_error($this->conn)) {
        die("❌ Fout bij Cursusdetails: " . mysqli_error($this->conn));
    }

    // 3️ Koppel categorie
    $sql3 = "INSERT INTO CursusCategorie (cursus_id, categorie_id) VALUES (?, ?)";
    $stmt3 = mysqli_prepare($this->conn, $sql3);
    mysqli_stmt_bind_param($stmt3, "ii", $Id, $data['CategorieID']);
    mysqli_stmt_execute($stmt3);
    if (mysqli_error($this->conn)) {
        die("❌ Fout bij CursusCategorie: " . mysqli_error($this->conn));
    }

    // 4️ Voeg FAQ’s toe (indien aanwezig)
    if (!empty($data['faqs']) && is_array($data['faqs'])) {
        foreach ($data['faqs'] as $faq) {
            $vraag = $faq['vraag'];
            $antwoord = $faq['antwoord'];

            $sqlFaq = "INSERT INTO CursusFAQ (CursusID, Vraag, Antwoord) VALUES (?, ?, ?)";
            $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
            mysqli_stmt_bind_param($stmtFaq, "iss", $Id, $vraag, $antwoord);
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
        // Begin transactie
        mysqli_begin_transaction($this->conn);

        try {
            // 1️ Verwijder gekoppelde FAQ's
            $sqlFaq = "DELETE FROM CursusFAQ WHERE Id = ?";
            $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
            mysqli_stmt_bind_param($stmtFaq, "i", $courseId);
            mysqli_stmt_execute($stmtFaq);

            // 2️ Verwijder categorie-koppelingen
            $sqlCat = "DELETE FROM CursusCategorie WHERE Id = ?";
            $stmtCat = mysqli_prepare($this->conn, $sqlCat);
            mysqli_stmt_bind_param($stmtCat, "i", $courseId);
            mysqli_stmt_execute($stmtCat);

            // 3️ Verwijder cursusdetails
            $sqlDetails = "DELETE FROM Cursusdetails WHERE Id = ?";
            $stmtDetails = mysqli_prepare($this->conn, $sqlDetails);
            mysqli_stmt_bind_param($stmtDetails, "i", $courseId);
            mysqli_stmt_execute($stmtDetails);

            // Verwijder foto van server
            $sqlFoto = "SELECT FotoURL FROM Cursus WHERE Id = ?";
            $stmtFoto = mysqli_prepare($this->conn, $sqlFoto);
            mysqli_stmt_bind_param($stmtFoto, "i", $courseId);
            mysqli_stmt_execute($stmtFoto);
            $result = mysqli_stmt_get_result($stmtFoto);
            $foto = mysqli_fetch_assoc($result);

            if ($foto && file_exists($foto['FotoURL'])) {
                unlink($foto['FotoURL']);
            }


            // 4️ Verwijder hoofdrecord
            $sqlMain = "DELETE FROM Cursus WHERE Id = ?";
            $stmtMain = mysqli_prepare($this->conn, $sqlMain);
            mysqli_stmt_bind_param($stmtMain, "i", $courseId);
            mysqli_stmt_execute($stmtMain);

            // Alles gelukt → commit
            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            // Fout → rollback
            mysqli_rollback($this->conn);
            return false;
        }
    }



    public function updateCourse($courseId, $data)
    {
        mysqli_begin_transaction($this->conn);

        try {
            // 1️ Update hoofdtabel (Cursus)
            if (!empty($data['FotoURL'])) {
                $sql = "UPDATE Cursus 
                    SET Titel = ?, FotoURL = ?, Link = ?, Active = ?
                    WHERE Id = ?";
                $stmt = mysqli_prepare($this->conn, $sql);
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssii",
                    $data['Titel'],
                    $data['FotoURL'],
                    $data['Link'],
                    $data['Active'],
                    $courseId
                );
            } else {
                $sql = "UPDATE Cursus 
                    SET Titel = ?, Link = ?, Active = ?
                    WHERE Id = ?";
                $stmt = mysqli_prepare($this->conn, $sql);
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssii",
                    $data['Titel'],
                    $data['Link'],
                    $data['Active'],
                    $courseId
                );
            }
            mysqli_stmt_execute($stmt);

            // 2️ Update detailtabel
            $leerjaarId = !empty($data['LeerJaarID']) ? $data['LeerJaarID'] : NULL;

            $sql2 = "UPDATE Cursusdetails
                 SET KorteBeschrijving = ?, Beschrijving = ?, Materiaal = ?, Documenten = ?, LeerJaarID = ?
                 WHERE Id = ?";
            $stmt2 = mysqli_prepare($this->conn, $sql2);
            mysqli_stmt_bind_param(
                $stmt2,
                "ssiiii",
                $data['KorteBeschrijving'],
                $data['Beschrijving'],
                $data['Materiaal'],
                $data['Documenten'],
                $leerjaarId,
                $courseId
            );
            mysqli_stmt_execute($stmt2);

            // 3️ Categorie bijwerken (simpel: oude verwijderen → nieuwe toevoegen)
            $sqlDel = "DELETE FROM CursusCategorie WHERE Id = ?";
            $stmtDel = mysqli_prepare($this->conn, $sqlDel);
            mysqli_stmt_bind_param($stmtDel, "i", $courseId);
            mysqli_stmt_execute($stmtDel);

            if (!empty($data['CategorieID'])) {
                $sqlCat = "INSERT INTO CursusCategorie (Id, CategorieID) VALUES (?, ?)";
                $stmtCat = mysqli_prepare($this->conn, $sqlCat);
                mysqli_stmt_bind_param($stmtCat, "ii", $courseId, $data['CategorieID']);
                mysqli_stmt_execute($stmtCat);
            }

            // 4️ FAQ's logica — veilig bijwerken
            if (!empty($data['Faqs']) && is_array($data['Faqs'])) {
                foreach ($data['Faqs'] as $faq) {
                    $vraag = trim($faq['vraag'] ?? '');
                    $antwoord = trim($faq['antwoord'] ?? '');
                    $faqId = $faq['FAQID'] ?? null;

                    if ($vraag === '' || $antwoord === '')
                        continue;

                    // UPDATE bestaande FAQ
                    if (!empty($faqId)) {
                        $sqlFaqUpdate = "UPDATE CursusFAQ SET Vraag = ?, Antwoord = ? WHERE FAQID = ? AND Id = ?";
                        $stmtFaqUpdate = mysqli_prepare($this->conn, $sqlFaqUpdate);
                        mysqli_stmt_bind_param($stmtFaqUpdate, "ssii", $vraag, $antwoord, $faqId, $courseId);
                        mysqli_stmt_execute($stmtFaqUpdate);
                    } else {
                        // INSERT nieuwe FAQ
                        $sqlFaqInsert = "INSERT INTO CursusFAQ (Id, Vraag, Antwoord) VALUES (?, ?, ?)";
                        $stmtFaqInsert = mysqli_prepare($this->conn, $sqlFaqInsert);
                        mysqli_stmt_bind_param($stmtFaqInsert, "iss", $courseId, $vraag, $antwoord);
                        mysqli_stmt_execute($stmtFaqInsert);
                    }
                }
            }

            // 5️ FAQ’s verwijderen (alleen die door frontend gevraagd worden)
            if (!empty($data['DeletedFaqIDs']) && is_array($data['DeletedFaqIDs'])) {
                $ids = implode(',', array_map('intval', $data['DeletedFaqIDs']));
                $sqlDelFaqs = "DELETE FROM CursusFAQ WHERE FAQID IN ($ids) AND Id = $courseId";
                mysqli_query($this->conn, $sqlDelFaqs);
            }

            mysqli_commit($this->conn);
            return true;
        } catch (Throwable $e) {
            mysqli_rollback($this->conn);
            error_log("UpdateCourse error: " . $e->getMessage());
            return false;
        }
    }





}


?>