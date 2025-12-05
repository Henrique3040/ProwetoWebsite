<?php
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../models/Notificatie.php';
require_once __DIR__ . "/../core/Mailer.php";
require_once __DIR__ . '/../models/User.php';

class MaterialController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Material($db);
        $this->notificatieModel = new NotificatieController($db);
        $this->UserModel = new UserController($db);
    }

    /* ---------------------------------------------------
        GET ALL MATERIALS (FOR ADMIN)
    --------------------------------------------------- */
    public function getAllMaterials(): array
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
        $aantal = (int) ($_POST['aantal'] ?? 1);


        // --- Foto upload ---
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
            die("Foto upload is verplicht.");
        }

        $fotoPath = $this->uploadFile($_FILES['foto']);

        $this->model->create($naam, $fotoPath, $aantal);

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
        $aantal = (int) ($_POST['aantal'] ?? 1);


        $fotoPath = null;

        // Nieuwe foto geüpload?
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $fotoPath = $this->uploadFile($_FILES['foto']);
        }

        $this->model->update($id, $naam, $aantal, $fotoPath);

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

    public function getMaterialAvailability($materiaal_id, $date)
    {
        return $this->model->getMaterialAvailability($materiaal_id, $date);
    }

    public function reserve($userId, $materialId, $cursusId, $date, $aantal)
    {
        $result = $this->model->reserve($userId, $materialId, $cursusId, $date, $aantal);

        if (!$result['success']) {
            return $result;
        }

        // -------------------------------------------
        // 1. Ophalen volledige reservatie + user info
        // -------------------------------------------
        $reservation = $this->model->getReservationById($result['reservation_id']);
        $user = $this->UserModel->getUserById($userId);
        

        // -------------------------------------------
        // 2. Mail naar gebruiker (bevestiging)
        // -------------------------------------------
        if (!empty($user['email']) && intval($user['email_notifications']) === 1) {
            $subject = "Bevestiging van je reservatie";
            $body = "
            <p>Beste {$user['username']},</p>
            <p>Je reservatie is succesvol ontvangen.</p>
            <p><strong>Materiaal:</strong> {$reservation['materiaal_id']}<br>
               <strong>Aantal:</strong> {$reservation['aantal']}<br>
               <strong>Datum:</strong> {$reservation['startdatum']}</p>
            <p>Status: <strong>In afwachting</strong></p>
        ";

            Mailer::sendMail($user['email'], $subject, $body);
        }

        sleep(10); // 1 seconde wachten zodat Mailtrap niet blokkeert

        // -------------------------------------------
        // 3. Mail naar ALLE admins
        // -------------------------------------------
        $admins = $this->UserModel->getAllAdmins();
        error_log("ADMINS: " . print_r($admins, true));
        foreach ($admins as $admin) {
            if (!empty($admin['email']) && intval($user['email_notifications']) === 1) {
                $subject = "Nieuwe reservatie geplaatst";
                $body = "
                <p>Hallo admin,</p>
                <p>Er is een nieuwe reservatie geplaatst:</p>
                <p>
                    <strong>Gebruiker:</strong> {$user['username']}<br>
                    <strong>Materiaal:</strong> {$reservation['materiaal_id']}<br>
                    <strong>Aantal:</strong> {$reservation['aantal']}<br>
                    <strong>Datum:</strong> {$reservation['startdatum']}
                </p>
            ";

                Mailer::sendMail($admin['email'], $subject, $body);
            }
        }

        return $result;
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

        // --- EMAIL STUREN ---
        if (!empty($reservation['email']) && intval($reservation['email_notifications']) === 1) {
            $subject = "Update van je reservatie";
            $body = "
            <p>Beste gebruiker,</p>
            <p>De status van je reservatie is gewijzigd naar: <strong>$status</strong>.</p>
            <p>Met vriendelijke groet,<br>Het reservatiesysteem</p>
        ";

            Mailer::sendMail($reservation['email'], $subject, $body);
        }

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
