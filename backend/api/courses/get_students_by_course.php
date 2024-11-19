<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); // Permite solicitudes solo desde el origen específico
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Permite el envío de cookies o credenciales
header('Content-Type: application/json');
require '../../config/db_connection.php';

// Obtener el ID del curso de los parámetros GET
$course_id = $_GET['course_id'] ?? null;

if (empty($course_id)) {
    echo json_encode(array("message" => "Course ID is required."));
    exit();
}

// Obtener los estudiantes del curso
$students_query = "
    SELECT u.id, u.name 
    FROM users u
    INNER JOIN enrollments e ON u.id = e.user_id
    WHERE e.course_id = ?
";

$students_stmt = $conn->prepare($students_query);
$students_stmt->bind_param('i', $course_id);
$students_stmt->execute();
$result = $students_stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

// Cerrar declaraciones
$students_stmt->close();
// Cerrar la conexión
$conn->close();
?>
