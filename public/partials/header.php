<!-- Header START -->
<header class="navbar-light navbar-sticky navbar-transparent"> <!-- Logo Nav START -->
    <?php $loggedIn = AuthController::isLoggedIn();
    if ($loggedIn) {
        $userId = $_SESSION['user']['id'];
        $notifications = $notificatieController->getUserNotifications($userId);
        $unreadCount = count(array_filter($notifications, fn($n) => $n['status'] === 'unread'));
    }
    ?>

    <nav class="navbar navbar-expand-xl">
        <div class="container">
            <!-- Logo START --> <a class="navbar-brand" href="index.php"> <img class="light-mode-item navbar-brand-item"
                    src="assets/images/prowetoLogoEdit.png" alt="logo"> </a>
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
                                                    <a class="dropdown-item" href="course-detail.php?id=<?= $course['id'] ?>">
                                                        <?= htmlspecialchars($course['Titel']) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a class="dropdown-item" href="categorie-page.php?id=<?= $category['id'] ?>">
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
                            <form class="rounded position-relative" method="GET" action="search.php">
                                <input class="form-control pe-5 bg-secondary bg-opacity-10 border-0" type="search"
                                    name="q" placeholder="Search" aria-label="Search">
                                <button
                                    class="btn btn-link bg-transparent px-2 py-0 position-absolute top-50 end-0 translate-middle-y"
                                    type="submit"><i class="fas fa-search fs-6 text-primary"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div> <!-- Nav Search END -->
            </div> <!-- Main navbar END --> <!-- Right header content START --> <!-- Add to cart -->


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
                                    <a class="small" href="/ajax/clear_notifications.php">Clear all</a>
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
                                                    <a href="#" class="list-group-item-action border-0 border-bottom d-flex p-3">

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
                                    <a href="#" class="stretched-link">See all incoming activity</a>
                                </div>
                            </div>
                        </div>

                        <!-- Notification dropdown menu END -->
                    </li>
                    <!-- Notification dropdown END -->

                </ul>
            <?php endif; ?>

            <?php if ($loggedIn): ?>
                <ul class="navbar-nav mx-auto">
                    <li>
                        <a class="bg-danger-soft-hover" href="logout.php">
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
    </nav> <!-- Logo Nav END -->
</header>