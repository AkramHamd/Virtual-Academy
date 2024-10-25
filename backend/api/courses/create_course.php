<?php
require '../../config/db_connection.php';

session_start(); // Start the session

header('Content-Type: application/json'); // Set the header to return JSON responses

try {
    // Check if the user is logged in and is an admin
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        
        // Get the data from the POST request
        $data = json_decode(file_get_contents("php://input"));

        // Validate input data (title, description, and category must not be empty)
        if (!empty($data->title) && !empty($data->description) && !empty($data->category)) {
            $title = $data->title;
            $description = $data->description;
            $category = $data->category;

            // Check if the course title already exists
            $name_check_query = "SELECT id FROM courses WHERE title = ?";
            $name_check_stmt = $conn->prepare($name_check_query);
            $name_check_stmt->bind_param('s', $title);
            $name_check_stmt->execute();
            $name_check_stmt->store_result();

            if ($name_check_stmt->num_rows > 0) {
                // Course title already exists
                echo json_encode(array("message" => "Course title already in use."));
            } else {
                // Insert the new course into the database
                $insert_query = "INSERT INTO courses (title, description, category) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                
                if (!$insert_stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }

                $insert_stmt->bind_param('sss', $title, $description, $category);

                if ($insert_stmt->execute()) {
                    echo json_encode(array("message" => "Course created successfully."));
                } else {
                    throw new Exception("Error creating course: " . $insert_stmt->error);
                }

                // Close statement
                $insert_stmt->close();
            }

            // Close the name check statement
            $name_check_stmt->close();

        } else {
            // Input validation failed
            echo json_encode(array("message" => "Incomplete input data. Please provide title, description, and category."));
        }

    } else {
        // User is not logged in or not an admin
        echo json_encode(array("message" => "Access denied. Admin privileges required."));
    }

} catch (Exception $e) {
    // Catch any unexpected errors and return a JSON error message
    echo json_encode(array("message" => "An error occurred: " . $e->getMessage()));
} finally {
    // Close the database connection
    $conn->close();
}
?>
