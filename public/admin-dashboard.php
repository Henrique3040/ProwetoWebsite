<!DOCTYPE html>
<html lang="en">
<head>
	<?php include("partials/title-meta.php"); ?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/apexcharts/apexcharts.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">

	<?php include("partials/head-css.php"); ?>
	<?php
	require_once __DIR__ . '/../app/core/init.php';
	require_once __DIR__ . '/../app/helpers/auth.php'; 
    requireAdmin();

	$coursesTotaal = $courseController->getAllCourses();
	$activatedCourses = $courseController->getActivatedCourses();
	$inactiveCourses = $courseController->getInactiveCourses();
	$subwebsitesTotaal = $subWebsiteController->index();
	$categorieTotaal = $categoryController->getAllCategories();
	$latestCourses = $courseController->getLatestUpdatedCourses(5);

	?>
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
				<div class="row">
					<div class="col-12 mb-3">
						<h1 class="h3 mb-2 mb-sm-0">Dashboard</h1>
					</div>
				</div>

				<!-- Counter boxes START -->
				<div class="row g-4 mb-4">
					<!-- Counter item -->
					<div class="col-md-6 col-xxl-3">
						<div class="card card-body bg-warning bg-opacity-15 p-4 h-100">
							<div class="d-flex justify-content-between align-items-center">
								<!-- Digit -->
								<div>
									<?php $inactiveCoursesCount = mysqli_num_rows($inactiveCourses); ?>
									<h2 class="purecounter mb-0 fw-bold" data-purecounter-start="0"
										data-purecounter-end="<?= $inactiveCoursesCount ?>" data-purecounter-delay="200">0
									</h2>
									<span class="mb-0 h6 fw-light">Inactive Courses</span>
								</div>
								<!-- Icon -->
								<div class="icon-lg rounded-circle bg-warning text-white mb-0"><i
										class="fa-solid fa-exclamation"></i></i></div>
							</div>
						</div>
					</div>

					<!-- Counter item -->
					<div class="col-md-6 col-xxl-3">
						<div class="card card-body bg-success bg-opacity-10 p-4 h-100">
							<div class="d-flex justify-content-between align-items-center">
								<!-- Digit -->
								<div>
									<?php $activatedCoursesCount = mysqli_num_rows($activatedCourses); ?>
									<div class="d-flex">
										<h2 class="purecounter mb-0 fw-bold" data-purecounter-start="0"
											data-purecounter-end=<?= $activatedCoursesCount ?>
											data-purecounter-delay="200">0</h2>

									</div>
									<span class="mb-0 h6 fw-light">Active Courses</span>
								</div>
								<!-- Icon -->
								<div class="icon-lg rounded-circle bg-success text-white mb-0"><i
										class="fa-solid fa-plug"></i></div>
							</div>
						</div>
					</div>

					<!-- Counter item -->
					<div class="col-md-6 col-xxl-3">
						<div class="card card-body bg-primary bg-opacity-10 p-4 h-100">
							<div class="d-flex justify-content-between align-items-center">
								<!-- Digit -->
								<div>
									<?php $categorieTotaalCount = count($categorieTotaal); ?>
									<h2 class="purecounter mb-0 fw-bold" data-purecounter-start="0"
										data-purecounter-end=<?= $categorieTotaalCount ?> data-purecounter-delay="200">0
									</h2>
									<span class="mb-0 h6 fw-light">Totaal Categorie</span>
								</div>
								<!-- Icon -->
								<div class="icon-lg rounded-circle bg-primary text-white mb-0"><i
										class="fa-solid fa-list"></i></div>
							</div>
						</div>
					</div>


					<!-- Counter item -->
					<div class="col-md-6 col-xxl-3">
						<div class="card card-body bg-purple bg-opacity-10 p-4 h-100">
							<div class="d-flex justify-content-between align-items-center">
								<!-- Digit -->
								<div>
									<?php $subwebsitesTotaalCount = mysqli_num_rows($subwebsitesTotaal); ?>
									<h2 class="purecounter mb-0 fw-bold" data-purecounter-start="0"
										data-purecounter-end=<?= $subwebsitesTotaalCount ?> data-purecounter-delay="200">
										0</h2>
									<span class="mb-0 h6 fw-light">SubWebsite</span>
								</div>
								<!-- Icon -->
								<div class="icon-lg rounded-circle bg-purple text-white mb-0"><i
										class="fa-solid fa-layer-group"></i></div>
							</div>
						</div>
					</div>


				</div>
				<!-- Counter boxes END -->

				<!-- Chart and Ticket START -->
				<div class="row g-4 mb-4">

					<!-- Chart START -->
					<div class="col-xxl-8">
						<div class="card shadow h-100">

							<!-- Card header -->
							<div class="card-header p-4 border-bottom">
								<h5 class="card-header-title">Earnings</h5>
							</div>

							<!-- Card body -->
							<div class="card-body">
								<!-- Apex chart -->
								<div id="ChartPayout"></div>
							</div>
						</div>
					</div>
					<!-- Chart END -->

					<!-- Ticket START -->
					<div class="col-xxl-4">
						<div class="card shadow h-100">
							<!-- Card header -->
							<div
								class="card-header border-bottom d-flex justify-content-between align-items-center p-4">
								<h5 class="card-header-title">Support Requests</h5>
								<a href="admin-course-list.php" class="btn btn-link p-0 mb-0">All courses</a>
							</div>

							<!-- Card body START -->
							<div class="card-body p-4">
								<?php while ($course = mysqli_fetch_assoc($latestCourses)): ?>
									<div class="d-flex justify-content-between position-relative mb-3">
										<div class="d-sm-flex">
											<div class="avatar avatar-md flex-shrink-0">
												<?php if (!empty($course['FotoURL'])): ?>
													<img class="avatar-img rounded-circle"
														src="<?= htmlspecialchars($course['FotoURL']) ?>" alt="course">
												<?php else: ?>
													<div class="avatar-img rounded-circle bg-secondary bg-opacity-10">
														<span
															class="text-secondary position-absolute top-50 start-50 translate-middle fw-bold">
															<?= strtoupper(substr($course['Titel'], 0, 2)) ?>
														</span>
													</div>
												<?php endif; ?>
											</div>
											<div class="ms-2 mt-1">
												<h6 class="mb-0">
													<a href="admin-edit-course.php?id=<?= $course['Id'] ?>"
														class="stretched-link">
														<?= htmlspecialchars($course['Titel']) ?>
													</a>
												</h6>
												<p class="mb-0 text-muted small">
													<?= $course['Active'] ? 'Active' : 'Pending' ?>
												</p>
												<span class="small text-muted">
													<?= date('d M Y, H:i', strtotime($course['LaatstBijgewerkt'] ?? $course['CreatedAt'])) ?>
												</span>
											</div>
										</div>
									</div>
									<hr>
								<?php endwhile; ?>
							</div>

						</div>
					</div>
					<!-- Ticket END -->
				</div>
				<!-- Chart and Ticket END -->
			</div>
			<!-- Page main content END -->
		</div>
		<!-- Page content END -->

	</main>
	<!-- **************** MAIN CONTENT END **************** -->

	<!-- Back to top -->
	<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>

	<!-- Vendors -->
	<script src="assets/vendor/@srexi/purecounterjs/purecounter_vanilla.js"></script>
	<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
	<script src="assets/vendor/overlayscrollbars/browser/overlayscrollbars.browser.es6.min.js"></script>

	<?php include("partials/footer-scripts.php"); ?>


</body>

</html>