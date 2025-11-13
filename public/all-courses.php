<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>
	<?php
	require_once __DIR__ . '/../app/core/init.php';

	$search = isset($_GET['search']) ? trim($_GET['search']) : '';
	$category = isset($_GET['category']) ? trim($_GET['category']) : '';
	$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
	$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;


	$categories = $categoryController->getAllCategories();
	$filters = [
		'search' => $search,
		'status' => '',       // niet nodig bij courses
		'category' => $category,
		'sort' => $sort
	];

	$limit = 8; // ✅ 8 cursussen per pagina
	
	$pagination = $courseController->getFilteredCourses($filters, $limit, $page);

	$courses = iterator_to_array($pagination['result']);
	$totalPages = $pagination['pages'];
	$currentPage = $pagination['page'];
	$totalCourses = $pagination['total'];


	?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">



	<?php include("partials/head-css.php"); ?>
</head>

<body>

	<?php include("partials/navbar.php"); ?>

	<!-- **************** MAIN CONTENT START **************** -->
	<main>

		<!-- =======================
		 Page Banner START -->
		<section class="bg-dark align-items-center d-flex"
			style="background:url(assets/images/pattern/04.png) no-repeat center center; background-size:cover;">
			<!-- Main banner background image -->
			<div class="container">
				<div class="row">
					<div class="col-12">
						<!-- Title -->
						<h1 class="text-white">Course List</h1>
					</div>
				</div>
			</div>
		</section>
		<!-- =======================
Page Banner END -->

		<!-- =======================
Page content START -->
		<section class="pt-5">
			<div class="container">
				<!-- Search option START -->
				<div class="row mb-4 align-items-center">
					<!-- Search bar -->
					<div class="col-sm-6 col-xl-4">
						<form class="border rounded p-2" method="GET" action="">
							<div class="input-group input-borderless">
								<input name="search" class="form-control me-1" type="search" placeholder="Search course"
									value="<?= htmlspecialchars($search ?? '') ?>">
								<button type="submit" class="btn btn-primary mb-0 rounded">
									<i class="fas fa-search"></i>
								</button>
							</div>
						</form>
					</div>

					<!-- Select option -->
					<div class="col-sm-6 col-xl-3 mt-3 mt-lg-0">
						<form class="border rounded p-2 input-borderless" method="GET" action="">
							<select name="category" class="form-select form-select-sm js-choice"
								onchange="this.form.submit()">
								<option value="">All</option>

								<?php foreach ($categories as $cat): ?>
									<option value="<?= htmlspecialchars($cat['Id'], ENT_QUOTES) ?>">
										<?= htmlspecialchars($cat['Naam'], ENT_QUOTES) ?>
									</option>
								<?php endforeach; ?>


							</select>
						</form>
					</div>

					<!-- Select option -->
					<div class="col-sm-6 col-xl-3 mt-3 mt-xl-0">
						<form method="GET" class="border rounded p-2 input-borderless">
							<input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
							<input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">

							<select name="sort" class="form-select form-select-sm js-choice"
								onchange="this.form.submit()">
								<option value="">Sort by</option>
								<option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Most rated</option>
								<option value="views" <?= $sort === 'views' ? 'selected' : '' ?>>Most viewed</option>
								<option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Most recent</option>
							</select>
						</form>
					</div>

					<!-- Button -->
					<div class="col-sm-6 col-xl-2 mt-3 mt-xl-0 d-grid">
						<a href="all-courses.php" class="btn btn-lg btn-primary mb-0">Reset</a>
					</div>
				</div>
				<!-- Search option END -->

				<!-- Course list START -->
				<div class="row g-4 justify-content-center">


					<!-- Card item START -->
					<div class="row g-4 justify-content-center">
						<?php if (!empty($courses)): ?>
							<?php foreach ($courses as $course): ?>
								<div class="col-lg-10 col-xxl-6">
									<div class="card rounded overflow-hidden shadow">
										<div class="row g-0">
											<div class="col-md-4">
												<img src="<?= htmlspecialchars($course['FotoURL']) ?>" class="img-fluid"
													alt="Course image">
											</div>
											<div class="col-md-8">
												<div class="card-body">
													<h5 class="card-title mb-0">
														<a
															href="course-detail.php?id=<?= htmlspecialchars($course['Id'], ENT_QUOTES) ?>">
															<?= htmlspecialchars($course['Titel'], ENT_QUOTES) ?>
														</a>
													</h5>

													<p class="mb-2"><i
															class="fas fa-eye text-success me-1"></i><?= (int) $course['Views'] ?>
														views</p>

														<p class="mb-2"> <i class="fas fa-star text-warning"></i>
														<?= number_format($course['Rating']) ?> Rating</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<p class="text-center text-muted">Geen resultaten gevonden.</p>
						<?php endif; ?>
					</div>

					<!-- Card item END -->

					<!-- Pagination START -->
					<div class="col-12">
						<nav class="mt-4 d-flex justify-content-center" aria-label="navigation">
							<ul class="pagination">
								<?php if ($page > 1): ?>
									<li class="page-item"><a class="page-link"
											href="?search=<?= urlencode($search) ?>&category=<?= $category ?>&sort=<?= $sort ?>&page=<?= $page - 1 ?>">
											&laquo;
										</a></li>
								<?php endif; ?>

								<?php for ($i = 1; $i <= $totalPages; $i++): ?>
									<li class="page-item <?= $i == $page ? 'active' : '' ?>">
										<a class="page-link"
											href="?search=<?= urlencode($search) ?>&category=<?= $category ?>&sort=<?= $sort ?>&page=<?= $i ?>">
											<?= $i ?>
										</a>
									</li>
								<?php endfor; ?>

								<?php if ($page < $totalPages): ?>
									<li class="page-item"><a class="page-link"
											href="?search=<?= urlencode($search) ?>&category=<?= $category ?>&sort=<?= $sort ?>&page=<?= $page + 1 ?>">
											&raquo;
										</a></li>
								<?php endif; ?>
							</ul>

						</nav>
					</div>
					<!-- Pagination END -->

				</div>
		</section>
		<!-- =======================
Page content END -->

		<!-- =======================
Action box START -->
		<section class="pt-0">
			<div class="container position-relative">
				<!-- SVG -->
				<figure class="position-absolute top-50 start-50 translate-middle ms-3">
					<svg>
						<path
							d="m496 22.999c0 10.493-8.506 18.999-18.999 18.999s-19-8.506-19-18.999 8.507-18.999 19-18.999 18.999 8.506 18.999 18.999z"
							fill="#fff" fill-rule="evenodd" opacity=".502" />
						<path
							d="m775 102.5c0 5.799-4.701 10.5-10.5 10.5-5.798 0-10.499-4.701-10.499-10.5 0-5.798 4.701-10.499 10.499-10.499 5.799 0 10.5 4.701 10.5 10.499z"
							fill="#fff" fill-rule="evenodd" opacity=".102" />
						<path
							d="m192 102c0 6.626-5.373 11.999-12 11.999s-11.999-5.373-11.999-11.999c0-6.628 5.372-12 11.999-12s12 5.372 12 12z"
							fill="#fff" fill-rule="evenodd" opacity=".2" />
						<path
							d="m20.499 10.25c0 5.66-4.589 10.249-10.25 10.249-5.66 0-10.249-4.589-10.249-10.249-0-5.661 4.589-10.25 10.249-10.25 5.661-0 10.25 4.589 10.25 10.25z"
							fill="#fff" fill-rule="evenodd" opacity=".2" />
					</svg>
				</figure>

				</div>
			</div>
		</section>
		<!-- =======================
Action box END -->

	</main>
	<!-- **************** MAIN CONTENT END **************** -->

	<!-- =======================
Footer START -->
	<?php include("partials/footer.php"); ?>
	<!-- =======================
Footer END -->

	<!-- Back to top -->
	<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>

	<!-- Vendors -->
	<script src="assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>


	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>