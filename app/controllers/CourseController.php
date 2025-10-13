<?php
require_once __DIR__ . '/../models/Course.php';

class CourseController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Course($db);
    }

    public function featured($limit = 8)
    {
        return $this->model->getFeaturedCourses($limit);
    }

    public function getCourseDetail($courseId)
    {
        return $this->model->getCourseDetail($courseId);
    }

    
}
?>
