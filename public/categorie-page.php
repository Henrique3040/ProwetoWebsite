<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("partials/title-meta.php"); ?>

    <!-- Plugins CSS -->
    <link rel="stylesheet" type="text/css" href="assets/vendor/tiny-slider/tiny-slider.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/glightbox/css/glightbox.min.css">
    <!-- Includes nodige controllers voor gebruik van data -->
    <?php
    require_once __DIR__ . '/../app/core/init.php';

    $categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $courses = $categoryController->getCoursesByCategory($categoryId);
   
	

    ?>

    <?php include("partials/head-css.php"); ?>
</head>

<body>

<?php include("partials/navbar.php"); ?>

<main>

<?php include("partials/home/courses-section.php");?>


</main>

<?php include("partials/footer.php"); ?>

</body>

</html>