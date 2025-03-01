<?php
include '../php/connection.php';

// Query the database to fetch the products
$stmt = $pdo->query("SELECT image_url, product_name FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section>
    <div class="our-products">
        <h4 class="h4">our products</h4>
        <?php
// Start the product carousel
echo '<div class="product-carousel">';

// Loop through each product and append to the HTML structure
foreach ($products as $product) {
    echo '
    <div>
        <img src="' . htmlspecialchars($product['image_url']) . '" alt="' . htmlspecialchars($product['product_name']) . '">
        <h4 class="h4">' . htmlspecialchars($product['product_name']) . '</h4>
    </div>';
}

// Close the product carousel
echo '</div>';
?>
        <div class="find-more-btn">
            <button>find out more</button>
        </div>
    </div>
</section>
