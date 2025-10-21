<?php
require_once __DIR__ . '/../models/SubWebsite.php';

class SubWebsiteController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new SubWebsite($db);
    }

    public function index()
    {
        return $this->model->getAll();
    }
}
?>
