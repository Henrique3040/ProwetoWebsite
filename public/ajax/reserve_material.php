<?php
require_once "../../app/core/init.php";
 if (!AuthController::isLoggedIn()) {
    http_response_code(403);
    echo "NOT_LOGGED_IN";
    exit;
 }

 $userId = $_SESSION['user']['id'];
 $materialId = $_POST['material_id'];
 $courseId = $_POST['course_id'];
 $date = $_POST['datum'];

 if (!$materialId || !$date) {
   echo "MISSING_DATA";
   exit;
}


 $result = $materiaalController->reserve($userId, $materialId, $courseId,$date);
 
 

 if ($result['success']) {
   header("Location: /course-detail.php?id=" . $courseId);
   exit;
 } else {
   header("Location: /course-detail.php?id=" . $courseId . "&error=" . $result['reason']);
   exit;
 }
