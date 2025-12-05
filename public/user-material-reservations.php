<?php
include_once __DIR__ . '/../app/core/init.php';
require_once __DIR__ . '/../app/helpers/auth.php';
requireLogin(); // user moet ingelogd zijn

$userId = $_SESSION['user']['id'];

// handle delete (POST)
$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteReservation') {
    $reservationId = $_POST['id'] ?? null;
    if ($reservationId) {
        $res = $materiaalController->deleteReservationForUser($reservationId, $userId);
        if ($res['success']) {
            $flash = ['type' => 'success', 'msg' => 'Reservatie verwijderd.'];
        } else {
            $flash = ['type' => 'danger', 'msg' => 'Kon niet verwijderen: ' . ($res['reason'] ?? 'unknown')];
        }
    }
}

// haal alle reservaties van user
$reservations = $materiaalController->getUserReservations($userId);
?>

<!doctype html>
<html lang="nl">
<head>
    <?php include("partials/title-meta.php"); ?>
    <?php include("partials/head-css.php"); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include("partials/navbar.php"); ?>

<main class="container py-4">
    <h1>Mijn reservaties</h1>

    <?php if ($flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($reservations)): ?>
                <p class="text-muted">Je hebt nog geen reservaties.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
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
                            <?php
                                $canDelete = in_array($r['status'], ['goedgekeurd', 'afgerond'], true);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($r['materiaal_naam'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($r['cursus_titel'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($r['startdatum']) ?> → <?= htmlspecialchars($r['einddatum']) ?></td>
                                <td>
                                    <span class="badge <?= $r['status'] === 'in_afwachting' ? 'bg-warning' : ($r['status'] === 'goedgekeurd' ? 'bg-success' : ($r['status'] === 'geweigerd' ? 'bg-danger' : 'bg-secondary')) ?>">
                                        <?= htmlspecialchars(ucfirst($r['status'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($r['aangemaakt_op']) ?></td>
                                <td>
                                    <?php if ($canDelete): ?>
                                        <form method="POST" class="d-inline deleteReservationForm">
                                            <input type="hidden" name="action" value="deleteReservation">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($r['Id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-danger confirm-delete-btn">Verwijder</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Verwijder</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
$(document).ready(function(){
    // bevestig voordat formulier verstuurd wordt
    $('.deleteReservationForm').on('submit', function(e){
        e.preventDefault();
        const ok = confirm('Weet je zeker dat je deze reservatie wilt verwijderen? Dit kan niet ongedaan gemaakt worden.');
        if (!ok) return;
        // simpele submit
        this.submit();
    });
});
</script>

<?php include("partials/footer-scripts.php"); ?>
<script src="js/emailNotificaties.js"></script>
</body>
</html>
