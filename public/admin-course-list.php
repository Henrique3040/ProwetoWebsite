<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<?php
	require_once __DIR__ . '/../app/core/init.php';
	require_once __DIR__ . '/../app/helpers/auth.php';
	requireAdmin();

	// Haal alle cursussen op
	$coursesTotaal = $courseController->getAllCount();
	$activatedCourses = $courseController->getActivatedCourses();
	$inactiveCourses = $courseController->getInactiveCourses();


	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course_id'])) {
		$courseController->delete($_POST['delete_course_id']);
		header('Location: admin-course-list.php?success=deleted');
		exit;
	}


	$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
	$limit = 10;

	$filters = [
		'search' => $_GET['search'] ?? '',
		'status' => $_GET['status'] ?? '',
		'sort' => $_GET['sort'] ?? ''
	];

	if (isset($_GET['reset'])) {
		header("Location: admin-course-list.php");
		exit;
	}	
	$filteredData = $courseController->getCoursesAdmin($filters, $limit, $page);
	$courses = $filteredData['result'];
	$totalPages = $filteredData['pages'];
	$currentPage = $filteredData['page'];
	$totalCourses = $filteredData['total'];

	?>


	<?php include("partials/head-css.php"); ?>
</head>

<body>

	<!-- **************** MAIN CONTENT START **************** -->
	<main>

		<?php include("partials/sidebar.php"); ?>

		<!-- Page content START -->
		<div class="page-content">

			

			<!-- Page main content START -->
			<div class="page-content-wrapper border">

				<!-- Title -->
				<div class="row mb-3">
					<div class="col-12 d-sm-flex justify-content-between align-items-center">
						<h1 class="h3 mb-2 mb-sm-0">Courses</h1>
						<a href="admin-create-course.php" class="btn btn-sm btn-primary mb-0">Create a Course</a>
					</div>
				</div>

				<!-- Course boxes START -->
				<div class="row g-4 mb-4">

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4">
						<div class="text-center p-4 bg-primary bg-opacity-10 border border-primary rounded-3">
							<h6>Total Courses</h6>
							<h2 class="mb-0 fs-1 text-primary"><?= $coursesTotaal ?></h2>
						</div>
					</div>
					<?php
					$activatedCount = mysqli_num_rows($activatedCourses);
					?>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4">
						<div class="text-center p-4 bg-success bg-opacity-10 border border-success rounded-3">
							<h6>Activated Courses</h6>
							<h2 class="mb-0 fs-1 text-success"><?= $activatedCount ?></h2>
						</div>
					</div>

					<?php
					$inactiveCount = mysqli_num_rows($inactiveCourses);
					?>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4">
						<div class="text-center p-4  bg-warning bg-opacity-15 border border-warning rounded-3">
							<h6>Pending Courses</h6>
							<h2 class="mb-0 fs-1 text-warning"><?= $inactiveCount ?></h2>
						</div>
					</div>
				</div>
				<!-- Course boxes END -->

				<!-- Card START -->
				<div class="card bg-transparent border">

					<!-- Card header START -->
					<div class="card-header bg-light border-bottom">
						<!-- Search and select START -->
						<form class="row g-3 align-items-center justify-content-between" method="GET" action="">
							<div class="col-md-8">
								<input name="search" class="form-control bg-body" type="search"
									placeholder="Search courses..."
									value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
									onchange="this.form.submit()">
							</div>

							<div class="col-md-3 d-flex gap-2">

								<select name="sort" class="form-select border-0 z-index-9"
									onchange="this.form.submit()">
									<option value="">Sort by</option>
									<option value="newest" <?= ($filters['sort'] == 'newest') ? 'selected' : '' ?>>Newest
									</option>
									<option value="oldest" <?= ($filters['sort'] == 'oldest') ? 'selected' : '' ?>>Oldest
									</option>
									<option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Active
									</option>
									<option value="pending" <?= ($filters['status'] == 'pending') ? 'selected' : '' ?>>
										Pending</option>
								</select>

								<button type="submit" name="reset" value="1" class="btn btn-sm btn-primary mb-0">
									Reset
								</button>

							</div>
						</form>

						<!-- Search and select END -->
					</div>
					<!-- Card header END -->

					<!-- Card body START -->
					<div class="card-body">
						<!-- Course table START -->
						<div class="table-responsive border-0 rounded-3">
							<!-- Table START -->
							<table class="table table-dark-gray align-middle p-4 mb-0 table-hover">
								<!-- Table head -->
								<thead>
									<tr>
										<th scope="col" class="border-0 rounded-start">Course Name</th>
										<th scope="col" class="border-0">Categories</th>
										<th scope="col" class="border-0">Added Date</th>
										<th scope="col" class="border-0">Views</th>
										<th scope="col" class="border-0">Status</th>
										<th scope="col" class="border-0 rounded-end">Action</th>
									</tr>
								</thead>

								<!-- Table body START -->
								<tbody>
									<?php if (mysqli_num_rows($courses) > 0): ?>
										<?php while ($course = mysqli_fetch_assoc($courses)): ?>
											<tr>
												<!-- Course Name -->
												<td>
													<div class="d-flex align-items-center position-relative">
														<div class="w-60px">
															<img src="<?= htmlspecialchars($course['FotoURL']) ?>"
																class="rounded" alt="Course image">
														</div>
														<h6 class="table-responsive-title mb-0 ms-2">
															<a href="course-detail.php?id=<?= $course['Id'] ?>"
																class="stretched-link">
																<?= htmlspecialchars($course['Titel']) ?>
															</a>
														</h6>
													</div>
												</td>

												<!-- Categories -->
												<td>
													<?= !empty($course['CategorieNamen']) ? htmlspecialchars($course['CategorieNamen']) : '<em>Geen categorieën</em>' ?>
												</td>

												<!-- Added Date -->
												<td>
													<?= !empty($course['CreatedAt']) ? date("d M Y", strtotime($course['CreatedAt'])) : '-' ?>
												</td>

												<!-- Views -->
												<td><?= (int) $course['Views'] ?></td>

												<!-- Status -->
												<td>
													<?php if ($course['Active'] == 1): ?>
														<span class="badge bg-success bg-opacity-10 text-success">Active</span>
													<?php else: ?>
														<span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
													<?php endif; ?>
												</td>

												<!-- Action -->
												<td>
													<a href="admin-edit-course.php?id=<?= $course['Id'] ?>"
														class="btn btn-sm btn-success me-1 editBtn">Edit</a>
													<form action="admin-course-list.php" method="POST" style="display:inline;">
														<input type="hidden" name="delete_course_id"
															value="<?= $course['Id'] ?>">
														<button type="submit" class="btn btn-sm btn-danger"
															onclick="return confirm('Weet je zeker dat je deze cursus wilt verwijderen?')">
															Delete
														</button>
													</form>

												</td>
											</tr>
										<?php endwhile; ?>
									<?php else: ?>
										<tr>
											<td colspan="6" class="text-center">Geen cursussen gevonden.</td>
										</tr>
									<?php endif; ?>
								</tbody>
								<!-- Table body END -->
							</table>
							<!-- Table END -->
						</div>
						<!-- Course table END -->
					</div>
					<!-- Card body END -->

					<!-- Card footer START -->
					<div class="card-footer bg-transparent pt-0">
						<!-- Pagination START -->
						<div class="d-sm-flex justify-content-sm-between align-items-sm-center">
							<p class="mb-0 text-center text-sm-start">
								Showing <?= (($currentPage - 1) * $limit) + 1 ?>
								to <?= min($currentPage * $limit, $totalCourses) ?>
								of <?= $totalCourses ?> entries
							</p>

							<nav class="d-flex justify-content-center mb-0" aria-label="navigation">
								<ul
									class="pagination pagination-sm pagination-primary-soft d-inline-block d-md-flex rounded mb-0">
									<?php if ($currentPage > 1): ?>
										<li class="page-item mb-0">
											<a class="page-link"
												href="?page=<?= $currentPage - 1 ?>&<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">
												<i class="fas fa-angle-left"></i>
											</a>
										</li>
									<?php endif; ?>

									<?php for ($i = 1; $i <= $totalPages; $i++): ?>
										<li class="page-item mb-0 <?= ($i == $currentPage) ? 'active' : '' ?>">
											<a class="page-link"
												href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
										</li>
									<?php endfor; ?>

									<?php if ($currentPage < $totalPages): ?>
										<li class="page-item mb-0">
											<a class="page-link"
												href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">
												<i class="fas fa-angle-right"></i>
											</a>
										</li>
									<?php endif; ?>
								</ul>
							</nav>
						</div>
						<!-- Pagination END -->
					</div>
					<!-- Card footer END -->
				</div>
				<!-- Card END -->
			</div>
			<!-- Page main content END -->

		</div>
		<!-- Page content END -->

	</main>
	<!-- **************** MAIN CONTENT END **************** -->

	<!-- Back to top -->
	<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>

	<!-- Vendors -->
	<script src="assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>
	<script src="assets/vendor/overlayscrollbars/browser/overlayscrollbars.browser.es6.min.js"></script>


	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>