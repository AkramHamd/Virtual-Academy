<?php
// Add CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require '../../config/db_connection.php';
session_start();
header('Content-Type: application/json');

try {
    if (isset($_SESSION['user_id'])) {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->course_id)) {
            $course_id = $data->course_id;

            $stmt = $conn->prepare("SELECT id, title, description, category, created_at FROM courses WHERE id = ?");
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $course = $result->fetch_assoc();
                echo json_encode($course);
            } else {
                echo json_encode(["message" => "Course not found."]);
            }

            $stmt->close();
        } else {
            echo json_encode(["message" => "Course ID is required."]);
        }
    } else {
        echo json_encode(["message" => "Access denied. Please log in to view course details."]);
    }
} catch (Exception $e) {
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
