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

        header("Location: admin-materialen.php");
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

        //Tijd van nammiddag enzo kunt aangepast worden naar gelang de nodige uren van het school
        $periode = $_POST['periode'];
        switch ($periode) {
            case "voormiddag":
                $starttijd = "08:00";
                $eindtijd = "12:00";
                break;

            case "namiddag":
                $starttijd = "12:00";
                $eindtijd = "17:00";
                break;
            case "avond":
                $starttijd = "17:00";
                $eindtijd = "20:00";
            break;
            default:
            case "hele_dag":
                $starttijd = "08:00";
                $eindtijd = "17:00";
        }

        //data opslaan via model
        $dateId = $this->model->addAvailability([
            'materiaal_id' => $_POST['materiaal_id'],
            'startdatum' => $_POST['startdatum'],
            'einddatum' => $_POST['einddatum'],
            'starttijd' => $starttijd,
            'eindtijd' => $eindtijd,
            'periode' => $periode
        ]);

        if ($dateId) {
            header("Location: admin-material-availability.php");
            exit;
        } else {
            die("Fout bij het toevoegen van beschikbaarheid.");
        }
    }

    public function getMaterialAvailability($materiaal_id, $date = null)
    {
        if (!$date) {
            return $this->model->getAvailabilityWithStock($materiaal_id);
        }

        return $this->model->getDayAvailability($materiaal_id, $date);
    }



    public function getAllAvailability($materiaal_id)
    {
        return $this->model->getAllAvailability($materiaal_id);
    }

    public function reserve($userId, $materialId, $cursusId, $date, $periode, $aantal)
    {
        $result = $this->model->reserve($userId, $materialId, $cursusId, $date, $periode, $aantal);

        if (!$result['success']) {
            return $result;
        }
        return $result;
    }

    /**
     * Verwerkt en bewaart meerdere materiaalreservaties voor één gebruiker.
     * Stopt en retourneert een fout zodra één reservatie faalt.
     */
    public function reserveMultiple($userId, $materialId, $courseId, array $reservations)
    {
        $saved = [];

        foreach ($reservations as $r) {
            $res = $this->model->reserve(
                $userId,
                $materialId,
                $courseId,
                $r['date'],
                $r['periode'],
                (int) $r['aantal']
            );

            if (!$res['success']) {
                return $res;
            }

            $saved[] = [
                'date' => $r['date'],
                'periode' => $r['periode'],
                'aantal' => $r['aantal']
            ];
        }

        //Deze is de email logica, staat in coment om niet al calls te gebruiken.
        
        // -------------------------
        // DATA OPHALEN
        // -------------------------
        $user = $this->UserModel->getUserById($userId);

        

        // -------------------------
        // 1 MAIL NAAR GEBRUIKER
        // -------------------------
        if (!empty($user['email']) && intval($user['email_notifications']) === 1) {

            $rows = '';
            foreach ($saved as $s) {
                $rows .= "
                  <tr>
                    <td>{$s['date']}</td>
                    <td>{$s['periode']}</td>
                    <td>{$s['aantal']}</td>
                  </tr>";
            }

            $body = "
            <p>Beste {$user['username']},</p>
            <p>Je reservatie is succesvol ontvangen met de volgende details:</p>
    
            <table border='1' cellpadding='6' cellspacing='0'>
              <tr>
                <th>Datum</th>
                <th>Periode</th>
                <th>Aantal</th>
              </tr>
              {$rows}
            </table>
    
            <p>Status: <strong>In afwachting</strong></p>
            ";

            Mailer::sendMail(
                $user['email'],
                "Bevestiging van je reservatie",
                $body
            );
        }
        

        sleep(10); // 1 seconde wachten zodat Mailtrap niet blokkeert

        // -------------------------
        // 1 MAIL NAAR ADMINS
        // -------------------------
        $admins = $this->UserModel->getAllAdmins();

        foreach ($admins as $admin) {
            if (!empty($admin['email'])) {

                Mailer::sendMail(
                    $admin['email'],
                    "Nieuwe reservatie geplaatst",
                    $body // zelfde body is prima
                );
            }
        }

        return [
            "success" => true
        ];
    }



    /**
    * Haalt het gekoppelde cursus-ID op voor een gegeven materiaal.
    */
    public function getCourseIdFromMaterial($materialId)
    {
        return $this->model->getCourseIdFromMaterial($materialId);
    }


    /**
    * Verwijdert een beschikbaarheidsrecord (adminfunctie).
    */
    public function deleteAvailability($id)
    {
        $this->model->deleteAvailability($id);
    }


    /**
    * Geeft een overzicht van alle reservaties (admin).
    */
    public function getAllReservations()
    {
        return $this->model->getAllReservations();
    }

    public function deleteReservation($id)
    {
        return $this->model->deleteReservation($id);
    }

    /**
    * Wijzigt de status van een reservatie en verstuurt notificaties indien toegestaan.
    */
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
