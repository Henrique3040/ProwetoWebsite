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
						<li class="nav-item"> <a class="nav-link" href="admin-subwebsites.php">SubWebsites</a></li>
						<li class="nav-item"> <a class="nav-link" href="admin-create-course.php">Add New Course</a></li>
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
						<a class="h5 mb-0 text-body" href="logout.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Sign out">
							<i class="bi bi-power"></i>
						</a>
				</div>
			</div>
			<!-- Sidebar footer END -->
			
		</div>
	</div>
</nav>
<!-- Sidebar END -->