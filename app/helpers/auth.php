<?php
/**
 * Controleert of de huidige gebruiker is ingelogd als admin.
 *
 * Deze functie wordt gebruikt om te voorkomen dat niet-ingelogde gebruikers
 * toegang krijgen tot adminpagina's.  
 * Als de gebruiker niet is ingelogd, wordt hij automatisch doorgestuurd
 * naar de foutpagina (`/error-404.php`).
 *
 * Gebruik:
 * ```php
 * session_start();
 * require_once 'path/to/auth.php';
 * requireAdmin(); // Roept deze functie aan bovenaan een beveiligde pagina
 * ```
 *
 * @return void
 */

function requireAdmin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: /error-404.php");
        exit;
    }
}
