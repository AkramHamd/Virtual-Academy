<?php
require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Obtener el ID del curso enviado por GET
$course_id = $_GET['course_id'] ?? null; // Usamos null si no está presente

// Verificar que el ID del curso no esté vacío
if (!empty($course_id)) {
    // Verificar si la tabla courses existe y si el curso existe
    $course_check_query = "SELECT id FROM courses WHERE id = ?";
    $course_check_stmt = $conn->prepare($course_check_query);
    $course_check_stmt->bind_param('i', $course_id);
    $course_check_stmt->execute();
    $course_check_stmt->store_result();

    if ($course_check_stmt->num_rows === 0) {
        echo json_encode(array("message" => "The course does not exist."));
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
    $select_query = "SELECT c.comment, c.rating, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.course_id = ?";
    $select_stmt = $conn->prepare($select_query);
    $select_stmt->bind_param('i', $course_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();

    // Verificar si hay comentarios
    if ($result->num_rows > 0) {
        $comments = array();
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row; // Almacenar cada comentario en un array
        }
        echo json_encode(array("comments" => $comments));
    } else {
        echo json_encode(array("message" => "No comments found for this course."));
    }

    // Cerrar declaraciones
    $select_stmt->close();
    $course_check_stmt->close();
    $table_check_stmt->close();
} else {
    echo json_encode(array("message" => "Incomplete data. Course ID is required."));
}

// Cerrar la conexión
$conn->close();
?>
