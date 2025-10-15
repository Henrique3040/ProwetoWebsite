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
        $sql = "SELECT SubWebsiteID, Title, Link, Icon FROM SubWebsite ORDER BY Title ASC";
        $result = mysqli_query($this->conn, $sql);
        return $result ?: [];
    }

}
?>
