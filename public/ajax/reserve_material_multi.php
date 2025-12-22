<?php
/**
 * ajax/reserve_material_multi.php
 */

require_once "../../app/core/init.php";

header("Content-Type: application/json; charset=utf-8");

// -------------------------------------------------
// 1) LOGIN CHECK
// -------------------------------------------------
if (!AuthController::isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'NOT_LOGGED_IN']);
    exit;
}

// -------------------------------------------------
// 2) JSON INLEZEN
// -------------------------------------------------
$raw = $_POST['json'] ?? file_get_contents("php://input");
if (!$raw) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No JSON payload']);
    exit;
}

$data = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// -------------------------------------------------
// 3) BASIS VALIDATIE
// -------------------------------------------------
$materialId = $data['materialId'] ?? null;
$courseId   = $data['courseId'] ?? null;
$items      = $data['items'] ?? null;

if (!$materialId || !is_array($items) || count($items) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'NOT_LOGGED_IN']);
    exit;
}

// -------------------------------------------------
// 4) FRONTEND → BACKEND TRANSFORMATIE
// -------------------------------------------------
// JS:
// items[] = { date, periodes: { voormiddag: {amount}, ... } }
//
// reserveMultiple verwacht:
// [
//   ['date','periode','aantal'],
//   ...
// ]
// -------------------------------------------------
$reservations = [];

foreach ($items as $item) {
    $date = $item['date'] ?? null;
    $periodes = $item['periodes'] ?? [];

    if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        continue;
    }

    foreach ($periodes as $periode => $meta) {
        $aantal = isset($meta['amount'])
            ? intval($meta['amount'])
            : intval($meta['aantal'] ?? 0);

        if ($aantal > 0) {
            $reservations[] = [
                'date' => $date,
                'periode' => $periode,
                'aantal' => $aantal
            ];
        }
    }
}

if (count($reservations) === 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Geen geldige reservaties gevonden'
    ]);
    exit;
}

// -------------------------------------------------
// 5) RESERVEER VIA NIEUWE FUNCTIE
// -------------------------------------------------
try {
    $result = $materiaalController->reserveMultiple(
        $userId,
        $materialId,
        $courseId,
        $reservations
    );

    if (!$result['success']) {
        http_response_code(409);
        echo json_encode($result);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Alle reservaties succesvol aangemaakt en mails verzonden.'
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
    exit;
}
