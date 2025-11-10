<!-- Header START -->
<header class="navbar-light navbar-sticky">
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

			</div>
		</div>
	</nav>
</header>
<!-- Header END -->