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

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $naam = $_POST['naam'] ?? '';
            $icon = $_POST['icon'] ?? '';

            if (empty($naam)) {
                echo json_encode(['success' => false, 'message' => 'Naam is verplicht']);
                return;
            }

            $result = $this->model->createCategorie($naam, $icon);
            if ($result) {
                header('Location: admin-course-category.php?success=created');
                exit;
            } else {
                echo "<p style='color:red;'>Er is iets misgegaan bij het aanmaken van de categorie.</p>";
            }
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['categorie_id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Geen ID ontvangen']);
                return;
            }

            $result = $this->model->deleteCategorie($id);
            if ($result) {
                header('Location: admin-course-category.php?success=deleted');
                exit;
            } else {
                echo "<p style='color:red;'>Er is iets misgegaan bij het verwijderen van de categorie.</p>";
            }
        }
    }


    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['categorie_id'] ?? null;
            $naam = $_POST['naam'] ?? '';
            $icon = $_POST['icon'] ?? '';

            if (!$id || empty($naam)) {
                echo json_encode(['success' => false, 'message' => 'Ongeldige data']);
                return;
            }

            $result = $this->model->updateCategorie($id, $naam, $icon);
            if ($result) {
                header('Location: admin-course-category.php?success=updated');
                exit;
            } else {
                echo "<p style='color:red;'>Er is iets misgegaan bij het updaten van de categorie.</p>";
            }
    
        }
    }

}
?>