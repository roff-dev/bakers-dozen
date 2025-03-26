<?php
require_once 'connection.php';

header('Content-Type: application/json');

// Initialize variables and error flags
$errors = [];
$hasErrors = false;

// Function to sanitize input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $errors['name'] = "Name is required";
        $hasErrors = true;
    } else {
        $name = clean_input($_POST["name"]);
    }

    // Validate address
    if (empty($_POST["address"])) {
        $errors['address'] = "Address is required";
        $hasErrors = true;
    } else {
        $address = clean_input($_POST["address"]);
    }

    // Validate email
    if (empty($_POST["email"])) {
        $errors['email'] = "Email is required";
        $hasErrors = true;
    } else {
        $email = clean_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
            $hasErrors = true;
        }
    }

    // Validate telephone number
    if (empty($_POST["telephone"])) {
        $errors['telephone'] = "Telephone number is required";
        $hasErrors = true;
    } else {
        $telephone = clean_input($_POST["telephone"]);
        if (!preg_match('/^\+?[0-9]{0,3}[-\s\.]?\(?[0-9]{3}\)?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $telephone)) {
            $errors['telephone'] = "Invalid telephone number format";
            $hasErrors = true;
        }
    }

    // Validate message (notes)
    if (empty($_POST["notes"])) {
        $errors['notes'] = "Notes are required";
        $hasErrors = true;
    } else {
        $notes = clean_input($_POST["notes"]);
    }

    // Validate total price
    if (empty($_POST['total_price']) || !is_numeric($_POST['total_price'])) {
        $errors['total_price'] = "Invalid total price"; 
        $hasErrors = true;
    } else {
        $totalPrice = $_POST['total_price'];
    }

    // Retrieve the discount value from the form
    $discount = isset($_POST["discount"]) ? $_POST["discount"] : NULL;

    // If there are no errors, insert the data into the database
    if (!$hasErrors) {
        try {
            // Prepare SQL insert statement
            $stmt = $pdo->prepare("INSERT INTO orders (name, address, email, telephone, notes, total_price, discount) 
            VALUES (:name, :address, :email, :telephone, :notes, :total_price, :discount)");

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':total_price', $totalPrice);
            $stmt->bindParam(':discount', $discount, PDO::PARAM_NULL); // Ensure discount is correctly bound

            // Execute the query
            $stmt->execute();

            // Return success response
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            // Handle database error
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // If validation fails, return the validation errors as JSON
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
    
    // End the script to avoid any further output
    exit;
}
