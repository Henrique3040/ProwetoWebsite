<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<link rel="stylesheet" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<?php
	include_once __DIR__ . '/../app/core/init.php';
	require_once __DIR__ . '/../app/helpers/auth.php';
	requireAdmin();

	// DELETE
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deleteMateriaal') {
		$materiaalController->delete();
		exit;
	}

	// UPDATE
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'updateMateriaal') {
		$materiaalController->update();
	}

	// CREATE
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'createMateriaal') {
		$materiaalController->create();
	}

	$materialen = $materiaalController->getAllMaterials();
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
						<h1 class="h3 mb-2 mb-sm-0">
							Materialen
							<span class="badge bg-orange bg-opacity-10 text-orange"><?= count($materialen) ?></span>
						</h1>
						<button class="btn btn-sm btn-primary" data-bs-toggle="modal"
							data-bs-target="#addMateriaalModal">
							+ Add Material
						</button>
					</div>
				</div>

				<div class="card bg-transparent border">
					<div class="card-header bg-light border-bottom">
						<h5 class="mb-0">All Materials</h5>
					</div>

					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-dark-gray align-middle table-hover">
								<thead>
									<tr>
										<th>Photo</th>
										<th>Naam</th>
										<th>Aangemaakt</th>
										<th>Bijgewerkt</th>
										<th>Acties</th>
									</tr>
								</thead>

								<tbody>
									<?php foreach ($materialen as $m): ?>
										<tr>
											<td>
												<?php if (!empty($m['FotoURL']) && file_exists($m['FotoURL'])): ?>
													<img src="<?= $m['FotoURL'] ?>" class="rounded" width="60">
												<?php else: ?>
													<span class="text-muted">No image</span>
												<?php endif; ?>
											</td>
											<td><?= htmlspecialchars($m['Naam']) ?></td>
											<td><?= $m['CreatedAt'] ?></td>
											<td><?= $m['UpdatedAt'] ?></td>

											<td>
												<button class="btn btn-sm btn-success editBtn" data-id="<?= $m['Id'] ?>"
													data-naam="<?= htmlspecialchars($m['Naam']) ?>"
													data-foto="<?= htmlspecialchars($m['FotoURL']) ?>"
													data-aantal="<?= $m['Aantal'] ?>">
													
													Edit
												</button>

												<button class="btn btn-sm btn-danger deleteBtn" data-id="<?= $m['Id'] ?>">
													Delete
												</button>

												<a href="admin-material-availability.php?id=<?= $m['Id'] ?>"
													class="btn btn-primary btn-sm">
													Open kalender →
												</a>

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

	<!-- ADD Modal -->
	<div class="modal fade" id="addMateriaalModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-dark">
					<h5 class="modal-title text-white">Add Material</h5>
					<button class="btn btn-sm btn-light" data-bs-dismiss="modal">X</button>
				</div>

				<form method="POST" enctype="multipart/form-data">
					<input type="hidden" name="action" value="createMateriaal">

					<div class="modal-body">

						<label class="form-label">Naam</label>
						<input name="naam" class="form-control" required>

						<label class="form-label mt-3">Foto (optional)</label>
						<input type="file" name="foto" class="form-control" accept="image/*">

						<label class="form-label mt-3">Aantal beschikbaar</label>
						<input type="number" name="aantal" class="form-control" min="1" required value="1">

					</div>

					<div class="modal-footer">
						<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button class="btn btn-success">Save</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<!-- EDIT Modal -->
	<div class="modal fade" id="editMateriaalModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-dark">
					<h5 class="modal-title text-white">Edit Material</h5>
					<button class="btn btn-sm btn-light" data-bs-dismiss="modal">X</button>
				</div>

				<form method="POST" enctype="multipart/form-data">
					<input type="hidden" name="action" value="updateMateriaal">
					<input type="hidden" name="id" id="editMateriaalID">

					<div class="modal-body">

						<label class="form-label">Naam</label>
						<input name="naam" id="editMateriaalNaam" class="form-control" required>

						<label class="form-label mt-3">Foto vervangen</label>
						<input type="file" name="foto" class="form-control" accept="image/*">

						<label class="form-label mt-3">Aantal beschikbaar</label>
						<input type="number" name="aantal" id="editMateriaalAantal" class="form-control" min="1"
							required>

						<div class="mt-2">
							<img id="editFotoPreview" src="" width="80" class="rounded d-none">
						</div>

					</div>

					<div class="modal-footer">
						<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button class="btn btn-success">Update</button>
					</div>

				</form>
			</div>
		</div>
	</div>

	<!-- DELETE FORM -->
	<form id="deleteMateriaalForm" method="POST" style="display:none;">
		<input type="hidden" name="action" value="deleteMateriaal">
		<input type="hidden" name="id" id="deleteMateriaalID">
	</form>

	<script src="js/admin-materialen.js"></script>

	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>