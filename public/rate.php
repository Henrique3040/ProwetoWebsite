<?php
/**
 * Script: Cursus rating verwerken
 *
 * Functionaliteit:
 *  - Ontvangt POST-parameters `course_id` en `rating`
 *  - Roept de controller aan om de rating op te slaan
 *  - Plaatst een cookie om te voorkomen dat een gebruiker opnieuw stemt
 *  - Geeft JSON-response terug
 */

require_once __DIR__ . '/../app/core/init.php';

// ----------------------------
// 1️⃣ POST data ophalen
// ----------------------------
$courseId = $_POST['course_id'] ?? null;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

// Validatie
if (!$courseId || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Ongeldige data']);
    exit;
}

// ----------------------------
// 2️⃣ Rating opslaan via controller
// ----------------------------
$courseController->rateCourse($courseId, $rating);

// ----------------------------
// 3️⃣ Cookie zetten om dubbele stemmen te voorkomen
// ----------------------------
// Naam: rated_<courseId>, waarde: "1", geldig 1 jaar
setcookie("rated_$courseId", "1", time() + (86400 * 365), "/");

// ----------------------------
// 4️⃣ JSON response teruggeven
// ----------------------------
echo json_encode(['success' => true]);
