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



 $materialId = $_POST['material_id'];
 $userId = $_SESSION['user']['id'];

 


/**
 * @var MateriaalController $materiaalController  
 *  Wordt geïnitialiseerd in init.php
 */

// Stap 4: Reservatie uitvoeren
 $result = $result = $materiaalController->reserve(
  $userId,
  $_POST['material_id'],
  $_POST['course_id'],
  $_POST['datum'],
  $_POST['aantal'],
);

 
 
// Stap 5: Redirect op basis van resultaat
 if ($result['success']) {
  // Succesvolle reservatie
 // Redirect terug naar de pagina waar de gebruiker vandaan kwam
  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit;
 } else {
  // Mislukt → stuur foutcode mee
   header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=" . $result['reason']);
   exit;
 }
