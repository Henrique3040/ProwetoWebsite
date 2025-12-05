<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("partials/title-meta.php"); ?>

    <link rel="stylesheet" type="text/css" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <?php
    include_once __DIR__ . '/../app/core/init.php';
    require_once __DIR__ . '/../app/helpers/auth.php';
    requireAdmin();

    // Load controllers
    $reservations = $materiaalController->getAllReservations();

    // Handle delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deleteReservation') {
        $materiaalController->deleteReservation($_POST['id']);
        header("Location: admin-material-reservaties.php");
        exit;
    }

    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'updateStatus') {
        $materiaalController->updateReservationStatus($_POST['id'], $_POST['status']);
        header("Location: admin-material-reservaties.php");
        exit;
    }
    ?>

    <?php include("partials/head-css.php"); ?>
</head>

<body>

    <main>
        <?php include("partials/sidebar.php"); ?>

        <div class="page-content">
            <div class="page-content-wrapper border">

                <div class="row mb-3">
                    <div class="col-12 d-sm-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-2 mb-sm-0">Materiaalreservaties
                            <span class="badge bg-orange bg-opacity-10 text-orange"><?= count($reservations) ?></span>
                        </h1>
                    </div>

                    <a href="admin-export-reservaties.php" class="btn btn-primary">
                        <i class="bi bi-file-earmark-excel"></i> Exporteren naar Excel
                    </a>

                </div>

                <div class="card bg-transparent border">
                    <div class="card-header bg-light border-bottom">
                        <div class="row g-3 align-items-center justify-content-between">
                            <div class="col-12 col-md-6">
                                <h5 class="mb-0">Alle reservaties</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive border-0 rounded-3">
                            <table class="table table-dark-gray align-middle p-4 mb-0 table-hover">
                                <thead>
                                    <tr>
                                        <th>Gebruiker</th>
                                        <th>Materiaal</th>
                                        <th>Cursus</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Aangemaakt</th>
                                        <th>Acties</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($reservations as $r): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($r['username']) ?></td>
                                            <td><?= htmlspecialchars($r['materiaal_naam']) ?></td>
                                            <td><?= htmlspecialchars($r['cursus_titel']) ?></td>
                                            <td><?= $r['startdatum'] ?> → <?= $r['einddatum'] ?></td>

                                            <td>
                                                <span class="badge
                                            <?php if ($r['status'] === 'pending')
                                                echo 'bg-warning';
                                            elseif ($r['status'] === 'approved')
                                                echo 'bg-success';
                                            elseif ($r['status'] === 'rejected')
                                                echo 'bg-danger';
                                            else
                                                echo 'bg-secondary'; ?>">
                                                    <?= ucfirst($r['status']) ?>
                                                </span>
                                            </td>

                                            <td><?= $r['aangemaakt_op'] ?></td>

                                            <td>
                                                <button class="btn btn-sm btn-success editStatusBtn"
                                                    data-id="<?= $r['Id'] ?>" data-status="<?= $r['status'] ?>">
                                                    Status
                                                </button>

                                                <button class="btn btn-sm btn-danger deleteReservationBtn"
                                                    data-id="<?= $r['Id'] ?>">
                                                    Delete
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>


    <!-- UPDATE STATUS MODAL -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white">Status wijzigen</h5>
                    <button type="button" class="btn btn-light btn-sm ms-auto" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="updateStatus">
                    <input type="hidden" name="id" id="editStatusId">

                    <div class="modal-body">
                        <label class="form-label">Nieuwe status</label>
                        <select name="status" id="editStatusValue" class="form-control">
                            <option value="in_afwachting">In afwachting</option>
                            <option value="goedgekeurd">Goedgekeurd</option>
                            <option value="geweigerd">Geweigerd</option>
                            <option value="afgerond">Afgerond</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger-soft" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success">Opslaan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- HIDDEN DELETE FORM -->
    <form id="deleteReservationForm" method="POST" style="display:none;">
        <input type="hidden" name="action" value="deleteReservation">
        <input type="hidden" name="id" id="deleteReservationID">
    </form>


    <script src="js/material-reservaties.js"></script>


    <?php include("partials/footer-scripts.php"); ?>

</body>

</html>