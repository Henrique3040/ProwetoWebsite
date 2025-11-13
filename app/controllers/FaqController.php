<?php
/**
 * FaqController
 *
 * Deze controller beheert alle logica met betrekking tot veelgestelde vragen (FAQ’s)
 * die gekoppeld zijn aan specifieke cursussen. 
 * Hij communiceert met het Faq-model voor het ophalen en opslaan van FAQ-gegevens.
 *
 * @author  
 * @version 1.0
 */

require_once __DIR__ . '/../models/Faq.php';
class FaqController
{
     /**
     * @var Faq Het model dat database-interacties voor FAQ’s beheert.
     */
    private $model;

    /**
     * Constructor
     *
     * Initialiseert de controller en laadt het Faq-model met de databaseverbinding.
     *
     * @param mysqli $db De actieve databaseverbinding.
     */
    public function __construct($db)
    {
        $this->model = new Faq($db);
    }

    /**
     * Slaat een nieuwe FAQ op in de database.
     *
     * Verwacht een POST-verzoek met velden:
     *  - `vraag`: De vraagtekst van de FAQ.
     *  - `antwoord`: Het antwoord op de vraag.
     *  - `cursus_id`: Het ID van de cursus waar deze FAQ bij hoort.
     *
     * Bij succes wordt doorgestuurd naar `admin-course-faq.php` met een succesmelding.
     *
     * @return void
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vraag'], $_POST['antwoord'], $_POST['cursus_id'])) {
            $cursusId = $_POST['cursus_id'];
            $vraag = trim($_POST['vraag']);
            $antwoord = trim($_POST['antwoord']);
            $this->model->createFaq($cursusId, $vraag, $antwoord);
            header("Location: admin-course-faq.php?id=$cursusId&success=1");
            exit;
        }
    }

     /**
     * Haalt alle FAQ’s op die bij een specifieke cursus horen.
     *
     * @param int $cursusId Het ID van de cursus.
     * @return array Lijst van FAQ’s voor de opgegeven cursus.
     */
    public function index($cursusId)
    {
        return $this->model->getFaqsByCourse($cursusId);
    }
}
?>
