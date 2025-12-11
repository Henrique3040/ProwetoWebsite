<?php
require_once __DIR__ . '/../models/Notificatie.php';

/**
 * Class NotificatieController
 *
 * Deze controller handelt alle notificatie-gerelateerde acties af.
 * Hij communiceert met het Notificatie-model om data op te halen,
 * tellen of aan te passen.
 */
class NotificatieController
{
    /** @var Notificatie Het model dat database-interacties uitvoert */
    private $model;

    /**
     * Constructor
     *
     * @param PDO $db De actieve database-connectie
     */
    public function __construct($db)
    {
        $this->model = new Notificatie($db);
    }

    /**
     * Haalt alle notificaties op van een specifieke gebruiker.
     *
     * @param int $userId Het ID van de gebruiker
     * @return array Een array met notificaties
     */
    public function getUserNotifications($userId)
    {
        return $this->model->getNotificationsForUser($userId);
    }

    /**
     * Geeft het aantal ongelezen notificaties terug.
     *
     * @param int $userId Het ID van de gebruiker
     * @return int Aantal ongelezen notificaties
     */
    public function getUnreadCount($userId)
    {
        return $this->model->getUnreadCount($userId);
    }

    /**
     * Markeer alle notificaties van een gebruiker als gelezen.
     *
     * @param int $userId Het ID van de gebruiker
     * @return bool True als de actie succesvol was
     */
    public function markAllAsRead($userId)
    {
        return $this->model->markAllAsRead($userId);
    }

    /**
     * Voeg een nieuwe notificatie toe voor een gebruiker.
     *
     * @param int $userId Het ID van de gebruiker
     * @param string $message De inhoud van de notificatie
     * @return bool True als de notificatie succesvol is aangemaakt
     */
    public function addNotification($userId, $message)
    {
        return $this->model->createNotification($userId, $message);
    }

    public function deleteAllNotification($userId){
        return $this->model->deleteAllNotification($userId);
    }
}
