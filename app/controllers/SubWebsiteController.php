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

    public function update(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['subwebsite_id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $link  = $_POST['link'] ?? '';
            $icon  = $_POST['icon'] ?? '';
        }

        $result = $this->model->update($id, $title, $link, $icon);
        if ($result) {
            header('Location: admin-subwebsites.php?success=updated');
                exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Fout bij het aanmaken van de subwebsite']);
        }
    }

    public function create(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $link  = $_POST['link'] ?? '';
            $icon  = $_POST['icon'] ?? '';
    
            if (empty($title) || empty($link)) {
                echo json_encode(['success' => false, 'message' => 'Titel en link zijn verplicht']);
                return;
            }
            
        }
    
        $result = $this->model->create($title, $link, $icon);
        if ($result) {
            header('Location: admin-subwebsites.php?success=created');
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Fout bij het aanmaken van de subwebsite']);
        }
    }

    public function delete(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subwebsiteId = $_POST['subwebsite_id'] ?? null;
            if (!$subwebsiteId) {
                echo json_encode(['success' => false, 'message' => 'Geen ID ontvangen']);
                return;
            }
        }
        $result = $this->model->delete($subwebsiteId);
        if ($result) {
            header('Location: admin-subwebsites.php?success=deleted');
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Fout bij het verwijderen van de subwebsite']);
        }
    }
}
?>
