<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("partials/title-meta.php"); ?>

    <!-- Plugins CSS -->
    <link rel="stylesheet" type="text/css" href="assets/vendor/tiny-slider/tiny-slider.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/glightbox/css/glightbox.min.css">
    <?php
    require_once __DIR__ . '/../app/core/init.php';


    $query = isset($_GET['q']) ? trim($_GET['q']) : '';

    $courses = [];

    if ($query !== '') {
        $courses = $courseController->searchCourses($query);
    }
    ?>

    <?php include("partials/head-css.php"); ?>

</head>

<body>
    <?php include("partials/navbar.php"); ?>
    <main>
        <?php include("partials/home/courses-section.php"); ?>
    </main>
    <?php include("partials/footer.php"); ?>

</body>

</html>