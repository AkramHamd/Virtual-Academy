<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); // Permite solicitudes solo desde localhost:3000
header('Access-Control-Allow-Methods: DELETE, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Permite el envío de credenciales como cookies
header('Content-Type: application/json');
require '../../config/db_connection.php';

session_start(); 
header('Content-Type: application/json'); 

try {
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->course_id)) {
            $course_id = $data->course_id;

            $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->bind_param("i", $course_id);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Course deleted successfully."]);
            } else {
                throw new Exception("Failed to delete course.");
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
