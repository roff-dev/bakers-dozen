<?php
include '../php/connection.php';

// Query the database to fetch all product details
$stmt = $pdo->query("SELECT image_url, product_name, price, quantity FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ALL PRODUCT LIST SECTION -->
<section class="product-list-section">
    <div class="container">
        <div class="product-header">
            <h2 class="h4 section-title">Our Products</h2>
            <div class="product-controls">
                <div class="filter-dropdown">
                    <select id="product-filter">
                        <option value="default">Sort by...</option>
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="price-asc">Price (Low to High)</option>
                        <option value="price-desc">Price (High to Low)</option>
                        <option value="stock">In Stock First</option>
                    </select>
                </div>
                <button class="favorites-toggle" id="favorites-toggle">
                    <i class="fa-regular fa-star"></i>
                    <span>Favorites</span>
                </button>
            </div>
        </div>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" 
                     data-product="<?php echo htmlspecialchars(json_encode($product)); ?>"
                     data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                     data-price="<?php echo htmlspecialchars($product['price']); ?>"
                     data-stock="<?php echo htmlspecialchars($product['quantity']); ?>">
                    <div class="favorite-icon">
                        <i class="fa-star fa-regular"></i>
                    </div>
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
                    <!-- Placeholder for recipe and ingredients -->
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
    const filterSelect = document.getElementById('product-filter');
    const favoritesToggle = document.getElementById('favorites-toggle');
    const productGrid = document.querySelector('.product-grid');
    
    // Initialize favorites from localStorage
    let favorites = new Set(JSON.parse(localStorage.getItem('favorites') || '[]'));
    
    // Update favorite icons based on stored favorites
    function updateFavoriteIcons() {
        productCards.forEach(card => {
            const productData = JSON.parse(card.dataset.product);
            const isFavorite = favorites.has(productData.product_name);
            const starIcon = card.querySelector('.fa-star');
            starIcon.classList.toggle('fa-solid', isFavorite);
            starIcon.classList.toggle('fa-regular', !isFavorite);
        });
    }
    
    // Initialize favorite icons
    updateFavoriteIcons();
    
    // Handle favorite toggling
    productCards.forEach(card => {
        const favoriteIcon = card.querySelector('.favorite-icon');
        favoriteIcon.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent modal from opening
            const productData = JSON.parse(card.dataset.product);
            const productName = productData.product_name;
            
            if (favorites.has(productName)) {
                favorites.delete(productName);
            } else {
                favorites.add(productName);
            }
            
            // Update localStorage
            localStorage.setItem('favorites', JSON.stringify([...favorites]));
            updateFavoriteIcons();
        });
        
        // Show product details in modal
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.favorite-icon')) {
                const productData = JSON.parse(this.dataset.product);
                document.getElementById('modal-product-image').src = productData.image_url;
                document.getElementById('modal-product-image').alt = productData.product_name;
                document.getElementById('modal-product-name').textContent = productData.product_name;
                document.getElementById('modal-product-price').textContent = `£${Number(productData.price).toFixed(2)}`;
                document.getElementById('modal-product-status').textContent = 
                    productData.quantity > 0 ? 'In Stock' : 'Out of Stock';
                modal.style.display = 'block';
            }
        });
    });
    
    // Filter products
    filterSelect.addEventListener('change', function() {
        const cards = Array.from(productCards);
        
        cards.sort((a, b) => {
            const aData = {
                name: a.dataset.name,
                price: parseFloat(a.dataset.price),
                stock: parseInt(a.dataset.stock)
            };
            const bData = {
                name: b.dataset.name,
                price: parseFloat(b.dataset.price),
                stock: parseInt(b.dataset.stock)
            };
            
            switch(this.value) {
                case 'name-asc':
                    return aData.name.localeCompare(bData.name);
                case 'name-desc':
                    return bData.name.localeCompare(aData.name);
                case 'price-asc':
                    return aData.price - bData.price;
                case 'price-desc':
                    return bData.price - aData.price;
                case 'stock':
                    return bData.stock - aData.stock;
                default:
                    return 0;
            }
        });
        
        // Reappend sorted cards
        cards.forEach(card => productGrid.appendChild(card));
    });
    
    // Toggle favorites view
    favoritesToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        productCards.forEach(card => {
            const productData = JSON.parse(card.dataset.product);
            if (this.classList.contains('active')) {
                card.style.display = favorites.has(productData.product_name) ? '' : 'none';
            } else {
                card.style.display = '';
            }
        });
    });
    
    // Modal close handlers
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