<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/plyr/plyr.css" />

	<?php

	require_once __DIR__ . '/../app/core/init.php';

	// Haal course ID op uit de URL
	$courseId = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$course = $courseController->getCourseDetail($courseId);

	$categories = $categoryController->getCategoriesByCourse($course['CursusID']);

	$faqs = $faqController-> index($course['CursusID']);
	?>


	<?php include("partials/head-css.php"); ?>
</head>

<body>

	<?php include("partials/navbar.php"); ?>

	<!-- **************** MAIN CONTENT START **************** -->
	<main>

		<!-- =======================
Page content START -->
		<section class="pt-3 pt-xl-5">
			<div class="container" data-sticky-container>
				<div class="row g-4">
					<!-- Main content START -->
					<div class="col-xl-8">

						<div class="row g-4">
							<!-- Title START -->
							<div class="col-12">
								<!-- Title -->
								<h2><?= htmlspecialchars($course['Titel']) ?></h2>
								<p><?= htmlspecialchars($course['KorteBeschrijving']) ?></p>
								<!-- Content -->
								<ul class="list-inline mb-0">
									<li class="list-inline-item fw-light h6 me-3"><i
											class="fas fa-star me-2"></i><?= htmlspecialchars($course['Rating']) ?>/5.0
									</li>
									<li class="list-inline-item fw-light h6 me-3"><i
											class="fas fa-globe me-2"></i><?= htmlspecialchars($course['Taal']) ?></li>
									<li class="list-inline-item fw-light h6 me-3"><i
											class="bi bi-patch-exclamation-fill me-2"></i>Last updated
										<?= date("d M Y", strtotime($course['LaatstBijgewerkt'])) ?>
									</li>
								</ul>

							</div>
							<!-- Title END -->

							<!-- Video Player -->
							<div class="col-12 position-relative">
								<div class="video-player rounded-3">
									<?php
									$videoUrl = htmlspecialchars($course['Link']);

									// Kijk of het een YouTube-link is
									if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
										// YouTube embed link maken
										$videoId = '';
										if (preg_match('/(?:v=|youtu\.be\/)([^&]+)/', $videoUrl, $matches)) {
											$videoId = $matches[1];
										}
										echo '<iframe width="100%" height="480" 
                                                 src="https://www.youtube.com/embed/' . $videoId . '" 
                                                      frameborder="0" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen>
						                      </iframe>';
									} else {
										// Normale lokale video
										echo '<video controls crossorigin="anonymous" playsinline poster="' . htmlspecialchars($course['FotoURL']) . '">
                                                  <source src="' . $videoUrl . '" type="video/mp4">
                                                    Je browser ondersteunt de video-tag niet.
                                              </video>';
									}
									?>
								</div>
							</div>

							<!-- About course START -->
							<div class="col-12">
								<div class="card border">
									<!-- Card header START -->
									<div class="card-header border-bottom">
										<h3 class="mb-0">Course description</h3>
									</div>
									<!-- Card header END -->

									<!-- Card body START -->
									<div class="card-body">


										<!-- List content -->
										<h5 class="mt-4">What you’ll learn</h5>
										<div class="row mb-3">
											<div class="card-body">
												<?= !empty($course['Beschrijving'])
													? nl2br($course['Beschrijving'])
													: '<p><em>No description available.</em></p>' ?>
											</div>

										</div>

									</div>
									<!-- Card body START -->
								</div>
							</div>
							<!-- About course END -->

							<!-- Curriculum START -->

							<!-- Curriculum END -->

							<!-- FAQs START -->

							<!-- FAQ Tab Content -->
							 
							<div   id="course-pills-5" role="tabpanel"
								aria-labelledby="course-pills-tab-5">
								<!-- Title -->
								<h5 class="mb-3">Frequently Asked Questions</h5>

								<!-- Accordion START -->
								<div class="accordion accordion-flush" id="accordionExample">
									<?php if (!empty($faqs)): ?>
										<?php foreach ($faqs as $index => $faq): ?>
											<div class="accordion-item">
												<h2 class="accordion-header" id="heading<?= $index ?>">
													<button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>"
														type="button" data-bs-toggle="collapse"
														data-bs-target="#collapse<?= $index ?>"
														aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
														aria-controls="collapse<?= $index ?>">

														<span class="text-secondary fw-bold me-3">
															<?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?>
														</span>
														<span class="h6 mb-0">
															<?= htmlspecialchars($faq['Vraag']) ?>
														</span>
													</button>
												</h2>

												<div id="collapse<?= $index ?>"
													class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
													aria-labelledby="heading<?= $index ?>" data-bs-parent="#accordionExample">

													<div class="accordion-body pt-0">
														<?= nl2br(htmlspecialchars($faq['Antwoord'])) ?>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									<?php else: ?>
										<p class="text-muted">Er zijn nog geen veelgestelde vragen voor deze cursus.</p>
									<?php endif; ?>
								</div>
								<!-- Accordion END -->
							</div>
							<!-- Content END -->


						 <!-- FAQs END -->
						</div>
					</div>
					<!-- Main content END -->

					<!-- Right sidebar START -->
					<div class="col-xl-4">
						<div data-sticky data-margin-top="80" data-sticky-for="768">
							<div class="row g-4">
								<div class="col-md-6 col-xl-12">
									<!-- Course info START -->
									<div class="card card-body border p-4">
										<!-- Price and share button -->
										<div class="d-flex justify-content-between align-items-center">

											<!-- Share button with dropdown -->
											<div class="dropdown">
												<a href="#" class="btn btn-sm btn-light rounded mb-0 small"
													role="button" id="dropdownShare" data-bs-toggle="dropdown"
													aria-expanded="false">
													<i class="fas fa-fw fa-share-alt"></i>
												</a>
												<!-- dropdown button -->
												<ul class="dropdown-menu dropdown-w-sm dropdown-menu-end min-w-auto shadow rounded"
													aria-labelledby="dropdownShare">
													<li><a class="dropdown-item" href="#"><i
																class="fab fa-twitter-square me-2"></i>Twitter</a></li>
													<li><a class="dropdown-item" href="#"><i
																class="fab fa-facebook-square me-2"></i>Facebook</a>
													</li>
													<li><a class="dropdown-item" href="#"><i
																class="fab fa-linkedin me-2"></i>LinkedIn</a></li>
													<li><a class="dropdown-item" href="#"><i
																class="fas fa-copy me-2"></i>Copy link</a></li>
												</ul>
											</div>
										</div>


										<!-- Divider -->
										<hr>

										<!-- Title -->
										<h5 class="mb-3">Deze cursus bevat</h5>
										<ul class="list-group list-group-borderless border-0">
											<li class="list-group-item px-0 d-flex justify-content-between">
												<span class="h6 fw-light mb-0"><i
														class="fas fa-fw fa-book-open text-primary"></i>Materialen</span>
												<span>Ja</span>
											</li>
											
											<li class="list-group-item px-0 d-flex justify-content-between">
												<span class="h6 fw-light mb-0"><i
														class="fas fa-fw fa-globe text-primary"></i>Language</span>
												<span>English</span>
											</li>
											<li class="list-group-item px-0 d-flex justify-content-between">
												<span class="h6 fw-light mb-0"><i
														class="fas fa-fw fa-user-clock text-primary"></i>Document</span>
												<span>Nee</span>
											</li>
											
										</ul>
										<!-- Divider -->
										<hr>

										<!-- Rating and follow -->
										<div
											class="d-sm-flex justify-content-sm-between align-items-center mt-0 mt-sm-2">
											<!-- Rating star -->
											<ul class="list-inline mb-0">
												<li class="list-inline-item me-0 small"><i
														class="fas fa-star text-warning"></i></li>
												<li class="list-inline-item me-0 small"><i
														class="fas fa-star text-warning"></i></li>
												<li class="list-inline-item me-0 small"><i
														class="fas fa-star text-warning"></i></li>
												<li class="list-inline-item me-0 small"><i
														class="fas fa-star text-warning"></i></li>
												<li class="list-inline-item me-0 small"><i
														class="fas fa-star-half-alt text-warning"></i></li>
												<li class="list-inline-item ms-2 h6 fw-light mb-0">4.5/5.0</li>
											</ul>

										</div>
									</div>
									<!-- Course info END -->
								</div>

								<!-- Tags START -->
								<div class="col-md-6 col-xl-12">
									<div class="card card-body border p-4">
										<h4 class="mb-3">Cursus Categorieën</h4>
										<ul class="list-inline mb-0">
											<?php
											if ($categories) {
												foreach ($categories as $cat) {
													echo '<li class="list-inline-item">
                            <a class="btn btn-outline-light btn-sm" href="#">' . htmlspecialchars($cat['Naam']) . '</a>
                          </li>';
												}
											} else {
												echo '<li class="list-inline-item text-muted">Geen categorieën</li>';
											}
											?>
										</ul>
									</div>
								</div>
								<!-- Tags END -->

							</div><!-- Row End -->
						</div>
					</div>
					<!-- Right sidebar END -->

				</div><!-- Row END -->
			</div>
		</section>
		<!-- =======================
Page content END -->

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
	<script src="assets/vendor/sticky-js/sticky.min.js"></script>
	<script src="assets/vendor/plyr/plyr.min.js"></script>

	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>