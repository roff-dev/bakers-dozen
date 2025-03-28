<?php
include "connection.php";
header('Content-Type: application/json');

// array to store validation errors
$response = ['success' => false, 'errors' => []];

// check form was submitted via POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //  get and sanitize form inputs
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $testimonial = trim(filter_input(INPUT_POST, 'testimonial', FILTER_SANITIZE_STRING));

    // validate name
    if (empty($name)){
        $response['errors']['name'] = "Name is required";
    } elseif (strlen($name) > 50){
        $response['errors']['name'] = "Name must be less than 50 characters";
    }

    // validate testimonial text
    if(empty($testimonial)){
        $response['errors']['testimonial'] = "Review content is required";
    } elseif(strlen($testimonial) > 500){
        $response['errors']['testimonial'] = "Review must be less than 500 characters";
    }

    // If no errors, proceed with database insertion
    if(empty($response['errors'])){
        try{
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, content, date_added) VALUES(?, ?, NOW())");
            $stmt->execute([$name, $testimonial]);

            $response['success'] = true;

        } catch (PDOException $e){
            $response['errors']['general'] = "An error occurred while saving your review. Please try again.";
        }
    }
}

echo json_encode($response);
exit;
?>