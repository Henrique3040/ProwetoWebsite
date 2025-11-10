<?php
include_once __DIR__ . '/../helpers/generateUUID.php';

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Maak een nieuwe admin user
    public function createUser($username, $password)
    {
        $id = generateUUID();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (id, username, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $id, $username, $hashedPassword);

        return mysqli_stmt_execute($stmt);
    }

    // Haal user op via username
    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // Login controle
    public function verifyLogin($username, $password)
    {
        $user = $this->getUserByUsername($username);
        

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}
?>