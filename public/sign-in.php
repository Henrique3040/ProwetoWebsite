<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("partials/title-meta.php"); ?>

	<?php include("partials/head-css.php"); ?>

	<?php
	require_once __DIR__ . '/../app/core/init.php';

	$error = '';

	var_dump($_POST);

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$username = $_POST['username'] ?? '';
		$password = $_POST['password'] ?? '';

		if ($userController->login($username, $password)) {
			header("Location: admin-dashboard.php");
			exit;
		} else {
			$error = "Ongeldige gebruikersnaam of wachtwoord.";
		}
	}
	?>

</head>

<body>

	<!-- **************** MAIN CONTENT START **************** -->
	<main>
		<section class="p-0 d-flex align-items-center position-relative overflow-hidden">

			<div class="container-fluid">
				<div class="row">
					<!-- left -->
					<div
						class="col-12 col-lg-6 d-md-flex align-items-center justify-content-center bg-primary bg-opacity-10 vh-lg-100">
						<div class="p-3 p-lg-5">
							<!-- Title -->
							<div class="text-center">
								<h2 class="fw-bold">Welcome to our largest community</h2>
								<p class="mb-0 h6 fw-light">Let's learn something new today!</p>
							</div>
							<!-- SVG Image -->
							<img src="assets/images/element/02.svg" class="mt-5" alt="">
							<!-- Info -->
						</div>
					</div>

					<!-- Right -->
					<div class="col-12 col-lg-6 m-auto">
						<div class="row my-5">
							<div class="col-sm-10 col-xl-8 m-auto">
								<!-- Title -->
								<span class="mb-0 fs-1">👋</span>
								<h1 class="fs-2">Login into Eduport!</h1>
								<p class="lead mb-4">Nice to see you! Please log in with your account.</p>

								<!-- Form START -->
								<form method="POST">
									<!-- Email -->
									<div class="mb-4">
										<label class="form-label">Email address *</label>
										<div class="input-group input-group-lg">
											<span
												class="input-group-text bg-light rounded-start border-0 text-secondary px-3">
												<i class="bi bi-envelope-fill"></i>
											</span>
											<input type="text" class="form-control border-0 bg-light rounded-end ps-1"
												placeholder="E-mail" name="username" required>
										</div>
									</div>

									<!-- Password -->
									<div class="mb-4">
										<label class="form-label">Password *</label>
										<div class="input-group input-group-lg">
											<span
												class="input-group-text bg-light rounded-start border-0 text-secondary px-3">
												<i class="fas fa-lock"></i>
											</span>
											<input type="password"
												class="form-control border-0 bg-light rounded-end ps-1"
												placeholder="password" name="password" required>
										</div>
									</div>

									<!-- Check box -->
									<div class="mb-4 d-flex justify-content-between">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="exampleCheck1">
											<label class="form-check-label" for="exampleCheck1">Remember me</label>
										</div>
										<div class="text-primary-hover">
											<a href="forgot-password.php" class="text-secondary">
												<u>Forgot password?</u>
											</a>
										</div>
									</div>
									<!-- Button -->
									<div class="align-items-center mt-0">
										<div class="d-grid">
											<button class="btn btn-primary mb-0" type="submit">Login</button>
										</div>
									</div>
								</form>
								<?php if (!empty($error)): ?>
									<div class="alert alert-danger mt-3">
										<?= htmlspecialchars($error) ?>
									</div>
								<?php endif; ?>

								<!-- Form END -->

							</div>
						</div> <!-- Row END -->
					</div>
				</div> <!-- Row END -->
			</div>
		</section>
	</main>
	<!-- **************** MAIN CONTENT END **************** -->

	<!-- Back to top -->
	<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>



	<?php include("partials/footer-scripts.php"); ?>

</body>

</html>