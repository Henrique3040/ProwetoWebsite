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
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $catName = htmlspecialchars($cat['Naam']);
                    $catId = (int) $cat['CategorieID'];
                    $total = isset($cat['TotalCourses']) ? (int) $cat['TotalCourses'] : count($cat['courses'] ?? []);
                    ?>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="card card-metro overflow-hidden rounded-3">
                            <img src="assets/images/courses/4by3/01.jpg" alt="<?= $catName ?>">
                            <div class="card-img-overlay d-flex">
                                <div class="mt-auto card-text">
                                    <a href="categorie-page.php?id=<?= $catId ?>"
                                        class="text-white mt-auto h5 stretched-link"><?= $catName ?></a>
                                    <div class="text-white"><?= $total ?> Courses</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No categories found.</p>
                </div>
            <?php endif; ?>

        </div> <!-- Row END -->
    </div>
</section>