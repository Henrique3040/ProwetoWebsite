<?php
// Laad databaseconfig
require_once __DIR__ . '/../config/database.php';

// Controllers
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/CourseController.php';

// Controller-objecten
$categoryController = new CategoryController($conn);
$courseController   = new CourseController($conn);

?>

