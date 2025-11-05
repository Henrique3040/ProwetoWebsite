<?php
require_once __DIR__ . '/../app/core/init.php';

// Maak nieuwe admin user
$username = 'admin2';
$password = 'admin321';

if ($userController->register($username, $password)) {
    echo "Admin user '$username' succesvol aangemaakt!";
} else {
    echo "Er is iets misgegaan bij het aanmaken van de admin user.";
}
