<?php

Class Leerjaar{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllLeerjaren()
    {
        $sql = "SELECT * FROM Leerjaar ORDER BY Naam ASC";
        $result = mysqli_query($this->conn, $sql);

        $leerjaren = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $leerjaren[] = $row;
        }
        return $leerjaren;
    }
}