<?php
/**
 * CategoryController
 *
 * Deze controller beheert alle logica rondom categorieën in de applicatie.
 * Hij communiceert met het Category-model om data uit de database op te halen
 * of te wijzigen (CRUD-operaties).
 *
 * @author  
 * @version 1.0
 */

require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    /**
     * @var Category Het model dat database-interacties voor categorieën beheert.
     */
    private $model;

    /**
     * Constructor
     *
     * Initialiseert de controller en laadt het Category-model met de databaseverbinding.
     *
     * @param mysqli $db De actieve databaseverbinding.
     */
    public function __construct($db)
    {
        $this->model = new Category($db);
    }

     /**
     * Haalt alle categorieën op met het aantal gekoppelde cursussen.
     *
     * @return array Associatieve array van categorieën en hun cursus-aantallen.
     */
    public function index()
    {
        return $this->model->getAllWithCourseCount();
    }

    /**
     * Haalt een beperkt aantal categorieën op.
     *
     * @param int $limit Het maximum aantal categorieën om op te halen (standaard 8).
     * @return array Associatieve array van categorieën.
     */
    public function getWithLimit($limit = 8)
    {
        return $this->model->getWithLimit($limit);
    }

     /**
     * Haalt alle cursussen op die gekoppeld zijn aan een specifieke categorie.
     *
     * @param int $categoryId Het ID van de categorie.
     * @return array Associatieve array van cursussen.
     */
    public function getCoursesByCategory($categoryId)
    {
        return $this->model->getCoursesByCategory($categoryId);
    }

    /**
     * Haalt alle categorieën op.
     *
     * @return array Associatieve array van categorieën.
     */
    public function getAllCategories()
    {
        return $this->model->getAll();
    }
    
  
        /**
     * Haalt alle categorieën op die gekoppeld zijn aan een specifieke cursus.
     *
     * @param int $courseId Het ID van de cursus.
     * @return array Associatieve array van categorieën.
     */
    public function getCategoriesByCourse($courseId)
    {
        return $this->model->getCategoriesByCourse($courseId);
    }

    /**
     * Maakt een nieuwe categorie aan.
     *
     * Verwacht een POST-verzoek met velden:
     *  - `naam`: De naam van de categorie (verplicht)
     *  - `icon`: De optionele icoonnaam of pad
     *
     * @return void
     */
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

    /**
     * Verwijdert een categorie.
     *
     * Verwacht een POST-verzoek met:
     *  - `id`: Het ID van de te verwijderen categorie.
     *
     * @return void
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
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

    /**
     * Wijzigt een bestaande categorie.
     *
     * Verwacht een POST-verzoek met:
     *  - `id`: Het ID van de categorie (verplicht)
     *  - `naam`: De nieuwe naam van de categorie (verplicht)
     *  - `icon`: De optionele icoonnaam of pad
     *
     * @return void
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
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