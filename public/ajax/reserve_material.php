<?php
/**
 * Endpoint: Materiaal reserveren
 *
 * Dit script:
 *  1. Controleert of de gebruiker is ingelogd
 *  2. Leest POST-data voor een materiaalaanvraag
 *  3. Probeert het materiaal te reserveren via MateriaalController
 *  4. Stuurt de gebruiker terug naar de cursusdetailpagina
 *     - Bij succes: normale redirect
 *     - Bij fout   : redirect met errorcode
 *
 * Vereiste POST-velden:
 *  - material_id (string, verplicht)
 *  - course_id   (string, optioneel afhankelijk van situatie)
 *  - datum       (date, verplicht)
 *  - aantal      (integer, standaard = 1)
 */
require_once "../../app/core/init.php";

// Stap 1: Verplicht ingelogd
 if (!AuthController::isLoggedIn()) {
    http_response_code(403);
    echo "NOT_LOGGED_IN";
    exit;
 }

 // Stap 2: Ophalen gebruiker en POST-waardes
 $userId = $_SESSION['user']['id'];
 $materialId = $_POST['material_id'];
 $courseId = $_POST['course_id'];
 $date = $_POST['datum'];
 $aantal = $_POST['aantal'] ?? 1;

// Stap 3: Validatie van noodzakelijke velden
 if (!$materialId || !$date) {
   echo "MISSING_DATA";
   exit;
}

/**
 * @var MateriaalController $materiaalController  
 *  Wordt geïnitialiseerd in init.php
 */

// Stap 4: Reservatie uitvoeren
 $result = $materiaalController->reserve($userId, $materialId, $courseId, $date, $aantal);

 
 
// Stap 5: Redirect op basis van resultaat
 if ($result['success']) {
  // Succesvolle reservatie
   header("Location: /course-detail.php?id=" . $courseId);
   exit;
 } else {
  // Mislukt → stuur foutcode mee
   header("Location: /course-detail.php?id=" . $courseId . "&error=" . $result['reason']);
   exit;
 }
