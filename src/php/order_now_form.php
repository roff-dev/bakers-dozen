<?php
include 'connection.php';
?>

<div class="form-paragraph-container">
    <h3>Order Now</h3>
    <p>Via this form.</p>
    <div class="alert alert-danger-hidden" id="alert">

    </div>
</div>

<div class="form-list-container">
    <form class="forms-container" name="orderForm" id="order-form" action="../../src/php/orderform.php" onsubmit="return validateForm()" method="POST">
        <div class="form-top">
            <input id="name" class="form form-control" type="text" name="name" placeholder="Name*" value="" required>
        </div>
        <div class="form-bottom">
            <input class="form form-control" id="address" type="text" name="address" placeholder="Address*" value="" required>
        </div>
        <div class="form-contact">
            <input class="form form-control" id="email" type="email" name="email" placeholder="Email Address*" value="" required>
        </div>
        <div class="form-contact">
            <input class="form form-control" id="telephone" type="text" name="telephone" placeholder="Phone Number*" value="" required>
        </div>

        <div class="form-products-wrapper">
            <?php 
                // Query the database to fetch all product details
                $stmt = $pdo->query("SELECT image_url, product_name, price, quantity, recipe_url FROM products");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

        <div class="form-product-grid">  
            <?php foreach ($products as $index => $product): ?>
                <div class="product-card" 
                    data-product="<?php echo htmlspecialchars(json_encode($product)); ?>"
                    data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                    data-price="<?php echo htmlspecialchars($product['price']); ?>"
                    data-stock="<?php echo htmlspecialchars($product['quantity']); ?>">
                    <div class="favourite-icon">
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
                            <label for="quantity-<?php echo $index; ?>">Quantity:</label>
                            <input type="number" class="quantity-input" data-price="<?php echo $product['price']; ?>" data-product-id="<?php echo $product['product_id']; ?>" min="0" max="<?php echo $product['quantity']; ?>" value="0" onchange="calculateTotal()">

                            <!-- Hidden fields to pass product data -->
                            <input type="hidden" name="products[<?php echo $index; ?>][product_id]" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="products[<?php echo $index; ?>][price]" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="products[<?php echo $index; ?>][discount]" value="0.00"> <!-- optional per-product discount -->

                        <?php else: ?>
                            <p class="product-status out-of-stock">Out of Stock</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

        <div class="form-bottom">
            <textarea id="notes" class="form form-end form-control"  name="notes" placeholder="Additional Notes*" value=""></textarea>
        </div>
        <div class="form-bottom">
            <label for="discount">Discount (%)</label>
            <input class="form form-control" id="discount" type="number" name="discount" placeholder="Enter discount" min="0" max="100" value="">
        </div>
        <div class="form-total-price">
            <h3>Total Price: £<span id="total-price">0.00</span></h3>
            <input type="hidden" id="total-price-input" name="total_price" value="0.00">
        </div>
        <div class="form-btn-container">
            <button class="btn-submit" id="submitBtn" type="submit" value="Submit" onclick="calculateTotal()">Submit</button>
        </div>
    </form>

</div>