<?php
/**
 * Markeer alle notificaties als gelezen voor de huidige gebruiker.
 *
 * Dit script:
 *  1. Laadt de applicatieconfiguratie (init.php)
 *  2. Controleert of een gebruiker is ingelogd
 *  3. Markeert alle notificaties van die gebruiker als 'read'
 *  4. Stuurt de gebruiker terug naar de vorige pagina
 */
require_once "../../app/core/init.php";

// Abrupte exit als er geen gebruiker is ingelogd
if (!isset($_SESSION['user'])) exit;

// Huidige gebruiker-ID ophalen
$userId = $_SESSION['user']['id'];

/** @var NotificatieController $notificatieController 
 *  Wordt aangemaakt in init.php
 */

$notificatieController->markAllAsRead($userId);

// Redirect terug naar de pagina waar de gebruiker vandaan kwam
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
