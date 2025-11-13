<?php
/**
 * SubWebsiteController
 *
 * Deze controller beheert alle logica rondom subwebsites binnen de applicatie.
 * Hij communiceert met het SubWebsite-model om subwebsites op te halen, aan te maken,
 * bij te werken of te verwijderen.
 *
 * @author  
 * @version 1.0
 */

require_once __DIR__ . '/../models/SubWebsite.php';

class SubWebsiteController
{

    /**
     * @var SubWebsite Het model dat database-interacties voor subwebsites beheert.
     */
    private $model;

    /**
     * Constructor
     *
     * Initialiseert de controller en laadt het SubWebsite-model met de databaseverbinding.
     *
     * @param mysqli $db De actieve databaseverbinding.
     */
    public function __construct($db)
    {
        $this->model = new SubWebsite($db);
    }

    /**
     * Haalt alle subwebsites op uit de database.
     *
     * @return array Associatieve array van alle subwebsites.
     */
    public function index()
    {
        return $this->model->getAll();
    }

    /**
     * Werkt een bestaande subwebsite bij in de database.
     *
     * Verwacht een POST-verzoek met velden:
     *  - `subwebsite_id`: Het ID van de subwebsite die wordt bijgewerkt.
     *  - `title`: De nieuwe titel van de subwebsite.
     *  - `link`: De nieuwe link (URL) van de subwebsite.
     *  - `icon`: (optioneel) Het icoon dat de subwebsite representeert.
     *
     * @return void
     */
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

    /**
     * Maakt een nieuwe subwebsite aan.
     *
     * Verwacht een POST-verzoek met velden:
     *  - `title`: De titel van de subwebsite (verplicht).
     *  - `link`: De URL van de subwebsite (verplicht).
     *  - `icon`: (optioneel) Het icoon dat de subwebsite visueel voorstelt.
     *
     * @return void
     */
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

    /**
     * Verwijdert een bestaande subwebsite uit de database.
     *
     * Verwacht een POST-verzoek met:
     *  - `subwebsite_id`: Het ID van de te verwijderen subwebsite.
     *
     * @return void
     */
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
