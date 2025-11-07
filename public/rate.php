<?php
require_once __DIR__ . '/../app/core/init.php';

$courseId = $_POST['course_id'];
$rating = intval($_POST['rating']);

$courseController->rateCourse($courseId, $rating);

// cookie zetten zodat ze niet opnieuw kunnen stemmen
setcookie("rated_$courseId", "1", time() + (86400 * 365), "/");

echo json_encode(['success' => true]);
