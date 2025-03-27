<?php
include '../php/connection.php';

// have set it so it will load a max of 5 random reviews from the database
$stmt = $pdo->query("SELECT name, content, date_added FROM testimonials ORDER BY RAND() LIMIT 5");
$result = $stmt->fetchAll();

?>

<main class="testimonial-page">
    <h1>What our customers say</h1>
    <div class="testimonial-page-content">
        
        <?php
        // success message on succesful form submit
        if (isset($_GET['success']) && $_GET['success'] == 1){
            echo '<div class="success-message>"
                    Thank you for giving your feedback!
                </div>';
        }
        ?>

        <div class="testimonials">
            <?php
                if(count($result) > 0){
                    foreach($result as $row){
                        echo '<div class="review">';
                        echo '<p>"' . htmlspecialchars($row["content"]) . '"</p>';
                        echo '<h4>- ' . htmlspecialchars($row["name"]) . '</h4>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No testimonials yet</p>";
                }
            ?>
        </div>

        <div class="add-testimonial">
            <h2>Share your experience</h2>
            <form class="testimonial-form" action="submit_testimonial.php" method="POST">
                <div class="testimonial-form-div">
                    <!-- <label for="name">Your Name:</label> -->
                    <input type="text" id="name" name="name" placeholder="Your Name" required>
                </div>
                <div class="testimonial-form-div">
                    <!-- <label for="testimonial">Your Review:</label> -->
                    <textarea id="testimonial-textarea" name="testimonial" rows="5" placeholder="Your Review" required></textarea>
                </div>
                <button class="testimonial-form-button" type="submit">Submit Review</button>
            </form>
        </div>
    </div>
</main>
