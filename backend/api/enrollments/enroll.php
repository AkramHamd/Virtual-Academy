<?php

// Update CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ... rest of your code ...

require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Obtener los datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que el usuario está autenticado y que el ID del curso no está vacío
if (isset($_SESSION['user_id']) && !empty($data->course_id)) {
    $user_id = $_SESSION['user_id'];
    $course_id = $data->course_id;

    // Verificar si la tabla enrollments existe
    $table_check_query = "SHOW TABLES LIKE 'enrollments'";
    $table_check_stmt = $conn->prepare($table_check_query);
    $table_check_stmt->execute();
    $table_check_stmt->store_result();

    if ($table_check_stmt->num_rows === 0) {
        echo json_encode(array("message" => "The 'enrollments' table does not exist."));
        exit();
    }

    // Verificar que el curso existe
    $course_check_query = "SELECT id FROM courses WHERE id = ?";
    $course_check_stmt = $conn->prepare($course_check_query);
    $course_check_stmt->bind_param('i', $course_id);
    $course_check_stmt->execute();
    $course_check_stmt->store_result();

    if ($course_check_stmt->num_rows === 0) {
        echo json_encode(array("message" => "Course not found."));
        exit();
    }

    // Verificar si el usuario ya está inscrito en el curso
    $check_query = "SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('ii', $user_id, $course_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo json_encode(array("message" => "User already enrolled in this course."));
    } else {
        // Inscribir al usuario en el curso
        $enroll_query = "INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)";
        $enroll_stmt = $conn->prepare($enroll_query);
        
        if (!$enroll_stmt) {
            die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
        }

        $enroll_stmt->bind_param('ii', $user_id, $course_id);
        if ($enroll_stmt->execute()) {
            echo json_encode(array("message" => "Enrollment successful."));
        } else {
            echo json_encode(array("message" => "Error enrolling user: " . $enroll_stmt->error));
        }
        $enroll_stmt->close();
    }

    // Cerrar declaraciones
    $check_stmt->close();
    $course_check_stmt->close();
    $table_check_stmt->close();
} else {
    echo json_encode(array("message" => "User not logged in or incomplete data."));
}

// Cerrar la conexión
$conn->close();
?>
