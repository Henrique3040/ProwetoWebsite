<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/tiny-slider/tiny-slider.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/glightbox/css/glightbox.min.css">

	<!-- Includes nodige controllers voor gebruik van data -->
	<?php
	include '../app/config/database.php';
	require_once __DIR__ . '/../app/controllers/CategoryController.php';
	require_once __DIR__ . '/../app/controllers/CourseController.php';

	$categoryController = new CategoryController($conn);
	$courseController = new CourseController($conn);

	$categories = $categoryController->getAllWithCourses();
	$result = $categoryController->index();
	$courses = $courseController->featured(8);

	
	?>

	<?php include("partials/head-css.php"); ?>
</head>

<body>

	<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	?>


	<!-- Header START -->
	<?php include("partials/header.php"); ?>
	<!-- Header END -->

	<!-- **************** MAIN CONTENT START **************** -->
	<main>

		<!-- =======================
Main Banner START -->
		<section class="bg-light">
			<div class="container pt-5 mt-0 mt-lg-5">

				<!-- Title and SVG START -->
				<div class="row position-relative mb-0 mb-sm-5 pb-0 pb-lg-5">
					<div class="col-lg-8 text-center mx-auto position-relative">

						<!-- SVG decoration -->
						<figure
							class="position-absolute top-100 start-50 translate-middle mt-4 ms-n9 pe-5 d-none d-lg-block">
							<svg>
								<path class="fill-success"
									d="m181.6 6.7c-0.1 0-0.2-0.1-0.3 0-2.5-0.3-4.9-1-7.3-1.4-2.7-0.4-5.5-0.7-8.2-0.8-1.4-0.1-2.8-0.1-4.1-0.1-0.5 0-0.9-0.1-1.4-0.2-0.9-0.3-1.9-0.1-2.8-0.1-5.4 0.2-10.8 0.6-16.1 1.4-2.7 0.3-5.3 0.8-7.9 1.3-0.6 0.1-1.1 0.3-1.8 0.3-0.4 0-0.7-0.1-1.1-0.1-1.5 0-3 0.7-4.3 1.2-3 1-6 2.4-8.8 3.9-2.1 1.1-4 2.4-5.9 3.9-1 0.7-1.8 1.5-2.7 2.2-0.5 0.4-1.1 0.5-1.5 0.9s-0.7 0.8-1.1 1.2c-1 1-1.9 2-2.9 2.9-0.4 0.3-0.8 0.5-1.2 0.5-1.3-0.1-2.7-0.4-3.9-0.6-0.7-0.1-1.2 0-1.8 0-3.1 0-6.4-0.1-9.5 0.4-1.7 0.3-3.4 0.5-5.1 0.7-5.3 0.7-10.7 1.4-15.8 3.1-4.6 1.6-8.9 3.8-13.1 6.3-2.1 1.2-4.2 2.5-6.2 3.9-0.9 0.6-1.7 0.9-2.6 1.2s-1.7 1-2.5 1.6c-1.5 1.1-3 2.1-4.6 3.2-1.2 0.9-2.7 1.7-3.9 2.7-1 0.8-2.2 1.5-3.2 2.2-1.1 0.7-2.2 1.5-3.3 2.3-0.8 0.5-1.7 0.9-2.5 1.5-0.9 0.8-1.9 1.5-2.9 2.2 0.1-0.6 0.3-1.2 0.4-1.9 0.3-1.7 0.2-3.6 0-5.3-0.1-0.9-0.3-1.7-0.8-2.4s-1.5-1.1-2.3-0.8c-0.2 0-0.3 0.1-0.4 0.3s-0.1 0.4-0.1 0.6c0.3 3.6 0.2 7.2-0.7 10.7-0.5 2.2-1.5 4.5-2.7 6.4-0.6 0.9-1.4 1.7-2 2.6s-1.5 1.6-2.3 2.3c-0.2 0.2-0.5 0.4-0.6 0.7s0 0.7 0.1 1.1c0.2 0.8 0.6 1.6 1.3 1.8 0.5 0.1 0.9-0.1 1.3-0.3 0.9-0.4 1.8-0.8 2.7-1.2 0.4-0.2 0.7-0.3 1.1-0.6 1.8-1 3.8-1.7 5.8-2.3 4.3-1.1 9-1.1 13.3 0.1 0.2 0.1 0.4 0.1 0.6 0.1 0.7-0.1 0.9-1 0.6-1.6-0.4-0.6-1-0.9-1.7-1.2-2.5-1.1-4.9-2.1-7.5-2.7-0.6-0.2-1.3-0.3-2-0.4-0.3-0.1-0.5 0-0.8-0.1s-0.9 0-1.1-0.1-0.3 0-0.3-0.2c0-0.4 0.7-0.7 1-0.8 0.5-0.3 1-0.7 1.5-1l5.4-3.6c0.4-0.2 0.6-0.6 1-0.9 1.2-0.9 2.8-1.3 4-2.2 0.4-0.3 0.9-0.6 1.3-0.9l2.7-1.8c1-0.6 2.2-1.2 3.2-1.8 0.9-0.5 1.9-0.8 2.7-1.6 0.9-0.8 2.2-1.4 3.2-2 1.2-0.7 2.3-1.4 3.5-2.1 4.1-2.5 8.2-4.9 12.7-6.6 5.2-1.9 10.6-3.4 16.2-4 5.4-0.6 10.8-0.3 16.2-0.5h0.5c1.4-0.1 2.3-0.1 1.7 1.7-1.4 4.5 1.3 7.5 4.3 10 3.4 2.9 7 5.7 11.3 7.1 4.8 1.6 9.6 3.8 14.9 2.7 3-0.6 6.5-4 6.8-6.4 0.2-1.7 0.1-3.3-0.3-4.9-0.4-1.4-1-3-2.2-3.9-0.9-0.6-1.6-1.6-2.4-2.4-0.9-0.8-1.9-1.7-2.9-2.3-2.1-1.4-4.2-2.6-6.5-3.5-3.2-1.3-6.6-2.2-10-3-0.8-0.2-1.6-0.4-2.5-0.5-0.2 0-1.3-0.1-1.3-0.3-0.1-0.2 0.3-0.4 0.5-0.6 0.9-0.8 1.8-1.5 2.7-2.2 1.9-1.4 3.8-2.8 5.8-3.9 2.1-1.2 4.3-2.3 6.6-3.2 1.2-0.4 2.3-0.8 3.6-1 0.6-0.2 1.2-0.2 1.8-0.4 0.4-0.1 0.7-0.3 1.1-0.5 1.2-0.5 2.7-0.5 3.9-0.8 1.3-0.2 2.7-0.4 4.1-0.7 2.7-0.4 5.5-0.8 8.2-1.1 3.3-0.4 6.7-0.7 10-1 7.7-0.6 15.3-0.3 23 1.3 4.2 0.9 8.3 1.9 12.3 3.6 1.2 0.5 2.3 1.1 3.5 1.5 0.7 0.2 1.3 0.7 1.8 1.1 0.7 0.6 1.5 1.1 2.3 1.7 0.2 0.2 0.6 0.3 0.8 0.2 0.1-0.1 0.1-0.2 0.2-0.4 0.1-0.9-0.2-1.7-0.7-2.4-0.4-0.6-1-1.4-1.6-1.9-0.8-0.7-2-1.1-2.9-1.6-1-0.5-2-0.9-3.1-1.3-2.5-1.1-5.2-2-7.8-2.8-1-0.8-2.4-1.2-3.7-1.4zm-64.4 25.8c4.7 1.3 10.3 3.3 14.6 7.9 0.9 1 2.4 1.8 1.8 3.5-0.6 1.6-2.2 1.5-3.6 1.7-4.9 0.8-9.4-1.2-13.6-2.9-4.5-1.7-8.8-4.3-11.9-8.3-0.5-0.6-1-1.4-1.1-2.2 0-0.3 0-0.6-0.1-0.9s-0.2-0.6 0.1-0.9c0.2-0.2 0.5-0.2 0.8-0.2 2.3-0.1 4.7 0 7.1 0.4 0.9 0.1 1.6 0.6 2.5 0.8 1.1 0.4 2.3 0.8 3.4 1.1z">
								</path>
							</svg>
						</figure>
						<!-- SVG decoration -->
						<figure class="position-absolute top-0 start-0 ms-n9">
							<svg width="22px" height="22px" viewBox="0 0 22 22">
								<polygon class="fill-orange"
									points="22,8.3 13.7,8.3 13.7,0 8.3,0 8.3,8.3 0,8.3 0,13.7 8.3,13.7 8.3,22 13.7,22 13.7,13.7 22,13.7 ">
								</polygon>
							</svg>
						</figure>
						<!-- SVG decoration -->
						<figure class="position-absolute top-100 start-100 translate-middle ms-5 d-none d-lg-block">
							<svg width="21.5px" height="21.5px" viewBox="0 0 21.5 21.5">
								<polygon class="fill-danger"
									points="21.5,14.3 14.4,9.9 18.9,2.8 14.3,0 9.9,7.1 2.8,2.6 0,7.2 7.1,11.6 2.6,18.7 7.2,21.5 11.6,14.4 18.7,18.9 ">
								</polygon>
							</svg>
						</figure>
						<!-- SVG decoration -->
						<figure class="position-absolute top-0 start-100 translate-middle d-none d-md-block">
							<svg width="27px" height="27px">
								<path class="fill-purple"
									d="M13.122,5.946 L17.679,-0.001 L17.404,7.528 L24.661,5.946 L19.683,11.533 L26.244,15.056 L18.891,16.089 L21.686,23.068 L15.400,19.062 L13.122,26.232 L10.843,19.062 L4.557,23.068 L7.352,16.089 L-0.000,15.056 L6.561,11.533 L1.582,5.946 L8.839,7.528 L8.565,-0.001 L13.122,5.946 Z">
								</path>
							</svg>
						</figure>

						<!-- Title -->
						<h1>Education, talents, and career opportunities. All in one place.</h1>
						<p>Get inspired and discover something new today. Grow your skill with the most reliable online
							courses and certifications in marketing, information technology, programming, and data
							science. </p>

						<!-- Search course -->
						<div class="col-md-8 text-center mx-auto pb-5">
							<form class="bg-body shadow rounded p-2">
								<div class="input-group">
									<input class="form-control border-0 me-1" type="search"
										placeholder="Find your course">
									<button type="button" class="btn btn-primary mb-0 rounded z-index-1"><i
											class="fas fa-search"></i></button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- Title and SVG END -->
			</div>
		</section>
		<!-- =======================
Main Banner END -->

		<!-- =======================
Video START -->
		<section class="pb-0 py-sm-0 mt-n8">
			<div class="container">
				<div class="row">
					<div class="col-md-8 text-center mx-auto">
						<div class="card card-body shadow p-2">
							<div class="position-relative">
								<!-- Image -->
								<img src="assets/images/about/12.jpg" class="card-img rounded-2" alt="...">
								<div class="card-img-overlay">
									<!-- Video link -->
									<div class="position-absolute top-50 start-50 translate-middle">
										<a href="https://www.youtube.com/embed/tXHviS-4ygo"
											class="btn btn-lg text-danger btn-round btn-white-shadow mb-0"
											data-glightbox="" data-gallery="video-tour">
											<i class="fas fa-play"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- =======================
Video END -->

		<!-- =======================
Category START -->

		<?php include 'partials/home/categories-section.php'; ?>

		<!-- =======================
Category END -->
		<!-- =======================
Featured course START -->
		<?php include 'partials/home/courses-section.php'; ?>
		<!-- =======================
Featured course END -->

		<!-- =======================
Action box START -->
		<section class="py-0">
			<div class="container">
				<div class="row g-4">
					<!-- Action box item -->
					<div class="col-lg-6 position-relative overflow-hidden">
						<div class="bg-primary bg-opacity-10 rounded-3 p-5 h-100">
							<!-- Image -->
							<div class="position-absolute bottom-0 end-0 me-3">
								<img src="assets/images/element/08.svg" class="h-100px h-sm-200px" alt="">
							</div>
							<!-- Content -->
							<div class="row">
								<div class="col-sm-8 position-relative">
									<h3 class="mb-1">Earn a Certificate</h3>
									<p class="mb-3 h5 fw-light lead">Get the right professional certificate program for
										you.</p>
									<a href="#" class="btn btn-primary mb-0">View Programs</a>
								</div>
							</div>
						</div>
					</div>

					<!-- Action box item -->
					<div class="col-lg-6 position-relative overflow-hidden">
						<div class="bg-secondary rounded-3 bg-opacity-10 p-5 h-100">
							<!-- Image -->
							<div class="position-absolute bottom-0 end-0 me-3">
								<img src="assets/images/element/15.svg" class="h-100px h-sm-200px" alt="">
							</div>
							<!-- Content -->
							<div class="row">
								<div class="col-sm-8 position-relative">
									<h3 class="mb-1">Best Rated Courses</h3>
									<p class="mb-3 h5 fw-light lead">Enroll now in the most popular and best rated
										courses.</p>
									<a href="#" class="btn btn-warning mb-0">View Courses</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- =======================
Action box END -->

		<!-- =======================
IT courses START -->
		<section>
			<div class="container">
				<!-- Title -->
				<div class="row mb-4">
					<div class="col-lg-8 text-center mx-auto">
						<h2 class="fs-1">Top Courses for IT</h2>
						<p class="mb-0">Information Technology Courses to expand your skills and boost your career &
							salary</p>
					</div>
				</div>

				<div class="row g-4">

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/01.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">Digital Marketing</a>
									<div class="text-white">23 Courses</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/03.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">Figma</a>
									<div class="text-white">16 Courses</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/05.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">Python</a>
									<div class="text-white">32 Courses</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/06.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">Angular</a>
									<div class="text-white">48 Courses</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/07.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">React-Native</a>
									<div class="text-white">31 Courses</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/08.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">Sketch</a>
									<div class="text-white">38 Courses</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/09.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">Javascript</a>
									<div class="text-white">33 Courses</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Course item -->
					<div class="col-sm-6 col-lg-4 col-xl-3">
						<!-- Image -->
						<div class="card card-metro overflow-hidden rounded-3">
							<img src="assets/images/courses/4by3/10.jpg" alt="">
							<!-- Image overlay -->
							<div class="card-img-overlay d-flex">
								<!-- Info -->
								<div class="mt-auto card-text">
									<a href="#" class="text-white mt-auto h5 stretched-link">Bootstrap</a>
									<div class="text-white">62 Courses</div>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- Row END -->
			</div>
		</section>
		<!-- =======================
IT courses END -->



		<!-- =======================
Action box START -->
		<section class="py-5">
			<div class="container position-relative">
				<div class="row">
					<div class="col-12">
						<!-- SVG decoration START -->
						<figure class="position-absolute top-50 start-50 translate-middle ms-2">
							<svg>
								<path class="fill-white opacity-2"
									d="m496 22.999c0 10.493-8.506 18.999-18.999 18.999s-19-8.506-19-18.999 8.507-18.999 19-18.999 18.999 8.506 18.999 18.999z">
								</path>
								<path class="fill-white opacity-2"
									d="m775 102.5c0 5.799-4.701 10.5-10.5 10.5-5.798 0-10.499-4.701-10.499-10.5 0-5.798 4.701-10.499 10.499-10.499 5.799 0 10.5 4.701 10.5 10.499z">
								</path>
								<path class="fill-white opacity-2"
									d="m192 102c0 6.626-5.373 11.999-12 11.999s-11.999-5.373-11.999-11.999c0-6.628 5.372-12 11.999-12s12 5.372 12 12z">
								</path>
								<path class="fill-white opacity-2"
									d="m20.499 10.25c0 5.66-4.589 10.249-10.25 10.249-5.66 0-10.249-4.589-10.249-10.249-0-5.661 4.589-10.25 10.249-10.25 5.661-0 10.25 4.589 10.25 10.25z">
								</path>
							</svg>
						</figure>
						<!-- SVG decoration END -->

						<div class="bg-dark p-4 p-sm-5 rounded-3">
							<div class="row justify-content-center position-relative">
								<!-- Svg decoration START -->
								<figure
									class="fill-white opacity-1 position-absolute top-50 start-0 translate-middle-y">
									<svg width="141px" height="141px">
										<path
											d="M140.520,70.258 C140.520,109.064 109.062,140.519 70.258,140.519 C31.454,140.519 -0.004,109.064 -0.004,70.258 C-0.004,31.455 31.454,-0.003 70.258,-0.003 C109.062,-0.003 140.520,31.455 140.520,70.258 Z" />
									</svg>
								</figure>
								<!-- SVG decoration END -->

								<!-- Action box -->
								<div class="col-11 position-relative">
									<div class="row align-items-center">
										<!-- Title -->
										<div class="col-lg-7">
											<h3 class="text-white mb-0">Create your first online assessment</h3>
											<p class="text-white small">Boost up your knowledge, grow your skill with
												the most reliable online courses and certifications</p>
											<!-- List -->
											<ul
												class="list-inline mb-0 justify-content-center justify-content-lg-start">
												<li class="list-inline-item text-white me-2"> <i
														class="bi bi-check-circle-fill text-success me-2"></i>Free
													registration</li>
												<li class="list-inline-item text-white me-2"> <i
														class="bi bi-check-circle-fill text-success me-2"></i>Powerful
													features</li>
											</ul>
										</div>
										<!-- Content and input -->
										<div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
											<a href="#" class="btn btn-warning mb-0">Sign Up for Free</a>
										</div>
									</div> <!-- Row END -->
								</div>
							</div>
						</div>
					</div>
				</div> <!-- Row END -->
			</div>
		</section>
		<!-- =======================
Action box END -->

	</main>
	<!-- **************** MAIN CONTENT END **************** -->

	<!-- =======================
Footer START -->
	<footer class="bg-light pt-5">
		<div class="container">
			<!-- Row START -->
			<div class="row g-4">

				<!-- Widget 1 START -->
				<div class="col-lg-3">
					<!-- logo -->
					<a class="me-0" href="index.php">
						<img class="light-mode-item h-40px" src="assets/images/logo.svg" alt="logo">
						<img class="dark-mode-item h-40px" src="assets/images/logo-light.svg" alt="logo">
					</a>
					<p class="my-3">Eduport education theme, built specifically for the education centers which is
						dedicated to teaching and involve learners. </p>
					<!-- Social media icon -->
					<ul class="list-inline mb-0 mt-3">
						<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-facebook"
								href="#"><i class="fab fa-fw fa-facebook-f"></i></a> </li>
						<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-instagram"
								href="#"><i class="fab fa-fw fa-instagram"></i></a> </li>
						<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-twitter"
								href="#"><i class="fab fa-fw fa-twitter"></i></a> </li>
						<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-linkedin"
								href="#"><i class="fab fa-fw fa-linkedin-in"></i></a> </li>
					</ul>
				</div>
				<!-- Widget 1 END -->

				<!-- Widget 2 START -->
				<div class="col-lg-6">
					<div class="row g-4">
						<!-- Link block -->
						<div class="col-6 col-md-4">
							<h5 class="mb-2 mb-md-4">Company</h5>
							<ul class="nav flex-column">
								<li class="nav-item"><a class="nav-link" href="#">About us</a></li>
								<li class="nav-item"><a class="nav-link" href="#">Contact us</a></li>
								<li class="nav-item"><a class="nav-link" href="#">News and Blogs</a></li>
								<li class="nav-item"><a class="nav-link" href="#">Library</a></li>
								<li class="nav-item"><a class="nav-link" href="#">Career</a></li>
							</ul>
						</div>

						<!-- Link block -->
						<div class="col-6 col-md-4">
							<h5 class="mb-2 mb-md-4">Community</h5>
							<ul class="nav flex-column">
								<li class="nav-item"><a class="nav-link" href="#">Documentation</a></li>
								<li class="nav-item"><a class="nav-link" href="#">Faq</a></li>
								<li class="nav-item"><a class="nav-link" href="#">Forum</a></li>
								<li class="nav-item"><a class="nav-link" href="#">Sitemap</a></li>
							</ul>
						</div>

						<!-- Link block -->
						<div class="col-6 col-md-4">
							<h5 class="mb-2 mb-md-4">Teaching</h5>
							<ul class="nav flex-column">
								<li class="nav-item"><a class="nav-link" href="#">Become a teacher</a></li>
								<li class="nav-item"><a class="nav-link" href="#">How to guide</a></li>
								<li class="nav-item"><a class="nav-link" href="#">Terms &amp; Conditions</a></li>
							</ul>
						</div>
					</div>
				</div>
				<!-- Widget 2 END -->

				<!-- Widget 3 START -->
				<div class="col-lg-3">
					<h5 class="mb-2 mb-md-4">Contact</h5>
					<!-- Time -->
					<p class="mb-2">
						Toll free:<span class="h6 fw-light ms-2">+1234 568 963</span>
						<span class="d-block small">(9:AM to 8:PM IST)</span>
					</p>

					<p class="mb-0">Email:<span class="h6 fw-light ms-2">example@gmail.com</span></p>

					<div class="row g-2 mt-2">
						<!-- Google play store button -->
						<div class="col-6 col-sm-4 col-md-3 col-lg-6">
							<a href="#"> <img src="assets/images/client/google-play.svg" alt=""> </a>
						</div>
						<!-- App store button -->
						<div class="col-6 col-sm-4 col-md-3 col-lg-6">
							<a href="#"> <img src="assets/images/client/app-store.svg" alt="app-store"> </a>
						</div>
					</div> <!-- Row END -->
				</div>
				<!-- Widget 3 END -->
			</div><!-- Row END -->

			<!-- Divider -->
			<hr class="mt-4 mb-0">

			<!-- Bottom footer -->
			<div class="py-3">
				<div class="container px-0">
					<div class="d-lg-flex justify-content-between align-items-center py-3 text-center text-md-left">
						<!-- copyright text -->
						<div class="text-body text-primary-hover"> Copyrights ©2024 Eduport. Build by <a
								href="https://1.envato.market/stackbros-portfolio" target="_blank"
								class="text-body">StackBros</a></div>
						<!-- copyright links-->
						<div class="justify-content-center mt-3 mt-lg-0">
							<ul class="nav list-inline justify-content-center mb-0">
								<li class="list-inline-item">
									<!-- Language selector -->
									<div class="dropup mt-0 text-center text-sm-end">
										<a class="dropdown-toggle nav-link" href="#" role="button" id="languageSwitcher"
											data-bs-toggle="dropdown" aria-expanded="false">
											<i class="fas fa-globe me-2"></i>Language
										</a>
										<ul class="dropdown-menu min-w-auto" aria-labelledby="languageSwitcher">
											<li><a class="dropdown-item me-4" href="#"><img class="fa-fw me-2"
														src="assets/images/flags/uk.svg" alt="">English</a></li>
											<li><a class="dropdown-item me-4" href="#"><img class="fa-fw me-2"
														src="assets/images/flags/gr.svg" alt="">German </a></li>
											<li><a class="dropdown-item me-4" href="#"><img class="fa-fw me-2"
														src="assets/images/flags/sp.svg" alt="">French</a></li>
										</ul>
									</div>
								</li>
								<li class="list-inline-item"><a class="nav-link" href="#">Terms of use</a></li>
								<li class="list-inline-item"><a class="nav-link pe-0" href="#">Privacy policy</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- =======================
Footer END -->

	<!-- Sticky element START -->
	<div class="alert alert-dismissible sticky-element fade show bg-dark text-white rounded-3 shadow p-4 ms-3 mb-3 col-10 col-md-4 col-lg-3 col-xl-2 d-none d-lg-block"
		role="alert">
		<div class="d-sm-flex align-items-center mb-3">
			<!-- Avatar -->
			<div>
				<div class="icon-lg bg-purple rounded-circle text-purple">
					<img class="p-3" src="assets/images/client/aftereffect.svg" alt="avatar">
				</div>
			</div>
			<!-- Info -->
			<div class="ms-sm-2 mt-2 mt-sm-0">
				<h6 class="mb-0 text-white">Adobe after effect motion</h6>
				<span class="small mb-0 me-3"><i class="far fa-clock text-danger me-2"></i>30 mins</span>
				<span class="small mb-0 me-1"><i class="fas fa-circle fw-bold text-success small me-2"></i>Live</span>
			</div>
		</div>
		<p class="mb-0 small">Its recommended that you complete this assignment to improve your design skills for
			graphics</p>

		<!-- Avatar group -->
		<div class="d-sm-flex justify-content-between mt-4">
			<ul class="avatar-group mb-2 mb-sm-0">
				<li class="avatar avatar-xs">
					<img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="avatar">
				</li>
				<li class="avatar avatar-xs">
					<img class="avatar-img rounded-circle" src="assets/images/avatar/02.jpg" alt="avatar">
				</li>
				<li class="avatar avatar-xs">
					<img class="avatar-img rounded-circle" src="assets/images/avatar/03.jpg" alt="avatar">
				</li>
				<li class="avatar avatar-xs">
					<img class="avatar-img rounded-circle" src="assets/images/avatar/06.jpg" alt="avatar">
				</li>
			</ul>

			<!-- Button -->
			<button type="button" class="btn btn-success btn-sm mb-0" data-bs-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">Join now</span>
			</button>
		</div>
		<!-- Close button -->
		<div class="position-absolute end-0 top-0 mt-n3 me-n3">
			<button type="button" class="btn btn-danger btn-round btn-sm mb-0" data-bs-dismiss="alert"
				aria-label="Close">
				<span aria-hidden="true"><i class="bi bi-x-lg"></i></span>
			</button>
		</div>
	</div>
	<!-- Sticky element START -->

	<!-- Back to top -->
	<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>

	<!-- Vendors -->
	<script src="assets/vendor/tiny-slider/min/tiny-slider.js"></script>
	<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>