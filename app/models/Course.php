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
            c.CursusID,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.CursusID = cc.CursusID
        LEFT JOIN Categorie cat ON cc.CategorieID = cat.CategorieID
        GROUP BY c.CursusID
        ORDER BY c.Views DESC
        LIMIT ?
        ";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        return $result;
    }


    // Haal cursus + detailinformatie op
    public function getCourseDetail($courseId)
    {
        $sql = "
        SELECT 
            c.CursusID,
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
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        JOIN Cursusdetails d ON c.CursusID = d.CursusID
        LEFT JOIN CursusCategorie cc ON c.CursusID = cc.CursusID
        LEFT JOIN Categorie cat ON cc.CategorieID = cat.CategorieID
        WHERE c.CursusID = ?
        GROUP BY c.CursusID
        ";


        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $courseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    // Zoek cursussen op titel
    public function searchCourses($query)
    {
        $sql = "
        SELECT 
            c.CursusID,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.CursusID = cc.CursusID
        LEFT JOIN Categorie cat ON cc.CategorieID = cat.CategorieID
        WHERE c.Titel LIKE CONCAT('%', ?, '%')
        GROUP BY c.CursusID
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
            c.CursusID,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            c.CreatedAt,
            GROUP_CONCAT(cat.Naam SEPARATOR ', ') AS CategorieNamen
        FROM Cursus c
        LEFT JOIN CursusCategorie cc ON c.CursusID = cc.CursusID
        LEFT JOIN Categorie cat ON cc.CategorieID = cat.CategorieID
        GROUP BY c.CursusID
        ORDER BY c.Titel ASC
        ";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        return $result;
    }


    public function getCategoriesByCourse($courseId)
    {
        $sql = "
        SELECT cat.CategorieID, cat.Naam
        FROM Categorie cat
        INNER JOIN CursusCategorie cc ON cat.CategorieID = cc.CategorieID
        WHERE cc.CursusID = ?";

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




    public function createCourse($data)
    {
        // 1️ Insert in Cursus
        $sql = "INSERT INTO Cursus (Titel, FotoURL, Link, Views, Featured)
            VALUES (?, ?, ?, 0, 0)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $data['Titel'], $data['FotoURL'], $data['Link']);
        mysqli_stmt_execute($stmt);
        $cursusId = mysqli_insert_id($this->conn);

        if (!$cursusId) {
            return false;
        }

        // 2 Insert in Cursusdetails
        $sql2 = "INSERT INTO Cursusdetails (CursusID, KorteBeschrijving, Beschrijving, Rating, Taal, Prijs, LaatstBijgewerkt)
             VALUES (?, ?, ?, 0, 'Nederlands', 0, NOW())";
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "iss", $cursusId, $data['KorteBeschrijving'], $data['Beschrijving']);
        mysqli_stmt_execute($stmt2);

        // 3 Koppel categorie
        $sql3 = "INSERT INTO CursusCategorie (CursusID, CategorieID) VALUES (?, ?)";
        $stmt3 = mysqli_prepare($this->conn, $sql3);
        mysqli_stmt_bind_param($stmt3, "ii", $cursusId, $data['CategorieID']);
        mysqli_stmt_execute($stmt3);


        // 4️ Voeg FAQ’s toe (indien aanwezig)
        if (!empty($data['faqs']) && is_array($data['faqs'])) {
            foreach ($data['faqs'] as $faq) {
                $vraag = $faq['vraag'];
                $antwoord = $faq['antwoord'];

                $sqlFaq = "INSERT INTO CursusFAQ (CursusID, Vraag, Antwoord) VALUES (?, ?, ?)";
                $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
                mysqli_stmt_bind_param($stmtFaq, "iss", $cursusId, $vraag, $antwoord);
                mysqli_stmt_execute($stmtFaq);
            }
        }

        return $cursusId;
    }


    public function deleteCourse($courseId)
    {
        // Begin transactie
        mysqli_begin_transaction($this->conn);

        try {
            // 1️ Verwijder gekoppelde FAQ's
            $sqlFaq = "DELETE FROM CursusFAQ WHERE CursusID = ?";
            $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
            mysqli_stmt_bind_param($stmtFaq, "i", $courseId);
            mysqli_stmt_execute($stmtFaq);

            // 2️ Verwijder categorie-koppelingen
            $sqlCat = "DELETE FROM CursusCategorie WHERE CursusID = ?";
            $stmtCat = mysqli_prepare($this->conn, $sqlCat);
            mysqli_stmt_bind_param($stmtCat, "i", $courseId);
            mysqli_stmt_execute($stmtCat);

            // 3️ Verwijder cursusdetails
            $sqlDetails = "DELETE FROM Cursusdetails WHERE CursusID = ?";
            $stmtDetails = mysqli_prepare($this->conn, $sqlDetails);
            mysqli_stmt_bind_param($stmtDetails, "i", $courseId);
            mysqli_stmt_execute($stmtDetails);

            // Verwijder foto van server
            $sqlFoto = "SELECT FotoURL FROM Cursus WHERE CursusID = ?";
            $stmtFoto = mysqli_prepare($this->conn, $sqlFoto);
            mysqli_stmt_bind_param($stmtFoto, "i", $courseId);
            mysqli_stmt_execute($stmtFoto);
            $result = mysqli_stmt_get_result($stmtFoto);
            $foto = mysqli_fetch_assoc($result);

            if ($foto && file_exists($foto['FotoURL'])) {
                unlink($foto['FotoURL']);
            }


            // 4️ Verwijder hoofdrecord
            $sqlMain = "DELETE FROM Cursus WHERE CursusID = ?";
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
            // 1️ Update hoofdgegevens (Cursus)
            if (!empty($data['FotoURL'])) {
                $sql = "UPDATE Cursus 
                    SET Titel = ?, FotoURL = ?, Link = ?, Active = ?
                    WHERE CursusID = ?";
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
                    WHERE CursusID = ?";
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

            // 2️ Update details (Cursusdetails)
            $sql2 = "UPDATE Cursusdetails
                SET KorteBeschrijving = ?, Beschrijving = ?, Materiaal = ?, Documenten = ?, LeerJaarID = ?
                WHERE CursusID = ?";
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

            // 3️ Update categorie
            if (!empty($data['CategorieID'])) {
                $sqlDel = "DELETE FROM CursusCategorie WHERE CursusID = ?";
                $stmtDel = mysqli_prepare($this->conn, $sqlDel);
                mysqli_stmt_bind_param($stmtDel, "i", $courseId);
                mysqli_stmt_execute($stmtDel);

                $sqlCat = "INSERT INTO CursusCategorie (CursusID, CategorieID) VALUES (?, ?)";
                $stmtCat = mysqli_prepare($this->conn, $sqlCat);
                mysqli_stmt_bind_param($stmtCat, "ii", $courseId, $data['CategorieID']);
                mysqli_stmt_execute($stmtCat);
            }

            // 4️ Update FAQ’s (eenvoudig: verwijder en voeg opnieuw toe)
            if (!empty($data['Faqs'])) {
                $sqlDelFaq = "DELETE FROM CursusFAQ WHERE CursusID = ?";
                $stmtDelFaq = mysqli_prepare($this->conn, $sqlDelFaq);
                mysqli_stmt_bind_param($stmtDelFaq, "i", $courseId);
                mysqli_stmt_execute($stmtDelFaq);

                foreach ($data['Faqs'] as $faq) {
                    $vraag = $faq['vraag'];
                    $antwoord = $faq['antwoord'];
                    $sqlFaq = "INSERT INTO CursusFAQ (CursusID, Vraag, Antwoord) VALUES (?, ?, ?)";
                    $stmtFaq = mysqli_prepare($this->conn, $sqlFaq);
                    mysqli_stmt_bind_param($stmtFaq, "iss", $courseId, $vraag, $antwoord);
                    mysqli_stmt_execute($stmtFaq);
                }
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