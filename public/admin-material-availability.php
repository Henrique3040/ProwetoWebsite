<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("partials/title-meta.php"); ?>

    <link rel="stylesheet" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <?php
    include_once __DIR__ . '/../app/core/init.php';
    require_once __DIR__ . '/../app/helpers/auth.php';
    requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($_POST['action'] === 'deleteAvailability') {
            $materiaalController->deleteAvailability($_POST['id']);

        }

        if ($_POST['action'] === 'addAvailability') {
            $materiaalController->addAvailability();
        }
    }

    $materiaalId = $_GET['id'] ?? null;
    if (!$materiaalId)
        die("Geen materiaal geselecteerd.");

    $materiaal = $materiaalController->getMaterialById($materiaalId);
    $beschikbaarheidResponse =$materiaalController->getAllAvailability($materiaalId);

    if (!isset($beschikbaarheidResponse['records']) || !is_array($beschikbaarheidResponse['records'])) {
        $beschikbaarheid = [];
    } else {
        $beschikbaarheid = $beschikbaarheidResponse['records'];
    }


    ?>

    <?php include("partials/head-css.php"); ?>
</head>

<body>

    <main>

        <?php include("partials/sidebar.php"); ?>

        <div class="page-content">
            <div class="page-content-wrapper border">

                <!-- Titel -->
                <div class="row mb-3">
                    <div class="col-12 d-sm-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-2 mb-sm-0">
                            Beschikbaarheid: <?= htmlspecialchars($materiaal['Naam']) ?>
                        </h1>

                        <a href="admin-materialen.php" class="btn btn-secondary btn-sm">← Terug</a>
                    </div>
                </div>

                <!-- Kalender + Lijst -->
                <div class="card bg-transparent border">

                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">Kalender</h5>
                    </div>

                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>

                    <div class="card-body border-top">
                        <h5>Beschikbare dagen</h5>

                        <?php if (empty($beschikbaarheid)): ?>
                            <p class="text-muted">Nog geen beschikbaarheid toegevoegd.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($beschikbaarheid as $b): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <strong><?= $b['startdatum'] ?> - <?= $b['einddatum'] ?></strong>
                                            <span class="badge bg-primary ms-2">
                                                <?= ucfirst($b['periode']) ?>
                                            </span>
                                        </span>

                                        <form method="POST" action="admin-material-availability.php?id=<?= $materiaal['Id'] ?>"
                                            onsubmit="return confirm('Verwijderen?');">
                                            <input type="hidden" name="action" value="deleteAvailability">
                                            <input type="hidden" name="id" value="<?= $b['Id'] ?>">
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Modal: Beschikbaarheid toevoegen -->
    <div class="modal fade" id="availabilityModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <form onsubmit="return false;">


                    <div class="modal-header bg-dark">
                        <h5 class="modal-title text-white">Beschikbaarheid toevoegen</h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">X</button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="materiaal_id" value="<?= $materiaalId ?>">
                        <input type="hidden" name="datum" id="selectedDate">
                        <input type="hidden" name="action" value="addAvailability">

                        <p>
                            Dag: <strong id="dateLabel"></strong>
                        </p>

                    
                        <label class="form-label mt-3">Periode</label>
                        <select name="periode" id="periode" class="form-control">
                            <option value="voormiddag">Voormiddag (08:00 - 12:00)</option>
                            <option value="namiddag">Namiddag (12:00 - 17:00)</option>
                            <option value="avond" selected>Avond (17:00 - 20:00)</option>
                            <option value="hele_dag" selected>Hele dag (08:00 - 20:00)</option>
                        </select>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="saveAvailabilityBtn" class="btn btn-success">Opslaan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        const availabilityData = <?= json_encode($beschikbaarheid) ?>;
        const materialId = "<?= $materiaalId ?>";
    </script>
    <script src="js/admin-material-availability.js"></script>
    <?php include("partials/footer-scripts.php"); ?>


</body>

</html>