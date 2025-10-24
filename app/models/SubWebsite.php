<?php
class SubWebsite
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all sub-websites
    public function getAll()
    {
        $sql = "SELECT SubWebsiteID, Title, Link, Icon, CreatedAt FROM SubWebsite ORDER BY Title ASC";
        $result = mysqli_query($this->conn, $sql);
        return $result ?: [];
    }

    // Update a sub-website
    public function update($id, $title, $link, $icon)
    {
        $sql = "UPDATE SubWebsite SET Title = ?, Link = ?, Icon = ? WHERE SubWebsiteID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $title, $link, $icon, $id);
        return mysqli_stmt_execute($stmt);
    }
    // Create a new sub-website
    public function create($title, $link, $icon)
    {
        $sql = "INSERT INTO SubWebsite (Title, Link, Icon) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $title, $link, $icon);
        return mysqli_stmt_execute($stmt);
    }

    // Delete a sub-website
    public function delete($id)
    {
        $sql = "DELETE FROM SubWebsite WHERE SubWebsiteID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

}
?>
