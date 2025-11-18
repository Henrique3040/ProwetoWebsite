<?php
/**
 * CourseController
 *
 * Deze controller beheert alle logica met betrekking tot cursussen.
 * Hij handelt weergave, zoeken, filtering en CRUD-bewerkingen af 
 * door te communiceren met het Course-model.
 *
 * @author  
 * @version 1.0
 */

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Document.php';


class CourseController
{
    /**
     * @var Course Het model dat database-interacties voor cursussen beheert.
     */
    private $model;

    /**
     * Constructor
     *
     * Initialiseert de controller en laadt het Course-model en Document-model met de databaseverbinding.
     *
     * @param mysqli $db De actieve databaseverbinding.
     */
    public function __construct($db)
    {
        $this->model = new Course($db);
        $this->documentModel = new Document($db);

    }

    /**
     * Haalt uitgelichte cursussen op (voor homepage of sectie "Aanbevolen cursussen").
     *
     * @param int $limit Het maximum aantal cursussen (standaard 8).
     * @return array Associatieve array van uitgelichte cursussen.
     */
    public function featured($limit = 8)
    {
        return $this->model->getFeaturedCourses($limit);
    }

    /**
     * Haalt de details van een specifieke cursus op.
     * Voegt tevens een weergave ("view") toe aan de cursus.
     *
     * @param int $courseId Het ID van de cursus.
     * @return array Associatieve array met cursusdetails.
     */
    public function getCourseDetail($courseId)
    {
        // eerst view toevoegen
        $this->model->addView($courseId);

        return $this->model->getCourseDetail($courseId);
    }

    /**
     * Voegt een beoordeling (rating) toe aan een cursus.
     *
     * @param int $courseId Het ID van de cursus.
     * @param int|float $rating De toegevoegde beoordeling (bijv. 1-5 sterren).
     * @return bool True als de beoordeling succesvol is toegevoegd.
     */

    public function rateCourse($courseId, $rating)
    {
        return $this->model->addRating($courseId, $rating);
    }

    /**
     * Zoekt naar cursussen op basis van een zoekterm.
     *
     * @param string $query De zoekterm.
     * @return array Resultaten van de zoekopdracht.
     */
    public function searchCourses($query)
    {
        return $this->model->searchCourses($query);
    }

    /**
     * Haalt alle cursussen op.
     *
     * @return array Lijst van alle cursussen.
     */
    public function getAllCourses()
    {
        return $this->model->getAllCourses();
    }

    /**
     * Haalt het totaal aantal cursussen op.
     *
     * @return int Het aantal cursussen.
     */
    public function getAllCount()
    {
        return $this->model->getAllCount();
    }

    /**
     * Haalt alle geactiveerde cursussen op.
     *
     * @return array Lijst van actieve cursussen.
     */
    public function getActivatedCourses()
    {
        return $this->model->getActivatedCourses();
    }

    /**
     * Haalt alle gedeactiveerde cursussen op.
     *
     * @return array Lijst van inactieve cursussen.
     */
    public function getInactiveCourses()
    {
        return $this->model->getInactiveCourses();
    }


    /**
     * Haalt cursussen op met filters (bijvoorbeeld categorie, leerjaar, status).
     *
     * @param array $filters Filtercriteria (optioneel).
     * @param int $limit Aantal resultaten per pagina.
     * @param int $page Huidige pagina.
     * @return array Gefilterde lijst van cursussen.
     */
    public function getFilteredCourses($filters = [], $limit = 10, $page = 1)
    {
        return $this->model->getFilteredCourses($filters, $limit, $page);
    }

    /**
     * Haalt cursussen op met filters voor de admin-omgeving.
     *
     * @param array $filters Filtercriteria (optioneel).
     * @param int $limit Aantal resultaten per pagina.
     * @param int $page Huidige pagina.
     * @return array Lijst van cursussen met admin-specifieke informatie.
     */
    public function getCoursesAdmin($filters = [], $limit = 10, $page = 1)
    {
        return $this->model->getCoursesAdmin($filters, $limit, $page);
    }

    /**
     * Slaat een nieuwe cursus op in de database.
     *
     * Verwacht een POST-verzoek met o.a.:
     * - `titel`, `korte_beschrijving`, `beschrijving`
     * - `categorie_id`, `video_link`, `leerjaar_id`
     * - `foto` (bestand upload)
     * - `faqs` (JSON string)
     * - `materiaal`, `documenten`, `active` (checkboxen)
     *
     * @return void
     */
    public function store()
    {

       


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = $_POST['titel'];
            $korteBeschrijving = $_POST['korte_beschrijving'];
            $beschrijving = $_POST['beschrijving'];
            $categorieID = $_POST['categorie_id'];
            $videoLink = $_POST['video_link'];
            $leerjaarId = $_POST['leerjaar_id'] ?? null;
            // Booleans
            $materiaal = isset($_POST['materiaal']) ? 1 : 0;
            $documenten = isset($_POST['documenten']) ? 1 : 0;
            $active = isset($_POST['active']) ? 1 : 0;
            $selectedMaterials = $_POST['material_ids'] ?? [];



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
                'Active' => $active,
                'Materiaal' => $materiaal,
                'Documenten' => $documenten,
                'LeerJaarID' => $leerjaarId,
                'faqs' => $faqs,
                'material_ids' => $selectedMaterials
            ]);
            

            if ($cursusId) {
                $this->uploadDocuments($cursusId);
                header('Location: admin-create-course.php?controller=course&action=success');
                exit;
            } else {
                echo "<p style='color:red;'>Er is iets misgegaan bij het opslaan van de cursus.</p>";
            }
        }
    }


    /**
     * Verwijdert een cursus uit de database.
     *
     * @param int $courseId Het ID van de te verwijderen cursus.
     * @return bool True als de cursus succesvol verwijderd is.
     */
    public function delete($courseId)
    {
        return $this->model->deleteCourse($courseId);
    }

    /**
     * Haalt de laatst bijgewerkte cursussen op.
     *
     * @param int $limit Het aantal cursussen om op te halen (standaard 5).
     * @return array Lijst van recent bijgewerkte cursussen.
     */
    public function getLatestUpdatedCourses($limit = 5)
    {
        return $this->model->getLatestUpdatedCourses($limit);
    }


    /**
     * Werkt een bestaande cursus bij in de database.
     *
     * Verwacht een POST-verzoek met:
     * - `titel`, `korte_beschrijving`, `beschrijving`
     * - `categorie_id`, `video_link`, `leerjaar_id`
     * - `foto` (optionele nieuwe upload)
     * - `faqs` (JSON-string met FAQ’s)
     * - `deletedFaqs` (JSON-string met te verwijderen FAQ-ID’s)
     *
     * @param int $courseId Het ID van de te updaten cursus.
     * @return void
     */
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

                $oldPhoto = $this->model->getCourseDetail($courseId)['FotoURL'] ?? null;
                if ($oldPhoto && file_exists($oldPhoto)) {
                    unlink($oldPhoto);
                }

                $filename = time() . '_' . basename($_FILES['foto']['name']);
                $targetFile = $uploadDir . $filename;
                move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile);
                $fotoURL = $targetFile;
            }


            // Nieuwe documenten uploaden
            $uploadedDocs = [];
            if (isset($_FILES['nieuwe_documenten']) && !empty($_FILES['nieuwe_documenten']['name'][0])) {
                $uploadDir = 'uploads/documents/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0755, true);

                foreach ($_FILES['nieuwe_documenten']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['nieuwe_documenten']['error'][$key] === UPLOAD_ERR_OK) {
                        $filename = time() . '_' . basename($_FILES['nieuwe_documenten']['name'][$key]);
                        $targetFile = $uploadDir . $filename;
                        move_uploaded_file($tmpName, $targetFile);
                        $uploadedDocs[] = $targetFile;
                    }
                }
            }


            // FAQ’s (optioneel)
            $faqs = [];
            if (isset($_POST['faqs']) && !empty($_POST['faqs'])) {
                $faqs = json_decode($_POST['faqs'], true);
            }

            // Verwijderde FAQ's
            $deletedFaqIDs = [];
            if (isset($_POST['deletedFaqs']) && !empty($_POST['deletedFaqs'])) {
                $deletedFaqIDs = json_decode($_POST['deletedFaqs'], true);
            }

            $deleteDocIDs = [];
            if (isset($_POST['deletedDocuments']) && !empty($_POST['deletedDocuments'])) {
                $deleteDocIDs = json_decode($_POST['deletedDocuments'], true);
            }

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
                'Faqs' => $faqs,
                'DeletedFaqIDs' => $deletedFaqIDs, // belangrijk!
                'UploadedDocuments' => $uploadedDocs,
                'DeletedDocumentIDs' => $deleteDocIDs // belangrijk!
            ]);

            if ($updated) {
                header("Location: admin-course-list.php?success=1");
                exit;
            } else {
                echo "<p style='color:red;'>Er is iets misgegaan bij het bijwerken van de cursus.</p>";
            }
        }
    }

    public function uploadDocuments($courseId)
    {
        if (isset($_FILES['documents'])) {
            $uploadDir = 'uploads/documents/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0755, true);

            foreach ($_FILES['documents']['name'] as $key => $name) {
                if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $filename = time() . '_' . basename($name);
                    $targetFile = $uploadDir . $filename;
                    move_uploaded_file($_FILES['documents']['tmp_name'][$key], $targetFile);

                    $fileType = pathinfo($name, PATHINFO_EXTENSION);
                    $this->documentModel->addDocument($courseId, $name, $targetFile, $fileType);
                }
            }
        }
    }


}
?>