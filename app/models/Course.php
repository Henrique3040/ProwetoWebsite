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
            cat.Naam AS CategorieNaam
        FROM Cursus c
        LEFT JOIN Categorie cat ON c.CategorieID = cat.CategorieID
        WHERE c.Titel LIKE CONCAT('%', ?, '%')
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
        $sql = "SELECT 
                    c.CursusID,
                    c.Titel,
                    c.FotoURL,
                    c.Link,
                    c.Views,
                    cat.Naam AS CategorieNaam
                FROM Cursus c
                LEFT JOIN Categorie cat ON c.CategorieID = cat.CategorieID
                ORDER BY c.Titel ASC";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        return $result;
    }
}
?>