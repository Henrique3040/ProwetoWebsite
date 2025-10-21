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
            d.KorteBeschrijving,
            d.Beschrijving,
            d.LaatstBijgewerkt,
            d.Rating,
            d.Taal,
            d.Prijs,
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



}


?>