<?php
// Laad databaseconfig
require_once __DIR__ . '/../config/database.php';
// Usage:
$db = Database::getInstance();
$conn = $db->getConnection();


// Controllers
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/CourseController.php';

// Controller-objecten
$categoryController = new CategoryController($conn);
$courseController   = new CourseController($conn);

?>

