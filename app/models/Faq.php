<?php
include_once __DIR__ . '/../helpers/generateUUID.php';
class Faq
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Alle vragen ophalen voor één cursus
    public function getFaqsByCourse($cursusId)
    {
        $sql = "SELECT * FROM CursusFAQ WHERE cursus_id = ? ORDER BY CreatedAt DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $cursusId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $faqs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $faqs[] = $row;
        }
        return $faqs;
    }

    // Nieuwe FAQ toevoegen
    public function createFaq($cursusId, $vraag, $antwoord)
    {
        $uuid = generateUUID();
        $sql = "INSERT INTO CursusFAQ (Id, CursusID, Vraag, Antwoord) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($this->conn));
        }

        mysqli_stmt_bind_param($stmt, "ssss", $uuid, $cursusId, $vraag, $antwoord);
        return mysqli_stmt_execute($stmt);
    }

    // FAQ verwijderen
    public function deleteFaq($faqId)
    {
        $sql = "DELETE FROM CursusFAQ WHERE ID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $faqId);
        return mysqli_stmt_execute($stmt);
    }
}
?>