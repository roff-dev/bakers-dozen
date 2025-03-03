<?php
include '../php/connection.php';

// Query the database to fetch all product details
$stmt = $pdo->query("SELECT image_url, product_name, price, quantity FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ALL PRODUCT LIST SECTION -->
<section class="product-list-section">
    <div class="container">
        <h2 class="h4 section-title">Our Products</h2>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-product="<?php echo htmlspecialchars(json_encode($product)); ?>">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-price">£<?php echo number_format($product['price'], 2); ?></p>
                        <?php if ($product['quantity'] > 0): ?>
                            <p class="product-status in-stock">In Stock</p>
                        <?php else: ?>
                            <p class="product-status out-of-stock">Out of Stock</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal for expanded product view -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-body">
                <div class="modal-image">
                    <img src="" alt="" id="modal-product-image">
                </div>
                <div class="modal-details">
                    <h3 id="modal-product-name"></h3>
                    <p id="modal-product-price"></p>
                    <p id="modal-product-status"></p>
                    <!-- Placeholder for future recipe and ingredients -->
                    <div class="future-content">
                        <p class="coming-soon">Recipe and ingredients coming soon!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('product-modal');
    const closeBtn = document.querySelector('.close-modal');
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        card.addEventListener('click', function() {
            const productData = JSON.parse(this.dataset.product);
            
            // Update modal content
            document.getElementById('modal-product-image').src = productData.image_url;
            document.getElementById('modal-product-image').alt = productData.product_name;
            document.getElementById('modal-product-name').textContent = productData.product_name;
            document.getElementById('modal-product-price').textContent = `£${Number(productData.price).toFixed(2)}`;
            document.getElementById('modal-product-status').textContent = 
                productData.quantity > 0 ? 'In Stock' : 'Out of Stock';
            
            modal.style.display = 'block';
        });
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>