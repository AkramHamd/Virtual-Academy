<?php
require '../../config/db_connection.php';

session_start();
header('Content-Type: application/json');

try {
    // Check if the user is logged in and is an admin
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
        $data = json_decode(file_get_contents("php://input"));

        // Validate course_id
        if (!empty($data->course_id)) {
            $course_id = $data->course_id;

            // Prepare the base query and dynamically add fields to update
            $query = "UPDATE courses SET ";
            $params = [];
            $types = '';

            if (!empty($data->title)) {
                $query .= "title = ?, ";
                $params[] = $data->title;
                $types .= 's';
            }
            if (!empty($data->description)) {
                $query .= "description = ?, ";
                $params[] = $data->description;
                $types .= 's';
            }
            if (!empty($data->category)) {
                $query .= "category = ?, ";
                $params[] = $data->category;
                $types .= 's';
            }

            // Remove the last comma and add the WHERE clause
            $query = rtrim($query, ", ") . " WHERE id = ?";
            $params[] = $course_id;
            $types .= 'i';

            // Prepare and bind the statement
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Course updated successfully."]);
            } else {
                throw new Exception("Failed to update course.");
            }

            $stmt->close();
        } else {
            echo json_encode(["message" => "Course ID is required."]);
        }
    } else {
        echo json_encode(["message" => "Access denied. Admin privileges required."]);
    }
} catch (Exception $e) {
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
