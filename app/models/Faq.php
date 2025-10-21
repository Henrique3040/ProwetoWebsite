<?php
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
        $sql = "SELECT * FROM CursusFAQ WHERE CursusID = ? ORDER BY CreatedAt DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $cursusId);
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
        $sql = "INSERT INTO CursusFAQ (CursusID, Vraag, Antwoord) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $cursusId, $vraag, $antwoord);
        return mysqli_stmt_execute($stmt);
    }

    // FAQ verwijderen
    public function deleteFaq($faqId)
    {
        $sql = "DELETE FROM CursusFAQ WHERE FAQID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $faqId);
        return mysqli_stmt_execute($stmt);
    }
}
?>
