<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/core/init.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Alleen admin
requireAdmin();

// Haal alle reservaties op
$reservations = $materiaalController->getAllReservations();

// Nieuw Excel-document
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Reservaties");

// Kolomnamen
$headers = [
    "A1" => "Gebruiker",
    "B1" => "Materiaal",
    "C1" => "Cursus",
    "D1" => "Startdatum",
    "E1" => "Einddatum",
    "F1" => "Status",
    "G1" => "Aangemaakt op"
];

// Headers in sheet plaatsen
foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Data invullen
$row = 2;
foreach ($reservations as $r) {

    // Verdere opmaak
    $sheet->setCellValue("A$row", $r['username']);
    $sheet->setCellValue("B$row", $r['materiaal_naam']);
    $sheet->setCellValue("C$row", $r['cursus_titel']);
    $sheet->setCellValue("D$row", $r['startdatum']);
    $sheet->setCellValue("E$row", $r['einddatum']);
    $sheet->setCellValue("F$row", ucfirst($r['status']));
    $sheet->setCellValue("G$row", $r['aangemaakt_op']);

    $row++;
}

// Kolommen automatisch breder maken
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download headers
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=reservaties_export.xlsx");
header("Cache-Control: max-age=0");

// Wegschrijven
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
