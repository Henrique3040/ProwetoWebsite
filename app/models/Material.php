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

    public function addAvailability($data)
    {
        $id = generateUUID();

        $sql = "INSERT INTO materiaal_beschikbaarheid (Id, materiaal_id, startdatum, einddatum, starttijd, eindtijd)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        $stmt->bind_param("ssssss", $id, $data['materiaal_id'], $data['startdatum'], $data['einddatum'], $data['starttijd'], $data['eindtijd']);
        $stmt->execute();

        return $id;
    }

    public function reserve($user_id, $materialId, $date)
    {

        $query = $this->conn->prepare(
            "SELECT startdatum, einddatum, starttijd, eindtijd
             FROM materiaal_beschikbaarheid
             WHERE materiaal_id = ?"
        );
        $query->bind_param("s", $materialId);
        $query->execute();
        $availability = $query->get_result()->fetch_assoc();

        if (!$availability) {
            return ["success" => false, "reason" => "NO_AVAILABILITY"];
        }

        $start = $availability['startdatum'];
        $end = $availability['einddatum'];

        // 2. Check of iemand deze periode al volledig gereserveerd heeft
        $check = $this->conn->prepare(
            "SELECT Id FROM materiaal_reservaties
             WHERE materiaal_id = ?
             AND ? BETWEEN startdatum AND einddatum"
        );
        $check->bind_param("ss", $materialId, $date);
        $check->execute();

        if ($check->get_result()->fetch_assoc()) {
            return ["success" => false, "reason" => "ALREADY_RESERVED"];
        }

        // 3. Opslaan
        $id = generateUUID();

        $insert = $this->conn->prepare(
            "INSERT INTO materiaal_reservaties 
             (Id, materiaal_id, user_id, startdatum, einddatum, starttijd, eindtijd)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $insert->bind_param(
            "sssssss",
            $id,
            $materialId,
            $user_id,
            $availability['startdatum'],
            $availability['einddatum'],
            $availability['starttijd'],
            $availability['eindtijd']
        );

        $insert->execute();

        return ["success" => true];

    }

    public function getCourseIdFromMaterial($materialId)
    {
        $stmt = $this->conn->prepare(
            "SELECT cursus_id FROM cursusmaterialen WHERE materiaal_id = ?"
        );
        $stmt->bind_param("s", $materialId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['cursus_id'] ?? null;
    }


    public function getMaterialAvailability($materiaal_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM materiaal_beschikbaarheid WHERE materiaal_id = ?");
        $stmt->bind_param("s", $materiaal_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteAvailability($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM materiaal_beschikbaarheid WHERE Id = ?");
        $stmt->bind_param("s", $id);

        return $stmt->execute();

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
