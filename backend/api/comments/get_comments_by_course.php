<?php
require '../../config/db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); // Return 200 OK with the CORS headers for preflight
}

// Obtener el ID del curso de los parámetros GET
$course_id = $_GET['course_id'] ?? null;

if (empty($course_id)) {
    echo json_encode(array("message" => "Course ID is required."));
    exit();
}

// Verificar si la tabla comments existe
$table_check_query = "SHOW TABLES LIKE 'comments'";
$table_check_stmt = $conn->prepare($table_check_query);
$table_check_stmt->execute();
$table_check_stmt->store_result();

if ($table_check_stmt->num_rows === 0) {
    echo json_encode(array("message" => "The 'comments' table does not exist."));
    exit();
}

// Obtener los comentarios del curso
$comment_query = "SELECT id, user_id, nameuser, comment, rating FROM comments WHERE course_id = ?";
$comment_stmt = $conn->prepare($comment_query);
$comment_stmt->bind_param('i', $course_id);
$comment_stmt->execute();
$result = $comment_stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode($comments);

// Cerrar declaraciones
$comment_stmt->close();
$table_check_stmt->close();
// Cerrar la conexión
$conn->close();
?>
