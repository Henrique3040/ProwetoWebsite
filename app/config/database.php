<?php
/**
 * Database Class
 *
 * Deze klasse beheert een MySQL-databaseverbinding met behulp van het Singleton-patroon.
 * Dit zorgt ervoor dat er maar één instantie van de databaseverbinding tegelijk bestaat.
 *
 * @author  
 * @version 1.0
 */
class Database
{
    /**
    * De enkele instantie van de Database (Singleton)
    * @var Database|null
    */
   private static $instance = null;
   /**
    * De actieve MySQLi-verbinding
    * @var mysqli
    */

   private $connection;
   /**
    * Database hostnaam
    * @var string
    */


   /**
    * Constructor
    *
    * De constructor is privé zodat de klasse niet rechtstreeks geïnstantieerd kan worden.
    * Hier wordt de verbinding met de MySQL-database tot stand gebracht.
    *
    * @throws Exception Als de verbinding met de database mislukt.
    */
   private function __construct()
   {
      $this->connection = new mysqli(
         $_ENV['DB_HOST'],
         $_ENV['DB_USER'],
         $_ENV['DB_PASS'],
         $_ENV['DB_NAME']

      );

      if ($this->connection->connect_error) {
         die("Connection failed: " . $this->connection->connect_error);
      }
   }

    /**
    * Haalt de enkele instantie van de Database op (Singleton)
    *
    * Als er nog geen instantie bestaat, wordt er één aangemaakt.
    *
    * @return Database De enkele instantie van de Database.
    */
    public static function getInstance()
   {
      if (self::$instance == null) {
         self::$instance = new Database();
      }
      return self::$instance;
   }

    /**
    * Haalt de actieve MySQLi-verbinding op
    *
    * @return mysqli De MySQLi-databaseverbinding.
    */
   public function getConnection()
   {
      return $this->connection;
   }
}

?>