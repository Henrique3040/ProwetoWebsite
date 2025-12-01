<?php
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../models/Notificatie.php';

class MaterialController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Material($db);
        $this->notificatieModel = new NotificatieController($db);
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

    public function reserve($userId, $materialId, $cursusId, $date)
    {

        $success = $this->model->reserve($userId, $materialId, $cursusId, $date);

        return $success;

    }

    public function getCourseIdFromMaterial($materialId)
    {
        return $this->model->getCourseIdFromMaterial($materialId);
    }

    public function deleteAvailability($id)
    {
        $this->model->deleteAvailability($id);
    }


    public function getAllReservations()
    {
        return $this->model->getAllReservations();
    }

    public function deleteReservation($id)
    {
        return $this->model->deleteReservation($id);
    }

    public function updateReservationStatus($id, $status)
    {
        // 1. status bijwerken in de database
        $this->model->updateReservationStatus($id, $status);

        // 2. ophalen user-id van de reservatie
        $reservation = $this->model->getReservationById($id);

        // 3. notificatie toevoegen
        $message = "De status van je reservatie is gewijzigd naar: $status.";
        $this->notificatieModel->addNotification($reservation['user_id'], $message);

        return true;
    }


    // Haal reservaties van ingelogde user
    public function getUserReservations(string $userId): array
    {
        return $this->model->getReservationsByUser($userId);
    }

    // User verwijdert eigen reservatie (wrapper)
    public function deleteReservationForUser(string $reservationId, string $userId): array
    {
        return $this->model->deleteReservationByUser($reservationId, $userId);
    }




}
