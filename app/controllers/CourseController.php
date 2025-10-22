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

    public function searchCourses($query)
    {
        return $this->model->searchCourses($query);
    }

    public function getAllCourses()
    {
        return $this->model->getAllCourses();
    }

    //Slaag cursus op de database via de model
    public function store()
    {


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = $_POST['titel'];
            $korteBeschrijving = $_POST['korte_beschrijving'];
            $beschrijving = $_POST['beschrijving'];
            $categorieID = $_POST['categorie_id'];
            $videoLink = $_POST['video_link'];

            // Upload foto
            $fotoURL = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/courses/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0755, true);

                $filename = time() . '_' . basename($_FILES['foto']['name']);
                $targetFile = $uploadDir . $filename;
                move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile);
                $fotoURL = $targetFile;
            }
            $faqs = isset($_POST['faqs']) ? json_decode(($_POST['faqs']), true) : [];

            // Data opslaan via model
            $cursusId = $this->model->createCourse([
                'Titel' => $titel,
                'KorteBeschrijving' => $korteBeschrijving,
                'Beschrijving' => $beschrijving,
                'CategorieID' => $categorieID,
                'FotoURL' => $fotoURL,
                'Link' => $videoLink,
                'faqs' => $faqs
            ]);

            if ($cursusId) {
                header('Location: admin-create-course.php?controller=course&action=success');
                exit;
            } else {
                echo "<p style='color:red;'>Er is iets misgegaan bij het opslaan van de cursus.</p>";
            }
        }
    }

    public function delete($courseId)
    {
        return $this->model->deleteCourse($courseId);
    }


    public function update($courseId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = $_POST['titel'] ?? '';
            $korteBeschrijving = $_POST['korte_beschrijving'] ?? '';
            $beschrijving = $_POST['beschrijving'] ?? '';
            $categorieID = $_POST['categorie_id'] ?? null;
            $videoLink = $_POST['video_link'] ?? '';
            $leerjaarId = $_POST['leerjaar_id'] ?? null;

            // Booleans
            $materiaal = isset($_POST['materiaal']) ? 1 : 0;
            $documenten = isset($_POST['documenten']) ? 1 : 0;
            $active = isset($_POST['active']) ? 1 : 0;

            // Upload nieuwe foto (optioneel)
            $fotoURL = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/courses/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0755, true);

                $filename = time() . '_' . basename($_FILES['foto']['name']);
                $targetFile = $uploadDir . $filename;
                move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile);
                $fotoURL = $targetFile;
            }

            // FAQ’s (optioneel, als JSON)
            $faqs = isset($_POST['faqs']) ? json_decode($_POST['faqs'], true) : [];

            // Update via model
            $updated = $this->model->updateCourse($courseId, [
                'Titel' => $titel,
                'KorteBeschrijving' => $korteBeschrijving,
                'Beschrijving' => $beschrijving,
                'CategorieID' => $categorieID,
                'FotoURL' => $fotoURL,
                'Link' => $videoLink,
                'Active' => $active,
                'Materiaal' => $materiaal,
                'Documenten' => $documenten,
                'LeerJaarID' => $leerjaarId,
                'Faqs' => $faqs
            ]);

            if ($updated) {
                header("Location: admin-course-list.php?success=1");
                exit;
            } else {
                echo "<p style='color:red;'>Er is iets misgegaan bij het bijwerken van de cursus.</p>";
            }
        }
    }



}
?>