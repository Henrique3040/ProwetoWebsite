<?php

use LDAP\Result;

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

    public function reserve($user_id, $materialId, $cursusId, $date)
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
             (Id, materiaal_id, cursus_id ,user_id, startdatum, einddatum, starttijd, eindtijd)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $insert->bind_param(
            "ssssssss",
            $id,
            $materialId,
            $cursusId,
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

    public function getAllReservations()
    {

        $sql = " SELECT 
                r.Id,
                r.startdatum,
                r.einddatum,
                r.starttijd,
                r.eindtijd,
                r.status,
                r.aangemaakt_op,
                u.username,
                m.Naam AS materiaal_naam,
                c.Titel AS cursus_titel
            FROM materiaal_reservaties r
            JOIN users u ON r.user_id = u.id
            JOIN materialen m ON r.materiaal_id = m.Id
            JOIN cursus c ON r.cursus_id = c.Id
            ORDER BY r.aangemaakt_op DESC";


        $stmt = mysqli_prepare($this->conn, $sql);

        // Debug: toon SQL fout
        if (!$stmt) {
            die("SQL Error: " . mysqli_error($this->conn));
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $reservaties = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reservaties[] = $row;
        }

        return $reservaties;
    }


    public function deleteReservation($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM materiaal_reservaties WHERE Id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function updateReservationStatus($id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE materiaal_reservaties SET status = ? WHERE Id = ?");
        $stmt->bind_param("ss", $status, $id);
        return $stmt->execute();
    }

    public function getReservationById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM materiaal_reservaties WHERE Id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    // Haal alle reservaties voor 1 gebruiker
    public function getReservationsByUser(string $userId): array
    {
        $sql = "
           SELECT r.Id, r.materiaal_id, r.startdatum, r.einddatum, r.starttijd, r.eindtijd, r.status, r.aangemaakt_op,
                  m.Naam AS materiaal_naam,
                  c.Titel AS cursus_titel
           FROM materiaal_reservaties r
           JOIN Materialen m ON r.materiaal_id = m.Id
           JOIN Cursus c ON c.Id = r.cursus_id   -- Verbind enkel met de cursus waar de reservatie voor is
           WHERE r.user_id = ?
           ORDER BY r.aangemaakt_op DESC
           ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Verwijdert een reservatie door de user zelf, alleen als status toegestaan.
     * Retourneert array met success boolean en optional reason.
     */
    public function deleteReservationByUser(string $reservationId, string $userId): array
    {
        // Eerst status ophalen en user_id checken
        $stmt = $this->conn->prepare("SELECT status, user_id FROM materiaal_reservaties WHERE Id = ?");
        $stmt->bind_param("s", $reservationId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            return ['success' => false, 'reason' => 'NOT_FOUND'];
        }

        // Controleer eigenaar
        if ($row['user_id'] !== $userId) {
            return ['success' => false, 'reason' => 'NOT_OWNER'];
        }

        // Alleen verwijderen als status toegestaan
        $allowed = ['goedgekeurd', 'afgerond'];
        if (!in_array($row['status'], $allowed, true)) {
            return ['success' => false, 'reason' => 'STATUS_NOT_ALLOWED'];
        }

        // Verwijderen
        $del = $this->conn->prepare("DELETE FROM materiaal_reservaties WHERE Id = ?");
        $del->bind_param("s", $reservationId);
        $ok = $del->execute();

        return ['success' => (bool) $ok];
    }


}
