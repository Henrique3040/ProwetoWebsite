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

        header("Location: admin-course-materials.php?updated=1");
        exit;
    }

    /* ---------------------------------------------------
        DELETE MATERIAL
    --------------------------------------------------- */
    public function delete()
    {
        $id = $_POST['id'];

        $this->model->delete($id);

        header("Location: admin-course-materials.php?deleted=1");
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
}
