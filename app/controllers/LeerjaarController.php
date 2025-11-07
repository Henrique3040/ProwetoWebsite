<?php
require_once __DIR__ . '/../models/Leerjaar.php';

Class LeerjaarController{
    private $model;

    public function __construct($db)
    {
        $this->model = new Leerjaar($db);
    }

    public function getAllLeerjaren()
    {
        return $this->model->getAllLeerjaren();
    }
}