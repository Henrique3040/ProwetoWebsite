<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/glightbox/css/glightbox.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/quill/quill.snow.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/bs-stepper/css/bs-stepper.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/overlayscrollbars/styles/overlayscrollbars.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<?php include("partials/head-css.php"); ?>

	<?php
	require_once __DIR__ . '/../app/core/init.php';

	if (!isset($_GET['id'])) {
		header("Location: admin-course-list.php");
		exit;
	}

	$courseId = $_GET['id'];

	// Als formulier werd gepost → update uitvoeren
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$courseController->update($courseId);
	}

	// Haal huidige cursus op
	$course = $courseController->getCourseDetail($courseId);
	$categories = $categoryController->getAllCategories();
	$leerjaren = $leerjaarController->getAllLeerjaren();

	// Beveiliging: redirect als cursus niet gevonden is
	if (!$course) {
		header("Location: admin-course-list.php?error=notfound");
		exit;
	}
	?>

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

				<h1 class="h3 mb-3">Edit Course</h1>

				<!-- Card START -->
				<div class="card border rounded-3 mb-5">
					<div id="stepper" class="bs-stepper stepper-outline">
						<!-- Card header -->
						<div class="card-header bg-light border-bottom px-lg-5">
							<!-- Step Buttons START -->
							<div class="bs-stepper-header" role="tablist">
								<!-- Step 1 -->
								<div class="step" data-target="#step-1">
									<div class="d-grid text-center align-items-center">
										<button type="button" class="btn btn-link step-trigger mb-0" role="tab"
											id="steppertrigger1" aria-controls="step-1">
											<span class="bs-stepper-circle">1</span>
										</button>
										<h6 class="bs-stepper-label d-none d-md-block">Course details</h6>
									</div>
								</div>
								<div class="line"></div>

								<!-- Step 2 -->
								<div class="step" data-target="#step-2">
									<div class="d-grid text-center align-items-center">
										<button type="button" class="btn btn-link step-trigger mb-0" role="tab"
											id="steppertrigger2" aria-controls="step-2">
											<span class="bs-stepper-circle">2</span>
										</button>
										<h6 class="bs-stepper-label d-none d-md-block">Course media</h6>
									</div>
								</div>
								<div class="line"></div>

								<!-- Step 3 -->
								<div class="step" data-target="#step-3">
									<div class="d-grid text-center align-items-center">
										<button type="button" class="btn btn-link step-trigger mb-0" role="tab"
											id="steppertrigger3" aria-controls="step-3">
											<span class="bs-stepper-circle">3</span>
										</button>
										<h6 class="bs-stepper-label d-none d-md-block">Additional information</h6>
									</div>
								</div>

							</div>
							<!-- Step Buttons END -->
						</div>

						<!-- Card body START -->
						<div class="card-body px-1 px-sm-4">
							<!-- Step content START -->
							<div class="bs-stepper-content">
								<form id="editCourseForm" method="POST"
									action="admin-edit-course.php?id=<?= $courseId ?>" enctype="multipart/form-data">

									<!-- Step 1 content START -->
									<div id="step-1" role="tabpanel" class="content fade"
										aria-labelledby="steppertrigger1">
										<!-- Title -->
										<h4>Course details</h4>

										<hr> <!-- Divider -->

										<!-- Basic information START -->
										<div class="row g-4">
											<!-- Course title -->
											<div class="col-12">
												<label class="form-label">Course title</label>
												<input class="form-control" type="text" name="titel"
													value="<?= htmlspecialchars($course['Titel'] ?? '') ?>"
													placeholder="Enter course title">
											</div>

											<!-- Short description -->
											<div class="col-12">
												<label class="form-label">Short description</label>
												<textarea class="form-control" name="korte_beschrijving"
													rows="2"><?= htmlspecialchars($course['KorteBeschrijving'] ?? '') ?></textarea>
											</div>

											<!-- Course category -->


											<div class="col-md-6">
												<label class="form-label">Course category</label>
												<select class="form-select" name="categorie_id">
													<option value="">Select category</option>
													<?php foreach ($categories as $cat): ?>
														<option value="<?= $cat['CategorieID'] ?>"
															<?= ($course['CategorieID'] == $cat['CategorieID']) ? 'selected' : '' ?>>
															<?= htmlspecialchars($cat['Naam']) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>

											<!-- Leerjaar -->
											<div class="col-md-6">
												<label class="form-label">Leerjaar</label>

												<select class="form-select" name="leerjaar_id">
													<option value="">Select leerjaar</option>
													<?php foreach ($leerjaren as $lj): ?>
														<option value="<?= $lj['LeerJaarID'] ?>"
															<?= ($course['LeerJaarID'] == $lj['LeerJaarID']) ? 'selected' : '' ?>>
															<?= htmlspecialchars($lj['Naam']) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>

											<!-- Switches -->
											<div class="col-md-4 d-flex align-items-center mt-4">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" name="documenten"
														id="docSwitch" <?= ($course['Documenten']) ? 'checked' : '' ?>>
													<label class="form-check-label" for="docSwitch">Documents
														available</label>
												</div>
											</div>

											<div class="col-md-4 d-flex align-items-center mt-4">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" name="materiaal"
														id="matSwitch" <?= ($course['Materiaal']) ? 'checked' : '' ?>>
													<label class="form-check-label" for="matSwitch">Material
														available</label>
												</div>
											</div>

											<div class="col-md-4 d-flex align-items-center mt-4">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" name="active"
														id="actSwitch" <?= ($course['Active']) ? 'checked' : '' ?>>
													<label class="form-check-label" for="actSwitch">Active</label>
												</div>
											</div>


											<!-- Course description -->
											<div class="col-12">
												<label class="form-label">Beschrijving</label>

												<!-- Editor toolbar -->
												<div class="bg-light border border-bottom-0 rounded-top py-3"
													id="quilltoolbar">
													<span class="ql-formats">
														<select class="ql-size"></select>
													</span>
													<span class="ql-formats">
														<button class="ql-bold"></button>
														<button class="ql-italic"></button>
														<button class="ql-underline"></button>
														<button class="ql-strike"></button>
													</span>
													<span class="ql-formats">
														<select class="ql-color"></select>
														<select class="ql-background"></select>
													</span>
													<span class="ql-formats">
														<button class="ql-code-block"></button>
													</span>
													<span class="ql-formats">
														<button class="ql-list" value="ordered"></button>
														<button class="ql-list" value="bullet"></button>
														<button class="ql-indent" value="-1"></button>
														<button class="ql-indent" value="+1"></button>
													</span>
													<span class="ql-formats">
														<button class="ql-link"></button>
														<button class="ql-image"></button>
													</span>
													<span class="ql-formats">
														<button class="ql-clean"></button>
													</span>
												</div>


												<div class="bg-body border rounded-bottom h-400px overflow-hidden"
													id="quilleditor">
													<?= isset($course['Beschrijving']) ? $course['Beschrijving'] : '' ?>
												</div>

												<!-- Verborgen input die door jQuery wordt gevuld -->
												<input type="hidden" name="beschrijving" id="beschrijving"
													value="<?= htmlspecialchars($course['Beschrijving'] ?? '') ?>">
											</div>

										</div>

										<!-- Step 1 button -->
										<div class="d-flex justify-content-end mt-3">
											<button type="button" class="btn btn-primary next-btn mb-0">Next</button>
										</div>
									</div>
									<!-- Basic information START -->
							</div>
							<!-- Step 1 content END -->

							<!-- Step 2 content START -->
							<div id="step-2" role="tabpanel" class="content fade" aria-labelledby="steppertrigger2">
								<!-- Title -->
								<h4>Course media</h4>

								<hr> <!-- Divider -->

								<div class="row">
									<!-- Upload image START -->
									<div class="col-12">
										<div
											class="text-center justify-content-center align-items-center p-4 p-sm-5 border border-2 border-dashed position-relative rounded-3">
											<!-- Preview huidige afbeelding -->
											<?php if (!empty($course['FotoURL']) && file_exists($course['FotoURL'])): ?>
												<img src="<?= htmlspecialchars($course['FotoURL']) ?>"
													class="h-50px rounded" alt="Course Image">
											<?php else: ?>
												<img src="assets/images/element/gallery.svg" class="h-50px" alt="">
											<?php endif; ?>

											<div>
												<h6 class="my-2">Upload course image here, or <a href="#!"
														class="text-primary">Browse</a></h6>
												<label style="cursor:pointer;">
													<span>
														<input class="form-control stretched-link" type="file"
															name="foto" id="image"
															accept="image/gif, image/jpeg, image/png" />
													</span>
												</label>
												<p class="small mb-0 mt-2"><b>Note:</b> Only JPG, JPEG and PNG.
													Our suggested dimensions are 600px * 450px. Larger image
													will be cropped to 4:3 to fit our thumbnails/previews.</p>
											</div>
										</div>

										<!-- Button -->
										<div class="d-sm-flex justify-content-end mt-2">
											<button type="button" class="btn btn-sm btn-danger-soft mb-3">Remove
												image</button>
										</div>
									</div>
									<!-- Upload image END -->

									<!-- Upload video START -->
									<div class="col-12">
										<h5>Upload video</h5>
										<!-- Input -->
										<div class="col-12 mt-4 mb-5">
											<label class="form-label">Video URL</label>
											<input class="form-control" type="text" name="video_link"
												value="<?= htmlspecialchars($course['Link'] ?? '') ?>"
												placeholder="Enter video url">

										</div>
										<div class="position-relative my-4">
											<hr>
											<p
												class="small position-absolute top-50 start-50 translate-middle bg-body px-3 mb-0">
												Or</p>
										</div>

										<div class="col-12">
											<label class="form-label">Upload video</label>
											<div class="input-group mb-3">
												<input type="file" class="form-control" id="inputGroupFile01"
													name="video_mp4" accept=".mp4">
												<label class="input-group-text">.mp4</label>
											</div>
											<div class="input-group mb-3">
												<input type="file" class="form-control" id="inputGroupFile02"
													name="video_webm" accept=".webm">
												<label class="input-group-text">.WebM</label>
											</div>
											<div class="input-group mb-3">
												<input type="file" class="form-control" id="inputGroupFile03"
													name="video_ogg" accept=".ogg">
												<label class="input-group-text">.OGG</label>
											</div>
										</div>

										<!-- Preview -->
										<h5 class="mt-4">Video preview</h5>
										<?php if (!empty($course['Link'])): ?>
											<div class="position-relative">
												<img src="assets/images/video-placeholder.jpg" class="rounded-4" alt="">
												<div class="position-absolute top-50 start-50 translate-middle">
													<a href="<?= htmlspecialchars($course['Link']) ?>"
														class="btn btn-lg text-danger btn-round btn-white-shadow mb-0"
														data-glightbox="" data-gallery="video-tour">
														<i class="fas fa-play"></i>
													</a>
												</div>
											</div>
										<?php endif; ?>

									</div>
									<!-- Upload video END -->

									<!-- Step 2 button -->
									<div class="d-flex justify-content-between mt-3">
										<button class="btn btn-secondary prev-btn mb-0">Previous</button>
										<button type="button" class="btn btn-primary next-btn mb-0">Next</button>
									</div>
								</div>
							</div>
							<!-- Step 2 content END -->


							<!-- Step 3 content START -->
							<div id="step-3" role="tabpanel" class="content fade" aria-labelledby="steppertrigger3">
								<!-- Title -->
								<h4>Additional information</h4>

								<hr> <!-- Divider -->

								<div class="row g-4" id="faqList">
									<?php if (!empty($course['Faqs'])): ?>
										<?php foreach ($course['Faqs'] as $faq): ?>
											<div class="col-12">
												<div class="bg-body p-4 border rounded">
													<div class="d-flex justify-content-between align-items-center mb-2">
														<h6 class="mb-0"><?= htmlspecialchars($faq['Vraag']) ?></h6>
														<div>
															<a href="#"
																class="btn btn-sm btn-success-soft btn-round me-1 edit-faq"
																data-id="<?= $faq['FAQID'] ?>"><i
																	class="far fa-fw fa-edit"></i></a>
															<button type="button"
																class="btn btn-sm btn-danger-soft btn-round delete-faq"
																data-id="<?= $faq['FAQID'] ?>"><i
																	class="fas fa-fw fa-times"></i></button>
														</div>
													</div>
													<p><?= htmlspecialchars($faq['Antwoord']) ?></p>
												</div>
											</div>
										<?php endforeach; ?>
									<?php else: ?>
										<p class="text-muted">Nog geen FAQ’s toegevoegd voor deze cursus.</p>
									<?php endif; ?>
								</div>


								<!-- Knop om nieuwe FAQ toe te voegen -->
								<div class="d-flex justify-content-end mt-4">
									<button type="button" class="btn btn-success-soft me-2" data-bs-toggle="modal"
										data-bs-target="#addQuestion">
										+ Add FAQ
									</button>
									<button id="updateCourseBtn" class="btn btn-primary">Update course</button>
								</div>



							</div>
							<!-- Step 3 content END -->

							</form>
						</div>
					</div>
					<!-- Card body END -->
				</div>
			</div>
			<!-- Card END -->
		</div>
		<!-- Page main content END -->

		</div>
		<!-- Page content END -->

	</main>
	<!-- **************** MAIN CONTENT END **************** -->


	<!-- Popup modal for add faq START -->
	<div class="modal fade" id="addQuestion" tabindex="-1" aria-labelledby="addQuestionLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-dark">
					<h5 class="modal-title text-white" id="addQuestionLabel">Add FAQ</h5>
					<button type="button" class="btn btn-sm btn-light mb-0 ms-auto" data-bs-dismiss="modal"
						aria-label="Close"><i class="bi bi-x-lg"></i></button>
				</div>
				<div class="modal-body">
					<form class="row text-start g-3">
						<!-- Question -->
						<div class="col-12">
							<label class="form-label">Question</label>
							<input id="faqQuestion" class="form-control" type="text" placeholder="Write a question">
						</div>
						<!-- Answer -->
						<div class="col-12 mt-3">
							<label class="form-label">Answer</label>
							<textarea id="faqAnswer" class="form-control" rows="4" placeholder="Write a answer"
								spellcheck="false"></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger-soft my-0" data-bs-dismiss="modal">Close</button>
					<button id="saveFaqBtn" type="button" class="btn btn-success my-0">Save topic</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Popup modal for add faq END -->

	<!-- Back to top -->
	<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>

	<!-- Vendors -->
	<script src="assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>
	<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
	<script src="assets/vendor/quill/quill.js"></script>
	<script src="assets/vendor/bs-stepper/js/bs-stepper.min.js"></script>
	<script src="assets/vendor/overlayscrollbars/browser/overlayscrollbars.browser.es6.min.js"></script>
	<script src="js/admin-edit-course.js"></script>

	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>