<?php
require_once __DIR__ . '/../models/Material.php';

class MaterialController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Material($db);
    }

    /* ---------------------------------------------------
        GET ALL MATERIALS (FOR ADMIN)
    --------------------------------------------------- */
    public function getAllMaterials()
    {
        return $this->model->getAll();
    }


    public function getMaterialById($id)
    {
        return $this->model->getById($id);
    }

    /* ---------------------------------------------------
        CREATE MATERIAL
    --------------------------------------------------- */
    public function create()
    {
        if (empty($_POST['naam'])) {
            die("Naam is verplicht.");
        }

        $naam = trim($_POST['naam']);

        // --- Foto upload ---
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
            die("Foto upload is verplicht.");
        }

        $fotoPath = $this->uploadFile($_FILES['foto']);

        $this->model->create($naam, $fotoPath);

        header("Location: admin-materialen.php");
        exit;
    }

    /* ---------------------------------------------------
        UPDATE MATERIAL
    --------------------------------------------------- */
    public function update()
    {
        $id = $_POST['id'];
        $naam = trim($_POST['naam']);

        $fotoPath = null;

        // Nieuwe foto geüpload?
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $fotoPath = $this->uploadFile($_FILES['foto']);
        }

        $this->model->update($id, $naam, $fotoPath);

        header("Location: admin-materialen?succes=1.php?");
        exit;
    }

    /* ---------------------------------------------------
        DELETE MATERIAL
    --------------------------------------------------- */
    public function delete()
    {
        $id = $_POST['id'];

        $this->model->delete($id);

        header("Location: admin-materialen.php");
        exit;
    }

    /* ---------------------------------------------------
        FILE UPLOAD HANDLER
    --------------------------------------------------- */
    private function uploadFile($file)
    {
        $folder = __DIR__ . "/../../public/uploads/materials/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = generateUUID() . "." . $ext;

        $target = $folder . $newName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            die("Upload mislukt.");
        }

        // Return browser-friendly path
        return "uploads/materials/" . $newName;
    }


    public function addAvailability()
    {

        //data opslaan via model
        $dateId = $this->model->addAvailability([
            'materiaal_id' => $_POST['materiaal_id'],
            'startdatum' => $_POST['startdatum'],
            'einddatum' => $_POST['einddatum'],
            'starttijd' => $_POST['starttijd'] ?? null,
            'eindtijd' => $_POST['eindtijd'] ?? null
        ]);

        if ($dateId) {
            header("Location: admin-material-availability.php");
            exit;
        } else {
            die("Fout bij het toevoegen van beschikbaarheid.");
        }
    }

    public function getMaterialAvailability($materiaal_id)
    {
        return $this->model->getMaterialAvailability($materiaal_id);
    }

    public function reseve()
    {
        $user_id = $_POST['user_id'];
        $materialId = $_POST['material_id'];
        $date = $_POST['datum'];


        $this->model->reserve($user_id, $materialId, $date);

        if ($this->model->reserve($user_id, $materialId, $date)) {
            header("Location: admin-materialen");
        } else {
            header("Location: material-details.php?error=1");
        }

    }

    public function deleteAvailability($id)
    { 
        $this->model->deleteAvailability($id);
    }



}
