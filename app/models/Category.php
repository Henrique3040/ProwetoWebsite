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
        $sql = "SELECT CategorieID, Naam, Icon, CreatedAt, UpdatedAt FROM Categorie ORDER BY Naam ASC";
        $result = mysqli_query($this->conn, $sql);

        $categories = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }
        }
        return $categories;
    
    }
     
     // Get all courses belonging to a category
    public function getCoursesByCategory($categoryId)
    {
        $sql = "
        SELECT c.CursusID, c.Titel, c.FotoURL, c.Link, c.Views
        FROM Cursus c
        INNER JOIN CursusCategorie cc ON c.CursusID = cc.CursusID
        WHERE cc.CategorieID = ?
        ORDER BY c.Views DESC
        ";        
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
        $categoryQuery = "SELECT CategorieID, Naam, Icon, CreatedAt, UpdatedAt FROM Categorie ORDER BY Naam ASC";
        $categoryResult = mysqli_query($this->conn, $categoryQuery);

        while ($cat = mysqli_fetch_assoc($categoryResult)) {
            $catId = (int)$cat['CategorieID'];
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
            COUNT(cc.CursusID) AS TotalCourses
        FROM Categorie c
        LEFT JOIN CursusCategorie cc ON c.CategorieID = cc.CategorieID
        GROUP BY c.CategorieID, c.Naam
        ORDER BY c.Naam ASC
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

    public function createCategorie($naam, $icon)
    {
        $sql = "INSERT INTO Categorie (Naam, Icon) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $naam, $icon);
        return mysqli_stmt_execute($stmt);
    }

    public function updateCategorie($id, $naam, $icon)
    {
        $sql = "UPDATE Categorie SET Naam = ?, Icon = ? WHERE CategorieID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $naam, $icon, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function deleteCategorie($id)
    {
        $sql = "DELETE FROM Categorie WHERE CategorieID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

}
?>
