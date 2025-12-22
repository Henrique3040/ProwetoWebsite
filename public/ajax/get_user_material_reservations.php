<?php
require_once __DIR__ . '/../../app/core/init.php';

header('Content-Type: application/json');

$userId = $_SESSION['user']['id'] ?? null;
$materialId = $_GET['material_id'] ?? null;

if (!$userId || !$materialId) {
    echo json_encode(['success' => false]);
    exit;
}

$reservations = $materiaalController->getUserReservations($userId);

/*
We filteren hier al op materiaal
en mappen naar iets dat frontend makkelijk gebruikt
*/
$mapped = [];

foreach ($reservations as $r) {
    if ($r['materiaal_id'] != $materialId) continue;

    $date = $r['startdatum'];

    if (!isset($mapped[$date])) {
        $mapped[$date] = [];
    }

    $mapped[$date][] = [
        'start' => $r['starttijd'],
        'end'   => $r['eindtijd'],
        'status'=> $r['status'],
        'periode' => $r['periode']
    ];
}

echo json_encode([
    'success' => true,
    'data' => $mapped
]);
