<?php

/**
 * Class Notificatie
 *
 * Dit model beheert alle database-acties met betrekking tot notificaties.
 * Het bevat functionaliteit om notificaties op te halen, te tellen,
 * te wijzigen en nieuwe notificaties aan te maken.
 */
class Notificatie
{
    /** @var mysqli Database connectie (MySQLi) */
    private $conn;

    /**
     * Constructor
     *
     * @param mysqli $db Actieve databaseconnectie (MySQLi)
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Haalt de (maximaal 20) meest recente notificaties op voor een gebruiker.
     *
     * @param string|int $userId Het ID van de gebruiker
     * @return array Associatieve array met notificatiegegevens
     */
    public function getNotificationsForUser($userId)
    {
        $sql = "SELECT * FROM notificaties 
                WHERE user_id = ?
                ORDER BY aangemaakt_op DESC
                LIMIT 20";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Geeft het aantal ongelezen notificaties terug.
     *
     * @param string|int $userId Het ID van de gebruiker
     * @return int Aantal ongelezen notificaties
     */
    public function getUnreadCount($userId)
    {
        $sql = "SELECT COUNT(*) FROM notificaties 
                WHERE user_id = ? AND status = 'unread'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_row()[0];
    }

    /**
     * Markeert alle notificaties van een gebruiker als gelezen.
     *
     * @param string|int $userId Het ID van de gebruiker
     * @return bool True als het succesvol is uitgevoerd
     */
    public function markAllAsRead($userId)
    {
        $sql = "UPDATE notificaties 
                SET status = 'read'
                WHERE user_id = ? AND status = 'unread'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        return $stmt->execute();
    }

    /**
     * Maakt een nieuwe notificatie aan voor een gebruiker.
     *
     * @param string|int $userId Het ID van de gebruiker
     * @param string $message De inhoud van de notificatie
     * @return bool True als de notificatie succesvol is toegevoegd
     *
     * @note Vereist dat de functie generateUUID() beschikbaar is in de toepassing.
     */
    public function createNotification($userId, $message)
    {
        $id = generateUUID();

        $sql = "INSERT INTO notificaties (id, user_id, boodschap)
                VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $id, $userId, $message);

        return $stmt->execute();
    }
}
