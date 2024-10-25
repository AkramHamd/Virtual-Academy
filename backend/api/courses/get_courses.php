<?php
require '../../config/db_connection.php';

session_start();
header('Content-Type: application/json');

try {
    if (isset($_SESSION['user_id'])) {
        $result = $conn->query("SELECT id, title, description, category, created_at FROM courses");

        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        echo json_encode($courses);
    } else {
        echo json_encode(["message" => "Access denied. Please log in to view courses."]);
    }
} catch (Exception $e) {
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
