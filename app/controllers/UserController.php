<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new User($db);
    }

    // Registratie (optioneel)
    public function register($username, $password)
    {
        return $this->model->createUser($username, $password);
    }

    // Login handler
    public function login($username, $password)
    {
        $user = $this->model->verifyLogin($username, $password);

        if ($user) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }

        return false;
    }

    // Logout handler
    public function logout()
    {
        session_unset(); // Verwijder alle sessievariabelen
        session_destroy(); // Vernietig de sessie
        header("Location: sign-in.php");
        exit;
    }

    // Check login
    public function isLoggedIn()
    {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
}
?>
