<?php
/**
 * Category
 *
 * Modelklasse voor het beheren van categorieën en hun relatie met cursussen.
 * Communiceert direct met de database via mysqli.
 *
 * Functionaliteiten:
 * - Ophalen van categorieën
 * - Ophalen van cursussen per categorie
 * - CRUD-acties voor categorieën
 *
 * @author  
 * @version 1.0
 */

include_once __DIR__ . '/../helpers/generateUUID.php';
class Category
{
    /**
     * @var mysqli $conn De actieve databaseverbinding.
     */
    private $conn;

    /**
     * Constructor
     *
     * @param mysqli $db De databaseverbinding.
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Haalt alle categorieën op uit de database.
     *
     * @return array Associatieve array met categorieën.
     */
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

    /**
     * Haalt alle cursussen op die gekoppeld zijn aan een bepaalde categorie.
     *
     * @param int $categoryId Het ID van de categorie.
     * @return array Lijst van cursussen binnen de categorie.
     */
    public function getCoursesByCategory($categoryId)
    {
        $sql = "
        SELECT 
            c.Id,
            c.Titel,
            c.FotoURL,
            c.Link,
            c.Views,
            d.Rating
        FROM Cursus c
        JOIN Cursusdetails d ON c.id = d.cursus_id
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

    /**
     * Haalt een beperkt aantal categorieën op, inclusief het aantal gekoppelde cursussen.
     *
     * @param int $limit Het maximum aantal categorieën.
     * @return array Associatieve array van categorieën met course count.
     */
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



    /**
     * Haalt alle categorieën op met het aantal gekoppelde cursussen.
     *
     * @return mysqli_result Resultaatset van de query.
     */
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


    /**
     * Haalt categorieën op die gekoppeld zijn aan een specifieke cursus.
     *
     * @param int $courseId Het ID van de cursus.
     * @return array Lijst van categorieën voor de cursus.
     */
    public function getCategoriesByCourse($courseId)
    {
        $sql = "
        SELECT cat.Id, cat.Naam
        FROM Categorie cat
        INNER JOIN CursusCategorie cc ON cat.Id = cc.categorie_id
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

    /**
     * Maakt een nieuwe categorie aan.
     *
     * @param string $naam De naam van de categorie.
     * @param string $icon Het icon voor de categorie.
     * @return bool True als succesvol, anders false.
     */
    public function createCategorie($naam, $icon)
    {
        $uuid = generateUUID();
        $sql = "INSERT INTO Categorie (Id, Naam, Icon) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $uuid , $naam, $icon);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Werkt een bestaande categorie bij.
     *
     * @param int $id Het ID van de categorie.
     * @param string $naam Nieuwe naam.
     * @param string $icon Nieuwe icon.
     * @return bool True als succesvol, anders false.
     */
    public function updateCategorie($id, $naam, $icon)
    {
        $sql = "UPDATE Categorie SET Naam = ?, Icon = ? WHERE Id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $naam, $icon, $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Verwijdert een categorie uit de database.
     *
     * @param int $id Het ID van de categorie.
     * @return bool True als succesvol, anders false.
     */
    public function deleteCategorie($id)
    {
        $sql = "DELETE FROM Categorie WHERE Id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

}
?>