<?php
include '../php/connection.php';

$stmt = $pdo->query("SELECT name, content, date_added FROM testimonials ORDER BY date_added DESC");
$result = $stmt->fetchAll();

?>

<main class="testimonial-page">
    <h1>What our customers say</h1>

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

    <div>

    </div>
</main>
