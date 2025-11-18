<?php

class Material
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /* ---------------------------------------------------
        GET ALL MATERIALS
    --------------------------------------------------- */
    public function getAll()
    {
        $sql = "SELECT Id, Naam, FotoURL, CreatedAt, UpdatedAt 
                FROM Materialen 
                ORDER BY CreatedAt DESC";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /* ---------------------------------------------------
        CREATE MATERIAL
    --------------------------------------------------- */
    public function create($naam, $fotoPath)
    {
        $id = generateUUID();

        $sql = "INSERT INTO Materialen (Id, Naam, FotoURL) 
                VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $id, $naam, $fotoPath);

        return mysqli_stmt_execute($stmt);
    }

    /* ---------------------------------------------------
        GET SINGLE MATERIAL
    --------------------------------------------------- */
    public function getById($id)
    {
        $sql = "SELECT * FROM Materialen WHERE Id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    /* ---------------------------------------------------
        UPDATE MATERIAL
    --------------------------------------------------- */
    public function update($id, $naam, $fotoPath = null)
    {
        if ($fotoPath === null) {
            // Naam alleen wijzigen
            $sql = "UPDATE Materialen SET Naam = ?, UpdatedAt = NOW() WHERE Id = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $naam, $id);
        } else {
            // Foto vervangen
            $old = $this->getById($id);
            if ($old && file_exists($old['FotoURL'])) {
                unlink($old['FotoURL']);
            }

            $sql = "UPDATE Materialen SET Naam = ?, FotoURL = ?, UpdatedAt = NOW() WHERE Id = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $naam, $fotoPath, $id);
        }

        return mysqli_stmt_execute($stmt);
    }

    /* ---------------------------------------------------
        DELETE MATERIAL
    --------------------------------------------------- */
    public function delete($id)
    {
        $material = $this->getById($id);

        if ($material) {
            // Delete photo from server
            if (file_exists($material['FotoURL'])) {
                unlink($material['FotoURL']);
            }

            $sql = "DELETE FROM Materialen WHERE Id = ?";
            $stmt = mysqli_prepare($this->conn, $sql);

            mysqli_stmt_bind_param($stmt, "s", $id);
            return mysqli_stmt_execute($stmt);
        }

        return false;
    }
}
