<?php
// Laad databaseconfig
require_once __DIR__ . '/../config/database.php';
// Usage:
$db = Database::getInstance();
$conn = $db->getConnection();


// Controllers
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/CourseController.php';
require_once __DIR__ . '/../controllers/SubWebsiteController.php';
require_once __DIR__ . '/../controllers/FaqController.php';

// Controller-objecten
$categoryController = new CategoryController($conn);
$courseController   = new CourseController($conn);
$subWebsiteController = new SubWebsiteController($conn);
$faqController = new FaqController($conn);

?>

