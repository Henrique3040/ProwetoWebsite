<?php
class AuthController
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['admin'] === true;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header("Location: /sign-in.php");
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            header("Location: /error-404.php");
            exit;
        }
    }


}
