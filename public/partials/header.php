<!-- Header START -->
<header class="navbar-light navbar-sticky navbar-transparent"> <!-- Logo Nav START -->
    <nav class="navbar navbar-expand-xl">
        <div class="container"> <!-- Logo START --> <a class="navbar-brand" href="index.php"> <img
                    class="light-mode-item navbar-brand-item" src="assets/images/logo.svg" alt="logo"> <img
                    class="dark-mode-item navbar-brand-item" src="assets/images/logo-light.svg" alt="logo"> </a>
            <!-- Logo END --> <!-- Responsive navbar toggler --> <button class="navbar-toggler ms-auto" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse"
                aria-expanded="true" aria-label="Toggle navigation"> <span class="me-2"><i
                class="fas fa-search fs-5"></i></span> </button> 
            <!-- Category menu START -->
            <ul class="navbar-nav navbar-nav-scroll dropdown-clickable">
                <li class="nav-item dropdown dropdown-menu-shadow-stacked"> <a class="nav-link" href="#"
                        id="categoryMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i
                            class="bi bi-grid-3x3-gap-fill me-3 fs-5 me-xl-1 d-xl-none"></i> <i
                            class="bi bi-grid-3x3-gap-fill me-1 d-none d-xl-inline-block"></i> <span
                            class="d-none d-xl-inline-block">Category</span> </a>
                    <ul class="dropdown-menu z-index-unset" aria-labelledby="categoryMenu">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <?php if (!empty($category['courses'])): ?>
                                    <li class="dropdown-submenu dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#">
                                            <?= htmlspecialchars($category['Naam']) ?>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-start" data-bs-popper="none">
                                            <?php foreach ($category['courses'] as $course): ?>
                                                <li>
                                                    <a class="dropdown-item" href="course-detail.php?id=<?= $course['CursusID'] ?>">
                                                        <?= htmlspecialchars($course['Titel']) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a class="dropdown-item" href="categorie-page.php?id=<?= $category['CategorieID'] ?>">
                                            <?= htmlspecialchars($category['Naam']) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="#">No categories found</a></li>
                        <?php endif; ?>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item bg-primary text-primary bg-opacity-10 rounded-2 mb-0"
                                href="course-categories.php">
                                View all categories
                            </a>
                        </li>
                    </ul>

                </li>
            </ul> <!-- Category menu END --> <!-- Main navbar START -->
            <div class="navbar-collapse collapse" id="navbarCollapse"> <!-- Nav Search START -->
                <div class="col-xl-8">
                    <div class="nav my-3 my-xl-0 px-4 flex-nowrap align-items-center">
                        <div class="nav-item w-100">
                            <form class="rounded position-relative"> <input
                                    class="form-control pe-5 bg-secondary bg-opacity-10 border-0" type="search"
                                    placeholder="Search" aria-label="Search"> <button
                                    class="btn btn-link bg-transparent px-2 py-0 position-absolute top-50 end-0 translate-middle-y"
                                    type="submit"><i class="fas fa-search fs-6 text-primary"></i></button> </form>
                        </div>
                    </div>
                </div> <!-- Nav Search END -->
            </div> <!-- Main navbar END --> <!-- Right header content START --> <!-- Add to cart -->
            <div class="navbar-nav position-relative overflow-visible me-3"> <a href="#" class="nav-link"> <i
                        class="fas fa-shopping-cart fs-5"></i></a> <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-success mt-xl-2 ms-n1">5
                    <span class="visually-hidden">unread messages</span> </span> </div> <!-- Language -->
            <ul class="navbar-nav navbar-nav-scroll me-3 d-none d-xl-block">
                <li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" href="#" id="language"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i
                            class="fas fa-globe me-2"></i> <span class="d-none d-lg-inline-block">Language</span> </a>
                    <ul class="dropdown-menu dropdown-menu-end min-w-auto" aria-labelledby="language">
                        <li> <a class="dropdown-item" href="#"><img class="fa-fw me-2" src="assets/images/flags/uk.svg"
                                    alt="">English</a></li>
                        <li> <a class="dropdown-item" href="#"><img class="fa-fw me-2" src="assets/images/flags/gr.svg"
                                    alt="">German</a></li>
                        <li> <a class="dropdown-item" href="#"><img class="fa-fw me-2" src="assets/images/flags/sp.svg"
                                    alt="">French</a></li>
                    </ul>
                </li>
            </ul> <!-- Signout button -->
            <div class="navbar-nav d-none d-lg-inline-block"> <button class="btn btn-danger-soft mb-0"><i
                        class="fas fa-sign-in-alt me-2"></i>Sign Up</button> </div> <!-- Right header content END -->
        </div>
    </nav> <!-- Logo Nav END -->
</header>