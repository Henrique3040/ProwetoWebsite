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

    public function getAllCategories()
    {
        return $this->model->getAll();
    }
    
    public function getAllWithCourseCount()
    {
        $categories = [];
        $categoryResult = $this->model->getAll();

        while ($cat = mysqli_fetch_assoc($categoryResult)) {
            $cat['TotalCourses'] = $this->model->getCategoryCourseCount($cat['CategorieID']);
            $categories[] = $cat;
        }

        return $categories;
    }


    public function getCategoriesByCourse($courseId)
    {
        return $this->model->getCategoriesByCourse($courseId);
    }
}
?>