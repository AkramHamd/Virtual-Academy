<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require '../../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "User not logged in"]);
    exit();
}

if (!isset($_GET['course_id'])) {
    echo json_encode(["message" => "Course ID is required"]);
    exit();
}

$course_id = $_GET['course_id'];

try {
    $query = "SELECT id, title, video_url, support_material_url 
              FROM modules 
              WHERE course_id = ? 
              ORDER BY id ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }

    echo json_encode($modules);

} catch (Exception $e) {
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?> 