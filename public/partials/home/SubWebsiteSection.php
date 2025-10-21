<?php
$subWebsites = $subWebsiteController->index();
?>

<section>
    <div class="container">
        <div class="row g-4">
            <?php if ($subWebsites && mysqli_num_rows($subWebsites) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($subWebsites)): ?>
                    <?php
                    $title = htmlspecialchars($row['Title']);
                    $link  = htmlspecialchars($row['Link']);
                    $icon  = htmlspecialchars($row['Icon']);
                    ?>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="card card-body shadow rounded-3">
                            <div class="d-flex align-items-center">
                                <!-- Icon -->
                                <div class="icon-lg bg-primary bg-opacity-10 rounded-circle text-primary">
                                    <i class="<?= $icon ?>"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">
                                        <a href="<?= $link ?>" target="_blank" class="stretched-link"><?= $title ?></a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No sub-websites found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
