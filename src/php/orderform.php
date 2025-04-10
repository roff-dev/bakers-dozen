<?php
require_once 'connection.php';
header('Content-Type: application/json');

// Helper to clean input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];
$hasErrors = false;

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate order details
    $name = !empty($_POST["name"]) ? clean_input($_POST["name"]) : $errors['name'] = "Name is required";
    $address = !empty($_POST["address"]) ? clean_input($_POST["address"]) : $errors['address'] = "Address is required";
    $email = !empty($_POST["email"]) ? clean_input($_POST["email"]) : $errors['email'] = "Email is required";
    $telephone = !empty($_POST["telephone"]) ? clean_input($_POST["telephone"]) : $errors['telephone'] = "Telephone is required";
    $notes = !empty($_POST["notes"]) ? clean_input($_POST["notes"]) : $errors['notes'] = "Notes are required";
    $discount = isset($_POST["discount"]) && is_numeric($_POST["discount"]) ? $_POST["discount"] : null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (!preg_match('/^\+?[0-9]{0,3}[-\s\.]?\(?[0-9]{3}\)?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $telephone)) {
        $errors['telephone'] = "Invalid telephone format";
    }

    $totalPrice = isset($_POST['total_price']) && is_numeric($_POST['total_price']) ? $_POST['total_price'] : null;
    if (!$totalPrice) {
        $errors['total_price'] = "Invalid total price";
    }

    $products = $_POST['products'] ?? [];
    if (empty($products)) {
        $errors['products'] = "No products selected";
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    $products = json_decode($_POST['products'], true); // Decode the products array from JSON

    if (empty($products)) {
        echo json_encode(['success' => false, 'error' => 'Missing required product fields.']);
        exit;
    }

try {
    $pdo->beginTransaction();

    // 1. Insert into orders
    $stmt = $pdo->prepare("INSERT INTO orders (name, address, email, telephone, notes, total_price, discount) 
                           VALUES (:name, :address, :email, :telephone, :notes, :total_price, :discount)");
    $stmt->execute([
        ':name' => $name,
        ':address' => $address,
        ':email' => $email,
        ':telephone' => $telephone,
        ':notes' => $notes,
        ':total_price' => $totalPrice,
        ':discount' => $discount
    ]);

    $orderId = $pdo->lastInsertId();

    // Prepare statements
    $insertItem = $pdo->prepare("INSERT INTO order_product 
        (order_id, product_id, quantity, price_at_time, discount) 
        VALUES (:order_id, :product_id, :quantity, :price, :discount)");

    $updateStock = $pdo->prepare("UPDATE products 
        SET quantity = quantity - :quantity 
        WHERE product_id = :product_id AND quantity >= :quantity");

    foreach ($products as $product) {
        // Ensure each field is set and valid
        if (empty($product['product_id']) || !isset($product['quantity']) || !isset($product['price'])) {
            throw new Exception('Missing required product fields.');
        }

        $productId = (int) $product['product_id'];
        $quantity = (int) $product['quantity'];
        $price = (float) $product['price'];
        $itemDiscount = isset($product['discount']) ? (float) $product['discount'] : null;

        if ($quantity <= 0) continue; // Skip items with 0 qty

        // Update stock, check for insufficient inventory
        $updateStock->execute([
            ':product_id' => $productId,
            ':quantity' => $quantity
        ]);
        if ($updateStock->rowCount() === 0) {
            throw new Exception("Insufficient stock for product ID $productId.");
        }

        // Insert into junction table
        $insertItem->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':quantity' => $quantity,
            ':price' => $price,
            ':discount' => $itemDiscount
        ]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Order placed successfully.']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
}

// Assuming products are sent via POST as JSON data
if (isset($_POST['products'])) {
    $products = json_decode($_POST['products'], true);

    // Check if products data is valid
    if (!$products) {
        echo json_encode(['success' => false, 'error' => 'Invalid product data.']);
        exit;
    }
    
    // Debug the products array to see what you're receiving
    error_log(print_r($products, true));  // Logs the array to the PHP error log for inspection
} else {
    echo json_encode(['success' => false, 'error' => 'Products data not received.']);
    exit;
}
?>