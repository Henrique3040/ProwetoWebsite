<?php
require_once "../../app/core/init.php";
 if (!AuthController::isLoggedIn()) {
    http_response_code(403);
    echo "NOT_LOGGED_IN";
    exit;
 }

 $userId = $_SESSION['user']['id'];
 $materialId = $_POST['material_id'];
 $date = $_POST['datum'];

 if (!$materialId || !$date) {
   echo "MISSING_DATA";
   exit;
}


 $result = $materiaalController->reserve($userId, $materialId, $date);
 $cursusid = $materiaalController->getCourseIdFromMaterial($materialId);
 

 if ($result['success']) {
   header("Location: /course-detail.php?id=" . $cursusid);
   exit;
 } else {
   header("Location: /course-detail.php?id=" . $cursusid . "&error=" . $result['reason']);
   exit;
 }
