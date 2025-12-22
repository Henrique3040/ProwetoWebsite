<?php
/**
 * Endpoint: E-mailnotificatie-instelling van een gebruiker aanpassen
 *
 * Functie:
 *  - Controleert of de gebruiker is ingelogd
 *  - Leest POST-data (user_id + checkboxwaarde)
 *  - Past de e-mailnotificatie-instelling aan via UserController
 *  - Stuurt de gebruiker terug naar de vorige pagina
 *
 * POST-velden:
 *  - user_id (string|int, verplicht voor update)
 *  - value   (checkbox → wordt alleen meegestuurd als hij aangevinkt is)
 */
require_once "../../app/core/init.php";

// Gebruiker moet ingelogd zijn
if (!AuthController::isLoggedIn()) {
    header("Location: /sign-in.php");
    exit;
}

// Ophalen van POST-gegevens
$userId = $_POST['user_id'] ?? null;

// Checkbox: als hij aangevinkt is = 1, anders 0
$value = isset($_POST['value']) ? 1 : 0;

/**
 * @var UserController $userController
 * Wordt geïnitialiseerd in init.php
 */

// Alleen uitvoeren als user_id aanwezig is
if ($userId) {
    $userController->setEmailNotification($userId, $value);
}

// Redirect terug naar de pagina waar de gebruiker vandaan kwam
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
