<?php

/**
 * Leerjaar
 *
 * Modelklasse voor het ophalen van leerjaren uit de database.
 */
Class Leerjaar{
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
     * Haal alle leerjaren op, gesorteerd op naam.
     *
     * @return array Associatieve array van leerjaren
     */
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