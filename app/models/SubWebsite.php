<?php
include_once __DIR__ . '/../helpers/generateUUID.php';

/**
 * SubWebsite
 *
 * Modelklasse voor het beheren van sub-websites in de database.
 */
class SubWebsite
{
    /**
     * @var mysqli $conn Databaseverbinding
     */
    private $conn;

    /**
     * Constructor
     *
     * @param mysqli $db Databaseverbinding
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Haal alle sub-websites op, gesorteerd op titel.
     *
     * @return array Associatieve array van sub-websites
     */
    public function getAll()
    {
        $sql = "SELECT Id, Title, Link, Icon, CreatedAt FROM SubWebsite ORDER BY Title ASC";
        $result = mysqli_query($this->conn, $sql);
        return $result ?: [];
    }

    /**
     * Update een bestaande sub-website.
     *
     * @param int $id ID van de sub-website
     * @param string $title Titel van de sub-website
     * @param string $link Link van de sub-website
     * @param string $icon Icon van de sub-website
     * @return bool True bij succes, false bij falen
     */
    public function update($id, $title, $link, $icon)
    {
        $sql = "UPDATE SubWebsite SET Title = ?, Link = ?, Icon = ? WHERE ID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $title, $link, $icon, $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Maak een nieuwe sub-website aan.
     *
     * @param string $title Titel van de sub-website
     * @param string $link Link van de sub-website
     * @param string $icon Icon van de sub-website
     * @return bool True bij succes, false bij falen
     */
    public function create($title, $link, $icon)
    {
        $uuid = generateUUID();
        $sql = "INSERT INTO SubWebsite (Id , Title, Link, Icon) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $uuid , $title, $link, $icon);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Verwijder een sub-website.
     *
     * @param int $id ID van de sub-website
     * @return bool True bij succes, false bij falen
     */
    public function delete($id)
    {
        
        $sql = "DELETE FROM SubWebsite WHERE ID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

}
?>
