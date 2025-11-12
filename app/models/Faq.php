<?php
include_once __DIR__ . '/../helpers/generateUUID.php';

/**
 * Faq
 *
 * Modelklasse voor het beheren van FAQ's per cursus.
 */

class Faq
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
     * Haal alle FAQ's op voor een specifieke cursus.
     *
     * @param string $cursusId ID van de cursus
     * @return array Associatieve array van FAQ's
     */
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

     /**
     * Voeg een nieuwe FAQ toe aan een cursus.
     *
     * @param string $cursusId ID van de cursus
     * @param string $vraag De vraag
     * @param string $antwoord Het antwoord
     * @return bool Successtatus van de insert
     */
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

    /**
     * Verwijder een FAQ.
     *
     * @param string $faqId ID van de FAQ
     * @return bool Successtatus van de delete
     */
    public function deleteFaq($faqId)
    {
        $sql = "DELETE FROM CursusFAQ WHERE ID = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $faqId);
        return mysqli_stmt_execute($stmt);
    }
}
?>