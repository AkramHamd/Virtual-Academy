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

// Debug: Print session info
error_log("Session user_id: " . print_r($_SESSION['user_id'], true));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Debug: Print the query and user_id
    $query = "SELECT c.* 
              FROM courses c 
              INNER JOIN enrollments e ON c.id = e.course_id 
              WHERE e.user_id = ?";
    
    error_log("User ID: " . $user_id);
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    // Debug: Print the number of courses found
    error_log("Number of courses found: " . count($courses));
    
    echo json_encode($courses);

} catch (Exception $e) {
    error_log("Error in get_enrolled_courses: " . $e->getMessage());
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?> 