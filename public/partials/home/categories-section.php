<section>
    <div class="container">
        <div class="row g-4"> <!-- Category item -->
            <div class="row g-4">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {

                        // Variables
                        $name = htmlspecialchars($row['Naam']);
                        $count = (int) $row['TotalCourses'];

                        echo '
                                       <div class="col-sm-6 col-lg-4 col-xl-3">
                                           <div class="card card-body shadow rounded-3">
                                               <div class="d-flex align-items-center">
                                                   <div class="ms-3">
                                                       <h5 class="mb-0"><a href="categorie-page.php?id=' . $row['CategorieID'] . '" class="stretched-link">' . $name . '</a></h5>
                                                       <span>' . $count . ' Courses</span>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>';
                    }
                } else {
                    echo '<p>No categories found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>