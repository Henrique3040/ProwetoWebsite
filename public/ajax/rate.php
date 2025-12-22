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

require_once __DIR__ . '/../../app/core/init.php';

// ----------------------------
// 1️⃣ POST data ophalen
// ----------------------------
$userId   = $_SESSION['user']['id'];
$courseId = $_POST['course_id'] ?? null;
$rating   = $_POST['rating'] ?? null;

// Validatie
if (!$courseId || !$rating) {
    echo json_encode(['success' => false]);
    exit;
}

// ----------------------------
// 2️⃣ Rating opslaan via controller
// ----------------------------
$courseController->rateCourse($courseId, $userId, (int)$rating);
error_log("RATE AJAX HIT");

// ----------------------------
// 3 JSON response teruggeven
// ----------------------------
echo json_encode(['success' => true]);
