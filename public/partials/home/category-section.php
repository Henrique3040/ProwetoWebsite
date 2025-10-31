<section>
    <div class="container">
        <!-- Title -->
        <div class="row mb-4">
            <div class="col-lg-8 text-center mx-auto">
                <h2 class="fs-1">Explore Categories</h2>
                <p class="mb-0">Browse through our categories and find what fits you best</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($categories)): ?>
                <?php
                $colors = ['primary', 'success', 'danger', 'warning', 'info', 'purple', 'orange', 'blue', 'dark'];
                $i = 0;
                ?>

                <?php foreach ($categories as $cat): ?>
                    <?php
                    $catName = htmlspecialchars($cat['Naam']);
                    $catId = (int) $cat['id'];
                    $total = isset($cat['TotalCourses']) ? (int) $cat['TotalCourses'] : count($cat['courses'] ?? []);
                    $iconClass = htmlspecialchars($cat['Icon'] ?? 'fa-question-circle');
                    $color = $colors[$i % count($colors)];
                    $i++;
                    ?>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="card card-metro overflow-hidden rounded-3 border-0 shadow-sm position-relative">
                            <!-- Achtergrondkleur -->
                            <div class="bg-<?= $color ?> bg-opacity-25 position-absolute top-0 start-0 w-100 h-100"></div>

                            <!-- Icon in het midden -->
                            <div class="d-flex align-items-center justify-content-center flex-column text-center position-relative py-5"
                                style="z-index:2;">
                                <div class="icon-lg bg-body rounded-circle shadow-sm mb-3 d-flex align-items-center justify-content-center"
                                    style="width:80px; height:80px;">
                                    <i class="fas <?= $iconClass ?> fa-2x text-<?= $color ?>"></i>
                                </div>
                                <h5 class="mb-1">
                                    <a href="categorie-page.php?id=<?= $catId ?>"
                                        class="stretched-link text-dark text-decoration-none"><?= $catName ?></a>
                                </h5>
                                <small class="text-muted"><?= $total ?> Courses</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No categories found.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="course-categories.php" class="btn btn-primary-soft">View all categorie
                <i class="fas fa-sync ms-2"></i>
            </a>
        </div>

    </div>
</section>