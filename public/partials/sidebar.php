<!-- Sidebar START -->
<nav class="navbar sidebar navbar-expand-xl navbar-dark bg-dark">

	<!-- Navbar brand for xl START -->
	<div class="d-flex align-items-center">
		<a class="navbar-brand" href="index.php">
			<img class="navbar-brand-item" src="assets/images/logo-light.svg" alt="">
		</a>
	</div>
	<!-- Navbar brand for xl END -->
	
	<div class="offcanvas offcanvas-start flex-row custom-scrollbar h-100" data-bs-backdrop="true" tabindex="-1" id="offcanvasSidebar">
		<div class="offcanvas-body sidebar-content d-flex flex-column bg-dark">

			<!-- Sidebar menu START -->
			<ul class="navbar-nav flex-column" id="navbar-sidebar">
				
				<!-- Menu item 1 -->
				<li class="nav-item"><a href="admin-dashboard.php" class="nav-link "><i class="bi bi-house fa-fw me-2"></i>Dashboard</a></li>
				
				<!-- Title -->
				<li class="nav-item ms-2 my-2">Pages</li>

				<!-- menu item 2 -->
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="collapse" href="#collapsepage" role="button" aria-expanded="false" aria-controls="collapsepage">
						<i class="bi bi-basket fa-fw me-2"></i>Courses
					</a>
					<!-- Submenu -->
					<ul class="nav collapse flex-column" id="collapsepage" data-bs-parent="#navbar-sidebar">
						<li class="nav-item"> <a class="nav-link" href="admin-course-list.php">All Courses</a></li>
						<li class="nav-item"> <a class="nav-link" href="admin-course-category.php">Course Category</a></li>
						<li class="nav-item"> <a class="nav-link" href="admin-course-detail.php">Course Detail</a></li>
					</ul>
				</li>

				<!-- Menu item 3 -->
				<li class="nav-item"> <a class="nav-link" href="admin-student-list.php"><i class="fas fa-user-graduate fa-fw me-2"></i>Students</a></li>

				<!-- Menu item 4 -->
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="collapse" href="#collapseinstructors" role="button" aria-expanded="false" aria-controls="collapseinstructors">
						<i class="fas fa-user-tie fa-fw me-2"></i>Instructors
					</a>
					<!-- Submenu -->
					<ul class="nav collapse flex-column" id="collapseinstructors" data-bs-parent="#navbar-sidebar">
						<li class="nav-item"> <a class="nav-link" href="admin-instructor-list.php">Instructors</a></li>
						<li class="nav-item"> <a class="nav-link" href="admin-instructor-detail.php">Instructor Detail</a></li>
						<li class="nav-item"> 
							<a class="nav-link" href="admin-instructor-request.php">Instructor requests
								<span class="badge text-bg-success rounded-circle ms-2">2</span>
							</a>
						</li>
					</ul>
				</li>
				
				<!-- Menu item 5 -->
				<li class="nav-item"> <a class="nav-link" href="admin-review.php"><i class="far fa-comment-dots fa-fw me-2"></i>Reviews</a></li>

				<!-- Menu item 6 -->
				<li class="nav-item"> <a class="nav-link" href="admin-earning.php"><i class="far fa-chart-bar fa-fw me-2"></i>Earnings</a></li>

				<!-- Menu item 7 -->
				<li class="nav-item"> <a class="nav-link" href="admin-setting.php"><i class="fas fa-user-cog fa-fw me-2"></i>Admin Settings</a></li>

				<!-- Menu item 8 -->
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="collapse" href="#collapseauthentication" role="button" aria-expanded="false" aria-controls="collapseauthentication">
						<i class="bi bi-lock fa-fw me-2"></i>Authentication
					</a>
					<!-- Submenu -->
					<ul class="nav collapse flex-column" id="collapseauthentication" data-bs-parent="#navbar-sidebar">
						<li class="nav-item"> <a class="nav-link" href="sign-up.php">Sign Up</a></li>
						<li class="nav-item"> <a class="nav-link" href="sign-in.php">Sign In</a></li>
						<li class="nav-item"> <a class="nav-link" href="forgot-password.php">Forgot Password</a></li>
						<li class="nav-item"> <a class="nav-link" href="admin-error-404.php">Error 404</a></li>
					</ul>
				</li>

			
			<!-- Sidebar footer START -->
			<div class="px-3 mt-auto pt-3">
				<div class="d-flex align-items-center justify-content-between text-primary-hover">
						<a class="h5 mb-0 text-body" href="admin-setting.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Settings">
							<i class="bi bi-gear-fill"></i>
						</a>
						<a class="h5 mb-0 text-body" href="index.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Home">
							<i class="bi bi-globe"></i>
						</a>
						<a class="h5 mb-0 text-body" href="sign-in.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Sign out">
							<i class="bi bi-power"></i>
						</a>
				</div>
			</div>
			<!-- Sidebar footer END -->
			
		</div>
	</div>
</nav>
<!-- Sidebar END -->