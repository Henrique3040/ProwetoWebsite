<?php
/**
 * UserController
 *
 * Deze controller beheert de gebruikersauthenticatie van het beheerdersgedeelte.
 * Hij communiceert met het User-model voor login, registratie en sessiebeheer.
 *
 * Functionaliteiten:
 * - Inloggen en authenticatie
 * - Uitloggen
 * - Registratie van nieuwe gebruikers (optioneel)
 * - Controleren of een gebruiker is ingelogd
 *
 * @author  
 * @version 1.0
 */

require_once __DIR__ . '/../models/User.php';

class UserController
{
    /**
     * @var User $model Het model dat database-interacties voor gebruikers beheert.
     */
    private $model;

    /**
     * Constructor
     *
     * Initialiseert de controller met een databaseverbinding.
     *
     * @param mysqli $db De actieve databaseverbinding.
     */
    public function __construct($db)
    {
        $this->model = new User($db);
    }

    /**
     * Registreert een nieuwe gebruiker (optioneel).
     *
     * @param string $username De gebruikersnaam van de nieuwe gebruiker.
     * @param string $password Het wachtwoord van de nieuwe gebruiker (wordt gehasht in het model).
     * @return bool True als registratie is gelukt, anders false.
     */
    public function register($email, $username, $password)
    {
        return $this->model->createUser($email,$username, $password);
    }

    /**
     * Probeert een gebruiker in te loggen met de opgegeven inloggegevens.
     *
     * Als de login geldig is, wordt een sessie gestart met:
     *  - `admin_logged_in` = true  
     *  - `admin_id` = ID van de gebruiker  
     *  - `username` = gebruikersnaam  
     *
     * @param string $username De ingevoerde gebruikersnaam.
     * @param string $password Het ingevoerde wachtwoord.
     * @return bool True als de inlog succesvol is, anders false.
     */
    public function login($username, $password)
    {
        $user = $this->model->getUserByUsername($username);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Sessie vullen met user info
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'admin' => intval($user['Admin']) === 1,
            'created' => $user['CreatedAt'],
        ];

        return true;
    }

    public function getAllAdmins (){
        $result = $this->model->getAllAdmins();
        return $result;
    }

    public function getUserById($userId){
        $result = $this->model->getUserById($userId);
        return $result;
    }

    public function setEmailNotification($userId, $value) {
        return $this->model->updateEmailNotificationSetting($userId, $value);
    }

    public function getEmailNotification($userId) {
        return $this->model->getEmailNotificationSetting($userId);
    }


    /**
     * Logt de huidige gebruiker uit.
     *
     * Vernietigt de actieve sessie en stuurt de gebruiker terug naar de inlogpagina.
     *
     * @return void
     */
    public function logout()
    {
        session_unset(); // Verwijder alle sessievariabelen
        session_destroy(); // Vernietig de sessie
        header("Location: /sign-in.php");
        exit;
    }

}
?>