<section class="pt-0 pt-md-5">
    <div class="container">
        <!-- Title -->
        <div class="row mb-4">
            <div class="col-lg-8 text-center mx-auto">
                <h2 class="fs-1 mb-0">Our Courses</h2>
                <p class="mb-0">Explore top picks of the week</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($courses) > 0): ?>
                <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                    <div class="col-md-6 col-lg-4 col-xxl-3">
                        <div class="card p-2 shadow h-100">
                            <div class="rounded-top overflow-hidden">
                                <div class="card-overlay-hover">
                                    <!-- Image -->
                                    <img src="<?= htmlspecialchars($course['FotoURL']) ?>" class="card-img-top"
                                        alt="<?= htmlspecialchars($course['Titel']) ?>">
                                </div>
                                <!-- Hover element -->
                                <div class="card-img-overlay">
                                    <div class="card-element-hover d-flex justify-content-end">
                                        <a href="#" class="icon-md bg-white rounded-circle">
                                            <i class="fas fa-shopping-cart text-danger"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- Card body -->
                            <div class="card-body px-2">
                                <!-- Stats row -->
                                <div class="d-flex justify-content-between">
                                    <ul class="list-inline hstack gap-2 mb-0">
                                        <li class="list-inline-item d-flex justify-content-center align-items-center">
                                            <div class="icon-md bg-orange bg-opacity-10 text-orange rounded-circle">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <span class="h6 fw-light mb-0 ms-2">
                                                <?= number_format($course['Views']) ?>
                                            </span>
                                        </li>
                                        <li class="list-inline-item d-flex justify-content-center align-items-center">
                                            <div class="icon-md bg-warning bg-opacity-15 text-warning rounded-circle">
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <span class="h6 fw-light mb-0 ms-2">4.5</span>
                                        </li>
                                    </ul>
                                    <div class="avatar avatar-sm">
                                        <img class="avatar-img rounded-circle" src="assets/images/avatar/09.jpg" alt="avatar">
                                    </div>
                                </div>
                                <hr>
                                <h6 class="card-title">
                                    <a href="course-detail.php?id=<?= urlencode($course['CursusID']) ?>">
                                        <?= htmlspecialchars($course['Titel']) ?>
                                    </a>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mb-0">
                                    <div>
                                        <a href="#" class="badge bg-info bg-opacity-10 text-info me-2">
                                            <i class="fas fa-circle small fw-bold"></i>
                                            <?= htmlspecialchars($course['CategorieNaam'] ?? 'General') ?>
                                        </a>
                                    </div>
                                    <h5 class="text-success mb-0">$<?= rand(100, 300) ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No courses found.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="all-courses.php" class="btn btn-primary-soft">View all courses
                <i class="fas fa-sync ms-2"></i>
            </a>
        </div>
    </div>
</section>