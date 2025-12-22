<?php
/**
 * API Endpoint: Materiaal beschikbaarheid ophalen
 *
 * Doel:
 *  Retourneert beschikbaarheid van een bepaald materiaal op een bepaalde datum.
 *
 * Vereisten:
 *  - Gebruiker moet zijn ingelogd (403 wanneer niet ingelogd)
 *  - Parameter 'material_id' is verplicht
 *  - Parameter 'date' is optioneel (bij geen datum geeft de controller meestal volledige kalender terug)
 *
 * Response:
 *  - JSON-array met beschikbaarheidsdata
 *  - Of JSON met "error" wanneer er een fout optreedt
 */
require_once "../../app/core/init.php";
header("Content-Type: application/json");

// Stap 1: Controleer loginstatus
if (!AuthController::isLoggedIn()) {
    http_response_code(403);
    echo json_encode(["error" => "NOT_LOGGED_IN"]);
    exit;
}

// Stap 2: Lees GET-parameters
$materialId = $_GET['material_id'] ?? null;
$date = $_GET['date'] ?? null;

// Material ID is verplicht
if (!$materialId) {
    echo json_encode(["error" => "Material ID is required"]);
    exit;
}

// Stap 3: Haal beschikbaarheid op via de controller
/**
 * @var MateriaalController $materiaalController
 * Wordt aangemaakt in init.php
 */
$data = $materiaalController->getMaterialAvailability($materialId, $date);

// Stap 4: JSON-response terugsturen
echo json_encode($data);
exit;
