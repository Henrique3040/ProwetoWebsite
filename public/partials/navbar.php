<!-- Header START -->
<header class="navbar-light navbar-sticky">
	<?php $loggedIn = AuthController::isLoggedIn();
	if ($loggedIn) {
		$userId = $_SESSION['user']['id'];
		$notifications = $notificatieController->getUserNotifications($userId);
		$unreadCount = $notificatieController->getUnreadCount($userId);

		$userEmailNotif = $userController->getEmailNotification($userId);
	}

	?>

	<nav class="navbar navbar-expand-xl">
		<div class="container">

			<!-- Logo -->
			<a class="navbar-brand" href="index.php">
				<img class="light-mode-item navbar-brand-item" src="assets/images/prowetoLogoEdit.png" alt="logo">
			</a>

			<!-- Mobile toggle -->
			<button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
				data-bs-target="#navbarCollapse">
				<span class="navbar-toggler-animation">
					<span></span><span></span><span></span>
				</span>
			</button>

			<!-- Navbar Content -->
			<div class="collapse navbar-collapse" id="navbarCollapse">

				<!-- Main Menu -->
				<ul class="navbar-nav mx-auto">

					<li class="nav-item">
						<a class="nav-link" href="index.php">Home</a>
					</li>

					<li class="nav-item">
						<a class="nav-link" href="about.php">About Us</a>
					</li>

					<?php if ($loggedIn): ?>
						<li class="nav-item">
							<a class="nav-link" href="user-material-reservations.php">Mijn reservaties</a>
						</li>
					<?php endif; ?>


				</ul>
				<!-- End Main Menu -->

				<!-- Search Bar -->
				<div class="nav my-3 my-xl-0 px-4 flex-nowrap align-items-center">
					<div class="nav-item w-100">
						<form class="position-relative" method="GET" action="search.php">
							<input class="form-control pe-5 bg-transparent" type="search" name="q" placeholder="Search">
							<button
								class="bg-transparent p-2 position-absolute top-50 end-0 translate-middle-y border-0"
								type="submit">
								<i class="fas fa-search fs-6 text-primary-hover text-reset"></i>
							</button>
						</form>
					</div>
				</div>
				<!-- End Search Bar -->

				<?php if ($loggedIn): ?>
					<ul class="navbar-nav mx-auto">
						<!-- Notification dropdown START -->
						<li class="nav-item ms-2 ms-md-3 dropdown">
							<!-- Notification button -->
							<a class="btn btn-light btn-round mb-0" href="#" role="button" data-bs-toggle="dropdown"
								aria-expanded="false" data-bs-auto-close="outside">
								<i class="bi bi-bell fa-fw"></i>
							</a>
							<!-- Notification dote -->
							<?php if ($unreadCount > 0): ?>
								<span class="notif-badge animation-blink"></span>
							<?php endif; ?>



							<!-- Notification dropdown menu START -->
							<div
								class="dropdown-menu dropdown-animation dropdown-menu-end dropdown-menu-size-md p-0 shadow-lg border-0">
								<div class="card bg-transparent">
									<div
										class="card-header bg-transparent border-bottom py-4 d-flex justify-content-between align-items-center">
										<h6 class="m-0">
											Notifications
											<?php if ($unreadCount > 0): ?>
												<span class="badge bg-danger bg-opacity-10 text-danger ms-2">
													<?= $unreadCount ?> new
												</span>
											<?php endif; ?>
										</h6>
										<a class="small" href="/ajax/clear_notifications.php">Mark as read</a>
									</div>
									<div class="card-body p-0">
										<ul class="list-group list-unstyled list-group-flush">

											<?php if (empty($notifications)): ?>
												<li class="p-3 text-center text-muted small">
													Geen nieuwe notificaties.
												</li>
											<?php else: ?>

												<?php foreach ($notifications as $n): ?>
													<li>
														<a href="#"
															class="list-group-item-action border-0 border-bottom d-flex p-3">

															<div class="me-3">
																<div class="avatar avatar-md">
																	<img class="avatar-img rounded-circle"
																		src="/assets/images/avatar/default.png" alt="avatar">
																</div>
															</div>

															<div>
																<p class="text-body small m-0">
																	<?= htmlspecialchars($n['boodschap']) ?>
																</p>

																<small class="text-muted">
																	<?= $n['aangemaakt_op'] ?>
																</small>
															</div>

														</a>
													</li>
												<?php endforeach; ?>

											<?php endif; ?>

										</ul>

									</div>
									<!-- Button -->
									<div class="card-footer bg-transparent border-0 py-3 text-center position-relative">
										<form method="post" action="ajax/delete_all_notification.php">
											<input type="hidden" name="user_id" value="<?= $userId ?>">
											<button type="submit">Clear All</button>
										</form>

									</div>
								</div>
							</div>

							<!-- Notification dropdown menu END -->
						</li>
						<!-- Notification dropdown END -->

						<li class="nav-item ms-2 ms-md-3 dropdown">
							<a class="btn btn-light btn-round mb-0" href="#" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-gear fa-fw"></i>
							</a>
							<div class="dropdown-menu dropdown-menu-end p-3 shadow-lg">
								<form method="POST" action="ajax/update_email_notif.php">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="value" value="1"
											id="emailNotificationToggle" <?= $userEmailNotif ? 'checked' : '' ?>>
										<label class="form-check-label" for="emailNotificationToggle">
											Ontvang e-mail notificaties
										</label>
									</div>
									<input type="hidden" name="user_id" value="<?= $userId ?>">
									<button type="submit" class="btn btn-primary btn-sm mt-2">Opslaan</button>
								</form>
							</div>
						</li>



					</ul>
				<?php endif; ?>

				<?php if ($loggedIn): ?>
					<ul class="navbar-nav mx-auto">
						<li>
							<a class="bg-danger-soft-hover" href="ajax/logout.php">
								<i class="bi bi-power fa-fw me-2"></i>Log out
							</a>
						</li>
					</ul>
				<?php else: ?>
					<ul class="navbar-nav mx-auto">
						<li>
							<a class="bg-primary-soft-hover" href="sign-in.php">
								<i class="bi bi-person fa-fw me-2"></i>Log in
							</a>
						</li>
					</ul>
				<?php endif; ?>

			</div>
		</div>
	</nav>

</header>
<!-- Header END -->