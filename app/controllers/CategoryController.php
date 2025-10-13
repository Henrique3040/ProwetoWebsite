<?php
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Category($db);
    }

    public function index()
    {
        return $this->model->getAllWithCourseCount();
    }

    // Get all categories with their courses
    public function getAllWithCourses()
    {
        return $this->model->getAllWithCourses();
    }

    public function getCoursesByCategory($categoryId)
    {
        return $this->model->getCoursesByCategory($categoryId);
    }
}
?>