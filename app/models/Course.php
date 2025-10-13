<?php
class Course
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getFeaturedCourses($limit = 8)
    {
        $sql = "
        SELECT 
            c.CursusID,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            cat.Naam AS CategorieNaam
        FROM Cursus c
        LEFT JOIN Categorie cat ON c.CategorieID = cat.CategorieID
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
        $sql = "SELECT 
                    c.CursusID,
                    c.Titel,
                    c.CategorieID,
                    d.KorteBeschrijving,
                    d.Beschrijving,
                    d.LaatstBijgewerkt,
                    d.Rating,
                    d.Taal,
                    d.Prijs
                FROM Cursus AS c
                JOIN Cursusdetails AS d ON c.CursusID = d.CursusID
                WHERE c.CursusID = ?";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $courseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }


}
?>
