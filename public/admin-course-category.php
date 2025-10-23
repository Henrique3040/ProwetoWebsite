<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



	<?php
	include_once __DIR__ . '/../app/core/init.php';

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deleteCategorie') {
		$categoryController->delete();
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'updateCategorie') {
		$categoryController->update();
	}

	$categories = $categoryController->getAllCategories();

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'createCategorie') {
		$categoryController->create();
	}

	?>

	<?php include("partials/head-css.php"); ?>
</head>

<body>

	<!-- **************** MAIN CONTENT START **************** -->
	<main>
		<?php include("partials/sidebar.php"); ?>

		<div class="page-content">
			<?php include("partials/topbar.php"); ?>

			<div class="page-content-wrapper border">

				<div class="row mb-3">
					<div class="col-12 d-sm-flex justify-content-between align-items-center">
						<h1 class="h3 mb-2 mb-sm-0">Categorieën
							<span class="badge bg-orange bg-opacity-10 text-orange"><?= count($categories) ?></span>
						</h1>
						<button class="btn btn-sm btn-primary mb-0" data-bs-toggle="modal"
							data-bs-target="#addCategorieModal">
							+ Create a Category
						</button>
					</div>
				</div>

				<div class="card bg-transparent border">
					<div class="card-header bg-light border-bottom">
						<div class="row g-3 align-items-center justify-content-between">
							<div class="col-md-8">
								<form class="rounded position-relative">
									<input class="form-control bg-body" type="search" placeholder="Search"
										aria-label="Search" id="searchCategory">
									<button
										class="bg-transparent p-2 position-absolute top-50 end-0 translate-middle-y border-0 text-primary-hover text-reset"
										type="button">
										<i class="fas fa-search fs-6 "></i>
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
										<th>Naam</th>
										<th>Aangemaakt op</th>
										<th>Bijgewerkt op</th>
										<th>Acties</th>
									</tr>
								</thead>
								<tbody id="categoryTable">
									<?php foreach ($categories as $categorie): ?>
										<tr>
											<td><i class="fa-solid <?= htmlspecialchars($categorie['Icon']) ?> fa-lg"></i>
											</td>
											<td><?= htmlspecialchars($categorie['Naam']) ?></td>
											<td><?= htmlspecialchars($categorie['CreatedAt']) ?></td>
											<td><?= htmlspecialchars($categorie['UpdatedAt']) ?></td>
											<td>
												<button class="btn btn-sm btn-success me-1 editBtn"
													data-id="<?= $categorie['CategorieID'] ?>"
													data-naam="<?= htmlspecialchars($categorie['Naam']) ?>"
													data-icon="<?= htmlspecialchars($categorie['Icon']) ?>">Edit</button>

												<button class="btn btn-sm btn-danger deleteBtn"
													data-id="<?= $categorie['CategorieID'] ?>">Delete</button>
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

	<!-- **************** MAIN CONTENT END **************** -->

	<!-- Popup modal for add Categorie START -->
	<div class="modal fade" id="addCategorieModal" tabindex="-1" aria-labelledby="addCategorieLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-dark">
					<h5 class="modal-title text-white" id="addCategorieLabel">Add Category</h5>
					<button type="button" class="btn btn-sm btn-light mb-0 ms-auto" data-bs-dismiss="modal">
						<i class="bi bi-x-lg"></i>
					</button>
				</div>
				<div class="modal-body">
					<form id="categorieForm" action="admin-course-category.php" method="POST">
						<input type="hidden" name="action" value="createCategorie">

						<div class="col-12">
							<label class="form-label">Category Name</label>
							<input id="categorieNaam" name="naam" class="form-control" type="text"
								placeholder="Enter category name" required>
						</div>

						<div class="col-12 mt-3">
							<label class="form-label">FontAwesome Icon</label>
							<div class="input-group">
								<span class="input-group-text"><i id="iconPreview"
										class="fas fa-question-circle"></i></span>
								<input id="categorieIcon" name="icon" class="form-control" type="text"
									placeholder="e.g. fa-laptop-code">
							</div>
							<small class="text-muted">
								Example: <code>fa-laptop-code</code>, <code>fa-chart-line</code>,
								<code>fa-graduation-cap</code><br>
								See all icons on <a href="https://fontawesome.com/icons"
									target="_blank">fontawesome.com/icons</a>
							</small>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-danger-soft my-0"
								data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-success my-0">Save Category</button>
						</div>
					</form>

				</div>

			</div>
		</div>
	</div>
	<!-- Popup modal for add Categorie END -->

	<!-- Popup modal for edit Categorie START -->
	<!-- Edit Modal -->
	<div class="modal fade" id="editCategorieModal" tabindex="-1" aria-labelledby="editCategorieLabel"
		aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-dark">
					<h5 class="modal-title text-white" id="editCategorieLabel">Edit Category</h5>
					<button type="button" class="btn btn-sm btn-light mb-0 ms-auto" data-bs-dismiss="modal">
						<i class="bi bi-x-lg"></i>
					</button>
				</div>
				<div class="modal-body">
					<form action="" method="POST" id="editCategorieForm" action="admin-course-category.php" method="POST">
						<input type="hidden" name="action" value="updateCategorie">
						<input type="hidden" name="categorie_id" id="editCategorieID">

						<div class="col-12">
							<label class="form-label">Category Name</label>
							<input name="naam" id="editCategorieNaam" class="form-control" type="text" required>
						</div>

						<div class="col-12 mt-3">
							<label class="form-label">FontAwesome Icon</label>
							<input name="icon" id="editCategorieIcon" class="form-control" type="text">
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-danger-soft my-0"
								data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-success my-0">Update</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- Popup modal for edit Categorie END -->



	<!-- Back to top -->
	<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>

	<!-- Vendors -->
	<script src="assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>
	<script src="assets/vendor/overlayscrollbars/browser/overlayscrollbars.browser.es6.min.js"></script>
	<script src="js/admin-categorie.js"></script>

	<?php include("partials/footer-scripts.php"); ?>

	<!--- delete categorie -->
	<form id="deleteCategorieForm" action="admin-course-category.php" method="POST" style="display:none;">
		<input type="hidden" name="action" value="deleteCategorie">
		<input type="hidden" name="categorie_id" id="deleteCategorieID">
	</form>

</body>

</html>