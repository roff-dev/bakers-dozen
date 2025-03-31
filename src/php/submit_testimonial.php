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

    // If no error from above validation proceed with saving to the database
    if(empty($response['errors'])){
        try{
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, content, date_added) VALUES(?, ?, NOW())");
            $stmt->execute([$name, $testimonial]);

            // Get 5 random reviews after inserting new one
            $reviewStmt = $pdo->query("SELECT name, content, date_added FROM testimonials ORDER BY RAND() LIMIT 5");
            $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

            // changes $response success to true and tells js that validation cleared
            $response['success'] = true;
            // adds the 5 random testimonials to $response['reviews'] to be displayed
            $response['reviews'] = $reviews;

        } catch (PDOException $e){
            $response['errors']['general'] = "An error occurred while saving your review. Please try again.";
        }
    }
}

// encodes $response to json to be sent back to the client
echo json_encode($response);
exit;
?>