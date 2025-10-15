<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<?php
	require_once __DIR__ . '/../app/core/init.php';
	// Haal alle categorieën op
	$categories = $categoryController->index();

	?>


	<?php include("partials/head-css.php"); ?>
</head>

<body>

	<?php include("partials/navbar.php"); ?>

	<!-- **************** MAIN CONTENT START **************** -->
	<main>

		<!-- =======================
Page Banner START -->
		<section class="bg-light position-relative">

			<!-- Svg decoration -->
			<figure class="position-absolute bottom-0 start-0 d-none d-lg-block">
				<svg width="822.2px" height="301.9px" viewBox="0 0 822.2 301.9">
					<path class="fill-warning opacity-5"
						d="M752.5,51.9c-4.5,3.9-8.9,7.8-13.4,11.8c-51.5,45.3-104.8,92.2-171.7,101.4c-39.9,5.5-80.2-3.4-119.2-12.1 c-32.3-7.2-65.6-14.6-98.9-13.9c-66.5,1.3-128.9,35.2-175.7,64.6c-11.9,7.5-23.9,15.3-35.5,22.8c-40.5,26.4-82.5,53.8-128.4,70.7 c-2.1,0.8-4.2,1.5-6.2,2.2L0,301.9c3.3-1.1,6.7-2.3,10.2-3.5c46.1-17,88.1-44.4,128.7-70.9c11.6-7.6,23.6-15.4,35.4-22.8 c46.7-29.3,108.9-63.1,175.1-64.4c33.1-0.6,66.4,6.8,98.6,13.9c39.1,8.7,79.6,17.7,119.7,12.1C634.8,157,688.3,110,740,64.6 c4.5-3.9,9-7.9,13.4-11.8C773.8,35,797,16.4,822.2,1l-0.7-1C796.2,15.4,773,34,752.5,51.9z">
					</path>
				</svg>
			</figure>

			<div class="container position-relative">
				<div class="row">
					<div class="col-12">
						<div class="row align-items-center">

							<!-- Image -->
							<div class="col-6 col-md-3 text-center order-1">
								<img src="assets/images/element/category-1.svg" alt="">
							</div>

							<!-- Content -->
							<div class="col-md-6 px-md-5 text-center position-relative order-md-2 mb-5 mb-md-0">

								<!-- Svg decoration -->
								<figure class="position-absolute top-0 start-0">
									<svg width="22px" height="22px" viewBox="0 0 22 22">
										<polygon class="fill-orange"
											points="22,8.3 13.7,8.3 13.7,0 8.3,0 8.3,8.3 0,8.3 0,13.7 8.3,13.7 8.3,22 13.7,22 13.7,13.7 22,13.7 ">
										</polygon>
									</svg>
								</figure>

								<!-- Svg decoration -->
								<figure
									class="position-absolute top-0 start-50 translate-middle mt-n6 d-none d-md-block">
									<svg width="27px" height="27px">
										<path class="fill-purple"
											d="M13.122,5.946 L17.679,-0.001 L17.404,7.528 L24.661,5.946 L19.683,11.533 L26.244,15.056 L18.891,16.089 L21.686,23.068 L15.400,19.062 L13.122,26.232 L10.843,19.062 L4.557,23.068 L7.352,16.089 L-0.000,15.056 L6.561,11.533 L1.582,5.946 L8.839,7.528 L8.565,-0.001 L13.122,5.946 Z">
										</path>
									</svg>
								</figure>


								<!-- Title -->
								<h1 class="mb-3">What do you want to learn?</h1>
								<p class="mb-3">Grow your skill with the most reliable online courses and certifications
								</p>

								<!-- Search -->
								<form class="bg-body rounded p-2" method="GET" action="search.php">
									<div class="input-group">
										<input class="form-control border-0 me-1" type="search" name="q"
											placeholder="Search course " required>
										<button type="submit" class="btn btn-dark mb-0 rounded">Search</button>
									</div>
								</form>
							</div>

							<!-- Image -->
							<div class="col-6 col-md-3 text-center order-3">
								<img src="assets/images/element/category-2.svg" alt="">
							</div>

						</div> <!-- Row END -->
					</div>
				</div> <!-- Row END -->
			</div>
		</section>
		<!-- =======================
Page Banner END -->

		<!-- =======================
Categories START -->
		<section>
			<div class="container">
				<div class="row mb-4">
					<div class="col-lg-8 mx-auto text-center">
						<h2>Choose a Category</h2>
						<p class="mb-0">Perceived end knowledge certainly day sweetness why cordially</p>
					</div>
				</div>

				<div class="row g-4">
					<?php
					if ($categories && mysqli_num_rows($categories) > 0):
						$colors = ['success', 'orange', 'danger', 'purple', 'info', 'blue', 'warning', 'dark', 'primary'];
						$icons = [
							'data-science.svg',
							'online.svg',
							'engineering.svg',
							'coding.svg',
							'profit.svg',
							'medical.svg',
							'home.svg',
							'artist.svg',
							'marketing.svg',
							'account.svg'
						];

						$i = 0;
						while ($cat = mysqli_fetch_assoc($categories)):
							$name = htmlspecialchars($cat['Naam']);
							$id = (int) $cat['CategorieID'];

							$color = $colors[$i % count($colors)];
							$icon = $icons[$i % count($icons)];
							$i++;
							?>
							<div class="col-sm-6 col-md-4 col-xl-3">
								<div
									class="card card-body bg-<?= $color ?> bg-opacity-10 text-center position-relative btn-transition p-4">
									<div class="icon-xl bg-body mx-auto rounded-circle mb-3">
										<img src="assets/images/element/<?= $icon ?>" alt="<?= $name ?>">
									</div>
									<h5 class="mb-2">
										<a href="categorie-page.php?id=<?= $id ?>" class="stretched-link"><?= $name ?></a>
									</h5>
									<!-- Optional: show course count if you have it -->
									<h6 class="mb-0">
										<?php
											
										 echo $cat['TotalCourses'] ?? '0'; 
										?>
									</h6>
								</div>
							</div>
						<?php endwhile;
					else: ?>
						<div class="col-12 text-center">
							<p>No categories found.</p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<!-- =======================
Categories END -->

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
	<script src="assets/vendor/tiny-slider/min/tiny-slider.js"></script>

	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>