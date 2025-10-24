<?php
include_once __DIR__ . '/../app/core/init.php';

// Verwerk POST-acties
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'createSubWebsite') {
        $subWebsiteController->create();
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'updateSubWebsite') {
        $subWebsiteController->update();
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deleteSubWebsite') {
        $subWebsiteController->delete();
        exit;
    }
}

// Haal alle subwebsites
$subwebsites = $subWebsiteController->index();
$totalSubwebsites = mysqli_num_rows($subwebsites);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("partials/title-meta.php"); ?>
    <link rel="stylesheet" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php include("partials/head-css.php"); ?>
</head>

<body>
<main>
    <?php include("partials/sidebar.php"); ?>
    <div class="page-content">
        <?php include("partials/topbar.php"); ?>

        <div class="page-content-wrapper border">

            <div class="row mb-3">
                <div class="col-12 d-sm-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-2 mb-sm-0">SubWebsites
                        <span class="badge bg-orange bg-opacity-10 text-orange"><?= $totalSubwebsites ?></span>
                    </h1>
                    <button class="btn btn-sm btn-primary mb-0" data-bs-toggle="modal"
                            data-bs-target="#addSubWebsiteModal">
                        + Add SubWebsite
                    </button>
                </div>
            </div>

            <div class="card bg-transparent border">
                <div class="card-header bg-light border-bottom">
                    <div class="row g-3 align-items-center justify-content-between">
                        <div class="col-md-8">
                            <form class="rounded position-relative">
                                <input class="form-control bg-body" type="search" placeholder="Search"
                                       aria-label="Search" id="searchSubWebsite">
                                <button
                                        class="bg-transparent p-2 position-absolute top-50 end-0 translate-middle-y border-0 text-primary-hover text-reset"
                                        type="button">
                                    <i class="fas fa-search fs-6"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive border-0 rounded-3">
                        <table class="table table-dark-gray align-middle p-4 mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Title</th>
                                <th>Link</th>
                                <th>CreatedAt</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="subWebsiteTable">
                            <?php foreach ($subwebsites as $site): ?>
                                <tr>
                                    <td><i class="fa-solid <?= htmlspecialchars($site['Icon']) ?> fa-lg"></i></td>
                                    <td><?= htmlspecialchars($site['Title']) ?></td>
                                    <td><a href="<?= htmlspecialchars($site['Link']) ?>" target="_blank"><?= htmlspecialchars($site['Link']) ?></a></td>
                                    <td><?= htmlspecialchars($site['CreatedAt']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success me-1 editBtn"
                                                data-id="<?= $site['SubWebsiteID'] ?>"
                                                data-title="<?= htmlspecialchars($site['Title']) ?>"
                                                data-link="<?= htmlspecialchars($site['Link']) ?>"
                                                data-icon="<?= htmlspecialchars($site['Icon']) ?>">Edit</button>

                                        <button class="btn btn-sm btn-danger deleteBtn"
                                                data-id="<?= $site['SubWebsiteID'] ?>">Delete</button>
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

<!-- Add SubWebsite Modal -->
<div class="modal fade" id="addSubWebsiteModal" tabindex="-1" aria-labelledby="addSubWebsiteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="addSubWebsiteLabel">Add SubWebsite</h5>
                <button type="button" class="btn btn-sm btn-light mb-0 ms-auto" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="addSubWebsiteForm" action="" method="POST">
                    <input type="hidden" name="action" value="createSubWebsite">
                    <div class="col-12 mb-2">
                        <label class="form-label">Title</label>
                        <input name="title" class="form-control" type="text" placeholder="Enter title" required>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">Link</label>
                        <input name="link" class="form-control" type="url" placeholder="https://example.com" required>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">FontAwesome Icon</label>
                        <input name="icon" class="form-control" type="text" placeholder="e.g. fa-laptop-code">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger-soft my-0" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success my-0">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit SubWebsite Modal -->
<div class="modal fade" id="editSubWebsiteModal" tabindex="-1" aria-labelledby="editSubWebsiteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="editSubWebsiteLabel">Edit SubWebsite</h5>
                <button type="button" class="btn btn-sm btn-light mb-0 ms-auto" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editSubWebsiteForm" action="" method="POST">
                    <input type="hidden" name="action" value="updateSubWebsite">
                    <input type="hidden" name="subwebsite_id" id="editSubWebsiteID">
                    <div class="col-12 mb-2">
                        <label class="form-label">Title</label>
                        <input name="title" id="editSubWebsiteTitle" class="form-control" type="text" required>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">Link</label>
                        <input name="link" id="editSubWebsiteLink" class="form-control" type="url" required>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">FontAwesome Icon</label>
                        <input name="icon" id="editSubWebsiteIcon" class="form-control" type="text">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger-soft my-0" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success my-0">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete form -->
<form id="deleteSubWebsiteForm" action="" method="POST" style="display:none;">
    <input type="hidden" name="action" value="deleteSubWebsite">
    <input type="hidden" name="subwebsite_id" id="deleteSubWebsiteID">
</form>

<script src="js/subwebsites.js"></script>

<?php include("partials/footer-scripts.php"); ?>
</body>
</html>
