<?php
include_once __DIR__ . '/../helpers/generateUUID.php';

/**
 * User
 *
 * Modelklasse voor het beheren van admin users.
 */
class User
{
     /**
     * @var mysqli $conn Databaseverbinding
     */
    private $conn;

    /**
     * Constructor
     *
     * @param mysqli $db Databaseverbinding
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Maak een nieuwe admin user aan
     *
     * @param string $username Gebruikersnaam
     * @param string $password Wachtwoord
     * @return bool True bij succes, false bij falen
     */
    public function createUser($email, $username, $password)
    {
        $id = generateUUID();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (id, username, email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $id, $username, $email, $hashedPassword);

        return mysqli_stmt_execute($stmt);
    }

    public function getAllAdmins (){
        $sql = "SELECT id, username, email FROM users WHERE admin = 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getUserById($userId)
    {
        $sql = "SELECT * FROM users WHERE Id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Haal een user op via gebruikersnaam
     *
     * @param string $username Gebruikersnaam
     * @return array|null Associatieve array van user of null als niet gevonden
     */
    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Controleer login credentials
     *
     * @param string $username Gebruikersnaam
     * @param string $password Wachtwoord
     * @return array|false User array bij correct, false bij fout
     */
    public function verifyLogin($username, $password)
    {
        $user = $this->getUserByUsername($username);
        

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function getEmailNotificationSetting($userId) {
        $sql = "SELECT email_notifications FROM users WHERE id=?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['email_notifications'] ?? 1;
    }

    public function updateEmailNotificationSetting($userId, $value) {
        $sql = "UPDATE users SET email_notifications=? WHERE id=?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $value, $userId);
        return mysqli_stmt_execute($stmt);
    }
}
?>