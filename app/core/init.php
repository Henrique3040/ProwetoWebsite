<?php
/**
 * Application Initializer
 *
 * Dit script start de sessie, maakt verbinding met de database
 * en laadt alle benodigde controllers. 
 * 
 * Het dient als een centrale plek voor het initialiseren van 
 * de belangrijkste onderdelen van de applicatie.
 *
 * @author  
 * @version 1.0
 */


// Start een PHP-sessie (voor gebruikersauthenticatie, status, etc.)
session_start();




/**
 * ------------------------------------------------------------
 * Databaseverbinding
 * ------------------------------------------------------------
 * Laad de databaseconfiguratie en maak verbinding via de 
 * Singleton Database-klasse.
 */
require_once __DIR__ . '/../config/database.php';

// Haal de singleton Database-instantie op en verkrijg de verbinding
$db = Database::getInstance();
$conn = $db->getConnection();


/**
 * ------------------------------------------------------------
 * Controllers
 * ------------------------------------------------------------
 * Laad alle controllers die verantwoordelijk zijn voor
 * specifieke onderdelen van de applicatie.
 */
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/CourseController.php';
require_once __DIR__ . '/../controllers/SubWebsiteController.php';
require_once __DIR__ . '/../controllers/FaqController.php';
require_once __DIR__ . '/../controllers/LeerjaarController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/MaterialController.php';
require_once __DIR__ . '/../controllers/NotificatieController.php';
require_once __DIR__ . '/../helpers/auth.php';


/**
 * ------------------------------------------------------------
 * Controller-objecten aanmaken
 * ------------------------------------------------------------
 * Elk controller-object ontvangt de databaseverbinding
 * zodat ze queries kunnen uitvoeren.
 */
$categoryController = new CategoryController($conn);
$courseController   = new CourseController($conn);
$subWebsiteController = new SubWebsiteController($conn);
$faqController = new FaqController($conn);
$leerjaarController = new LeerjaarController($conn);
$userController = new UserController($conn);
$materiaalController = new MaterialController($conn);
$notificatieController = new NotificatieController($conn);

?>
