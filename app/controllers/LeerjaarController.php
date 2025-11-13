<?php
/**
 * LeerjaarController
 *
 * Deze controller beheert de logica met betrekking tot leerjaren (schooljaren).
 * Hij communiceert met het Leerjaar-model om gegevens uit de database op te halen.
 *
 * @author  
 * @version 1.0
 */

require_once __DIR__ . '/../models/Leerjaar.php';

Class LeerjaarController{

    /**
     * @var Leerjaar Het model dat database-interacties voor leerjaren beheert.
     */
    private $model;

     /**
     * Constructor
     *
     * Initialiseert de controller en laadt het Leerjaar-model met de databaseverbinding.
     *
     * @param mysqli $db De actieve databaseverbinding.
     */
    public function __construct($db)
    {
        $this->model = new Leerjaar($db);
    }

    /**
     * Haalt alle leerjaren op uit de database.
     *
     * @return array Associatieve array met leerjaren.
     */
    public function getAllLeerjaren()
    {
        return $this->model->getAllLeerjaren();
    }
}