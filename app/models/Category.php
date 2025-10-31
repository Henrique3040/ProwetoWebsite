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
        $sql = "SELECT Id, Naam, Icon, CreatedAt, UpdatedAt FROM Categorie ORDER BY Naam ASC";
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
        SELECT 
            c.Id,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views
        FROM Cursus c
        INNER JOIN CursusCategorie cc ON c.Id = cc.cursus_id
        WHERE cc.categorie_id = ?
        ORDER BY c.Views DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            // Log de exacte SQL-fout en return een lege array zodat frontend niet breekt
            error_log('getCoursesByCategory prepare failed: ' . mysqli_error($this->conn));
            return [];
        }
    
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        if (!$result) {
            error_log('getCoursesByCategory execute/get_result failed: ' . mysqli_error($this->conn));
            return [];
        }
    
        $courses = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $courses[] = $row;
        }
    
        return $courses;
    
    }

    // Get all categories and their courses
    public function getAllWithCourses()
    {
        $categories = [];
        $categoryQuery = "SELECT Id, Naam, Icon, CreatedAt, UpdatedAt FROM Categorie ORDER BY Naam ASC";
        $categoryResult = mysqli_query($this->conn, $categoryQuery);

        while ($cat = mysqli_fetch_assoc($categoryResult)) {
            $catId = (int) $cat['ID'];
            $courses = $this->getCoursesByCategory($catId);

            $cat['courses'] = [];
            while ($course = mysqli_fetch_assoc($courses)) {
                $cat['courses'][] = $course;
            }

            $categories[] = $cat;
        }

        return $categories;
    }

    public function getWithLimit($limit)
    {
        $sql = "SELECT 
            c.id,
            c.Naam,
            c.Icon,
            c.CreatedAt,
            c.UpdatedAt,
            COUNT(cc.cursus_id) AS TotalCourses
         FROM Categorie c
         LEFT JOIN CursusCategorie cc ON c.id = cc.categorie_id
         GROUP BY c.id, c.Naam, c.Icon, c.CreatedAt, c.UpdatedAt
         ORDER BY c.Naam ASC
         LIMIT ?
         ";
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            die('Prepare failed: ' . mysqli_error($this->conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $categories = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }
        }
        return $categories;

    }



    /* Get all categories with the count of associated courses */
    public function getAllWithCourseCount()
    {
        $sql = "
        SELECT 
            c.ID,
            c.Naam,
            c.Icon,
            COUNT(cc.cursus_id) AS TotalCourses
        FROM Categorie c
        LEFT JOIN CursusCategorie cc ON c.ID = cc.categorie_id
        GROUP BY c.ID, c.Naam
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
        SELECT cat.ID, cat.Naam
        FROM Categorie cat
        INNER JOIN CursusCategorie cc ON cat.ID = cc.categorie_id
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

    public function createCategorie($naam, $icon)
    {
        $sql = "INSERT INTO Categorie (Naam, Icon) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $naam, $icon);
        return mysqli_stmt_execute($stmt);
    }

    public function updateCategorie($id, $naam, $icon)
    {
        $sql = "UPDATE Categorie SET Naam = ?, Icon = ? WHERE ID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $naam, $icon, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function deleteCategorie($id)
    {
        $sql = "DELETE FROM Categorie WHERE ID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

}
?>