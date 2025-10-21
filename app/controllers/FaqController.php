<?php
require_once __DIR__ . '/../models/Faq.php';
class FaqController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Faq($db);
    }

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

    public function index($cursusId)
    {
        return $this->model->getFaqsByCourse($cursusId);
    }
}
?>
