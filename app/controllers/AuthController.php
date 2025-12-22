<?php

/**
 * Class AuthController
 *
 * Deze controller beheert authenticatie- en autorisatiecontroles,
 * gebaseerd op informatie opgeslagen in de PHP-session.
 *
 * Functies omvatten:
 *  - Controleren of een gebruiker is ingelogd
 *  - Controleren of een gebruiker adminrechten heeft
 *  - Gebruikers verplichten in te loggen om een pagina te bezoeken
 *  - Admin-restricties afdwingen
 */
class AuthController
{
    /**
     * Controleert of er een gebruiker is ingelogd.
     *
     * @return bool True als een gebruiker is ingelogd, anders false
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

     /**
     * Controleert of de ingelogde gebruiker adminrechten heeft.
     *
     * @return bool True als gebruiker admin is, anders false
     */
    public static function isAdmin(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['admin'] === true;
    }

    /**
     * Verplicht dat een gebruiker moet zijn ingelogd.
     * Zo niet, wordt hij doorgestuurd naar de login-pagina.
     *
     * @return void
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header("Location: /sign-in.php");
            exit;
        }
    }

     /**
     * Verplicht dat de gebruiker adminrechten heeft.
     * Zo niet, wordt hij doorgestuurd naar een 404/verboden-pagina.
     *
     * @return void
     */
    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            header("Location: /error-404.php");
            exit;
        }
    }


}
