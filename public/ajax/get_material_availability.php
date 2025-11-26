<?php
require_once "../../app/core/init.php";

header("Content-Type: application/json");

if (!AuthController::isLoggedIn()) {
    http_response_code(403);
    echo json_encode(["error" => "NOT_LOGGED_IN"]);
    exit;
}


$materialId = $_GET['material_id'];

if(!$materialId){
    echo json_encode(["error" => "Material ID is required"]);
    exit;
}

$data = $materiaalController->getMaterialAvailability($materialId);


echo json_encode($data);
exit;
