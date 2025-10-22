<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">

	<?php
	require_once __DIR__ . '/../app/core/init.php';

	// Haal alle cursussen op
	$courses = $courseController->getAllCourses();

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course_id'])) {
		$courseController->delete((int) $_POST['delete_course_id']);
		header('Location: admin-course-list.php?success=deleted');
		exit;
	}
	
	?>


	<?php include("partials/head-css.php"); ?>
</head>

<body>

	<!-- **************** MAIN CONTENT START **************** -->
	<main>

		<?php include("partials/sidebar.php"); ?>

		<!-- Page content START -->
		<div class="page-content">

			<?php include("partials/topbar.php"); ?>

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
					<?php
					$totalCourses = mysqli_num_rows($courses);
					mysqli_data_seek($courses, 0); // reset pointer voor de while-loop hierboven
					?>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4">
						<div class="text-center p-4 bg-primary bg-opacity-10 border border-primary rounded-3">
							<h6>Total Courses</h6>
							<h2 class="mb-0 fs-1 text-primary"><?= $totalCourses ?></h2>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4">
						<div class="text-center p-4 bg-success bg-opacity-10 border border-success rounded-3">
							<h6>Activated Courses</h6>
							<h2 class="mb-0 fs-1 text-success">996</h2>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4">
						<div class="text-center p-4  bg-warning bg-opacity-15 border border-warning rounded-3">
							<h6>Pending Courses</h6>
							<h2 class="mb-0 fs-1 text-warning">200</h2>
						</div>
					</div>
				</div>
				<!-- Course boxes END -->

				<!-- Card START -->
				<div class="card bg-transparent border">

					<!-- Card header START -->
					<div class="card-header bg-light border-bottom">
						<!-- Search and select START -->
						<div class="row g-3 align-items-center justify-content-between">
							<!-- Search bar -->
							<div class="col-md-8">
								<form class="rounded position-relative">
									<input class="form-control bg-body" type="search" placeholder="Search"
										aria-label="Search">
									<button
										class="bg-transparent p-2 position-absolute top-50 end-0 translate-middle-y border-0 text-primary-hover text-reset"
										type="submit">
										<i class="fas fa-search fs-6 "></i>
									</button>
								</form>
							</div>

							<!-- Select option -->
							<div class="col-md-3">
								<!-- Short by filter -->
								<form>
									<select class="form-select js-choice border-0 z-index-9"
										aria-label=".form-select-sm">
										<option value="">Sort by</option>
										<option>Newest</option>
										<option>Oldest</option>
										<option>Accepted</option>
										<option>Rejected</option>
									</select>
								</form>
							</div>
						</div>
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
															<a href="course-detail.php?id=<?= $course['CursusID'] ?>"
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
													<span class="badge bg-success bg-opacity-10 text-success">Active</span>
												</td>

												<!-- Action -->
												<td>
													<a href="admin-edit-course.php?id=<?= $course['CursusID'] ?>"
														class="btn btn-sm btn-dark me-1">Edit</a>
													<form action="admin-course-list.php" method="POST" style="display:inline;">
														<input type="hidden" name="delete_course_id"
															value="<?= $course['CursusID'] ?>">
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
							<!-- Content -->
							<p class="mb-0 text-center text-sm-start">Showing 1 to 8 of 20 entries</p>
							<!-- Pagination -->
							<nav class="d-flex justify-content-center mb-0" aria-label="navigation">
								<ul
									class="pagination pagination-sm pagination-primary-soft d-inline-block d-md-flex rounded mb-0">
									<li class="page-item mb-0"><a class="page-link" href="#" tabindex="-1"><i
												class="fas fa-angle-left"></i></a></li>
									<li class="page-item mb-0"><a class="page-link" href="#">1</a></li>
									<li class="page-item mb-0 active"><a class="page-link" href="#">2</a></li>
									<li class="page-item mb-0"><a class="page-link" href="#">3</a></li>
									<li class="page-item mb-0"><a class="page-link" href="#"><i
												class="fas fa-angle-right"></i></a></li>
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