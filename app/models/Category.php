<?php
class Category
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //get all categories
    public function getAll()
    {
        $sql = "SELECT CategorieID, Naam FROM Categorie ORDER BY Naam ASC";
        $result = mysqli_query($this->conn, $sql);
        return $result ?: [];

    }
     
     // Get all courses belonging to a category
    public function getCoursesByCategory($categoryId)
    {
        $sql = "SELECT CursusID, Titel FROM Cursus WHERE CategorieID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result ?: [];
    }

    // Get all categories and their courses
    public function getAllWithCourses()
    {
        $categories = [];
        $categoryQuery = "SELECT CategorieID, Naam FROM Categorie ORDER BY Naam ASC";
        $categoryResult = mysqli_query($this->conn, $categoryQuery);

        while ($cat = mysqli_fetch_assoc($categoryResult)) {
            $catId = (int) $cat['CategorieID'];
            $courses = $this->getCoursesByCategory($catId);

            $cat['courses'] = [];
            while ($course = mysqli_fetch_assoc($courses)) {
                $cat['courses'][] = $course;
            }

            $categories[] = $cat;
        }

        return $categories;
    }


    

    /* Get all categories with the count of associated courses */
    public function getAllWithCourseCount()
    {
        $sql = "
        SELECT 
            c.CategorieID,
            c.Naam,
            COUNT(cur.CursusID) AS TotalCourses
        FROM Categorie c
        LEFT JOIN Cursus cur ON c.CategorieID = cur.CategorieID
        GROUP BY c.CategorieID, c.Naam
        ORDER BY c.Naam ASC
        ";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conn));
        }

        return $result;
    }
}
?>
