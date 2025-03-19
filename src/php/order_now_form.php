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
        <div class="form-bottom">
            <input id="subject" class="form form-control" type="text" name="subject" placeholder="Subject*" value="">
        </div>

        <div class="form-bottom">
            <textarea id="message" class="form form-end form-control"  name="message" placeholder="Message*" value=""></textarea>
        </div>
        <div class="form-btn-container">
            <button class="btn-submit" id="submitBtn" type="button" value="Submit">Submit</button>
        </div>
    </form>

    <?php 
        // Query the database to fetch the products
        $stmt = $pdo->query("SELECT image_url, product_name FROM products");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="form-product-grid">  

            <?php foreach ($products as $product): ?>
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
                            <p class="product-status in-stock">In Stock</p>
                        <?php else: ?>
                            <p class="product-status out-of-stock">Out of Stock</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
</div>