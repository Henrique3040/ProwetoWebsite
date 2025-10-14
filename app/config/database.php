<?php
class Database
{
   private static $instance = null;
   private $connection;

   private $host = "localhost";
   private $username = "root";
   private $password = "";
   private $database = "proweto";

   // Private constructor prevents direct object creation
   private function __construct()
   {
      $this->connection = new mysqli(
         $this->host,
         $this->username,
         $this->password,
         $this->database
      );

      if ($this->connection->connect_error) {
         die("Connection failed: " . $this->connection->connect_error);
      }
   }

   // Get the singleton instance
   public static function getInstance()
   {
      if (self::$instance == null) {
         self::$instance = new Database();
      }
      return self::$instance;
   }

   // Get the connection
   public function getConnection()
   {
      return $this->connection;
   }
}



?>