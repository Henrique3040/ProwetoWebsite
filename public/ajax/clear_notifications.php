<?php
require_once "../../app/core/init.php";

if (!isset($_SESSION['user'])) exit;

$userId = $_SESSION['user']['id'];

$notificatieController->markAllAsRead($userId);

// terug naar vorige pagina
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
