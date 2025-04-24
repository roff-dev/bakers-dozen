<?php
require_once 'connection.php';
header('Content-Type: application/json');

// Enable detailed error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Helper to clean input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Debug function to log data
function debug_log($label, $data) {
    error_log($label . ': ' . print_r($data, true));
}

$errors = [];
$hasErrors = false;

// Log all POST data for debugging
debug_log('All POST data', $_POST);

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Step 1: Validate basic form fields
        $name = !empty($_POST["name"]) ? clean_input($_POST["name"]) : null;
        $address = !empty($_POST["address"]) ? clean_input($_POST["address"]) : null;
        $email = !empty($_POST["email"]) ? clean_input($_POST["email"]) : null;
        $telephone = !empty($_POST["telephone"]) ? clean_input($_POST["telephone"]) : null;
        $notes = !empty($_POST["notes"]) ? clean_input($_POST["notes"]) : null;
        $discount = isset($_POST["discount"]) && is_numeric($_POST["discount"]) ? $_POST["discount"] : 0;
        $totalPrice = isset($_POST['total_price']) && is_numeric($_POST['total_price']) ? $_POST['total_price'] : 0;

        // Basic validation
        if (!$name) $errors['name'] = "Name is required";
        if (!$address) $errors['address'] = "Address is required";
        if (!$email) $errors['email'] = "Email is required";
        if (!$telephone) $errors['telephone'] = "Telephone is required";
        if (!$notes) $errors['notes'] = "Notes are required";
        if ($totalPrice <= 0) $errors['total_price'] = "Total price must be greater than 0";

        // Additional validation for email and phone
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }

        if ($telephone && !preg_match('/^\+?[0-9]{0,3}[-\s\.]?\(?[0-9]{3}\)?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $telephone)) {
            $errors['telephone'] = "Invalid telephone format";
        }

        // Step 2: Validate products data
        $productsData = null;
        if (isset($_POST['products'])) {
            $productsData = json_decode($_POST['products'], true);
            if (!$productsData || !is_array($productsData) || empty($productsData)) {
                $errors['products'] = "No products selected or invalid product data";
            }
        } else {
            $errors['products'] = "No products data received";
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        // Step 3: Process the order if all validation passes
        $pdo->beginTransaction();

        // Insert into orders table
        $orderStmt = $pdo->prepare("
            INSERT INTO orders (name, address, email, telephone, notes, discount, total_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $orderStmt->execute([
            $name,
            $address,
            $email,
            $telephone,
            $notes,
            $discount,
            $totalPrice
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Prepare statements for products
        $insertOrderProduct = $pdo->prepare("
            INSERT INTO order_product (order_id, product_id, quantity, price_at_time, discount)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $updateStock = $pdo->prepare("
            UPDATE products
            SET quantity = quantity - ?
            WHERE product_id = ? AND quantity >= ?
        ");
        
        // Process each product
        foreach ($productsData as $product) {
            // Extract product data
            $productId = isset($product['product_id']) ? (int)$product['product_id'] : null;
            $quantity = isset($product['quantity']) ? (int)$product['quantity'] : 0;
            $price = isset($product['price']) ? (float)$product['price'] : 0;
            $itemDiscount = isset($product['discount']) ? (float)$product['discount'] : 0;
            
            // Skip if no quantity
            if ($quantity <= 0) continue;
            
            // Skip if missing required fields
            if (!$productId || $price <= 0) {
                throw new Exception("Invalid product data: Missing product ID or price.");
            }
            
            // Update product stock
            $updateStock->execute([$quantity, $productId, $quantity]);
            
            // Check if stock update was successful
            if ($updateStock->rowCount() === 0) {
                throw new Exception("Insufficient stock for product ID: $productId");
            }
            
            // Insert into order_product junction table
            $insertOrderProduct->execute([
                $orderId, 
                $productId, 
                $quantity, 
                $price, 
                $itemDiscount
            ]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Return success
        echo json_encode([
            'success' => true, 
            'message' => 'Order placed successfully.',
            'order_id' => $orderId
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Return error
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
    }
} else {
    // Not a POST request
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid request method. Only POST is supported.'
    ]);
}
?>