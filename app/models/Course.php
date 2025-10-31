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
    
        mysqli_stmt_bind_param($stmt, "i", $courseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $course = mysqli_fetch_assoc($result);
    
        // ✅ Haal FAQ’s op met juiste kolomnaam (cursus_id)
        $sqlFaq = "SELECT id AS FAQID, Vraag, Antwoord FROM CursusFAQ WHERE cursus_id = ?";
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
        $sql2 = "INSERT INTO Cursusdetails (cursus_id, KorteBeschrijving, Beschrijving, Rating, Taal, Prijs, LaatstBijgewerkt, Materiaal, Documenten, LeerJaarID)
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

                $sqlFaq = "INSERT INTO CursusFAQ (cursus_id, Vraag, Antwoord) VALUES (?, ?, ?)";
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
        mysqli_begin_transaction($this->conn);

        try {
            // 1️⃣ FAQ
            $stmtFaq = mysqli_prepare(
                $this->conn,
                "DELETE FROM CursusFAQ WHERE cursus_id = ?"
            );
            mysqli_stmt_bind_param($stmtFaq, "i", $courseId);
            mysqli_stmt_execute($stmtFaq);

            // 2️⃣ Categorie
            $stmtCat = mysqli_prepare(
                $this->conn,
                "DELETE FROM CursusCategorie WHERE cursus_id = ?"
            );
            mysqli_stmt_bind_param($stmtCat, "i", $courseId);
            mysqli_stmt_execute($stmtCat);

            // 3️⃣ Details
            $stmtDetails = mysqli_prepare(
                $this->conn,
                "DELETE FROM Cursusdetails WHERE cursus_id = ?"
            );
            mysqli_stmt_bind_param($stmtDetails, "i", $courseId);
            mysqli_stmt_execute($stmtDetails);

            // 4️⃣ Foto ophalen + verwijderen
            $stmtFoto = mysqli_prepare(
                $this->conn,
                "SELECT FotoURL FROM Cursus WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmtFoto, "i", $courseId);
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
            mysqli_stmt_bind_param($stmtMain, "i", $courseId);
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
                    "sssii",
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
                    "ssii",
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
                "ssiiii",
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
                "DELETE FROM CursusCategorie WHERE cursus_id = $courseId"
            );

            if (!empty($data['CategorieID'])) {
                $stmtCat = mysqli_prepare(
                    $this->conn,
                    "INSERT INTO CursusCategorie (cursus_id, categorie_id) VALUES (?, ?)"
                );
                mysqli_stmt_bind_param($stmtCat, "ii", $courseId, $data['CategorieID']);
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
                            "ssii",
                            $faq['vraag'],
                            $faq['antwoord'],
                            $faq['FAQID'],
                            $courseId
                        );
                        mysqli_stmt_execute($stmtU);

                    } else {
                        // INSERT nieuw
                        $sqlI = "INSERT INTO CursusFAQ (cursus_id, Vraag, Antwoord) VALUES (?, ?, ?)";
                        $stmtI = mysqli_prepare($this->conn, $sqlI);
                        mysqli_stmt_bind_param(
                            $stmtI,
                            "iss",
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
                $ids = implode(',', array_map('intval', $data['DeletedFaqIDs']));
                mysqli_query(
                    $this->conn,
                    "DELETE FROM CursusFAQ WHERE id IN ($ids) AND cursus_id = $courseId"
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