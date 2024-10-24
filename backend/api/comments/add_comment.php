<?php
require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Obtener los datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que el usuario está autenticado, que el ID del curso no está vacío, que el comentario no está vacío y que la calificación es válida
if (isset($_SESSION['user_id']) && !empty($data->course_id) && !empty($data->comment) && isset($data->rating)) {
    $user_id = $_SESSION['user_id'];
    $course_id = $data->course_id;
    $comment = $data->comment;
    $rating = $data->rating;

    // Verificar que la calificación está entre 1 y 10
    if ($rating < 1 || $rating > 10) {
        echo json_encode(array("message" => "Rating must be between 1 and 10."));
        exit();
    }

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

    // Añadir el comentario a la base de datos
    $insert_query = "INSERT INTO comments (user_id, course_id, comment, rating) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    
    if (!$insert_stmt) {
        die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
    }

    $insert_stmt->bind_param('iisi', $user_id, $course_id, $comment, $rating);
    if ($insert_stmt->execute()) {
        echo json_encode(array("message" => "Comment added successfully."));
    } else {
        echo json_encode(array("message" => "Error adding comment: " . $insert_stmt->error));
    }

    // Cerrar declaraciones
    $insert_stmt->close();
    $course_check_stmt->close();
    $table_check_stmt->close();
} else {
    echo json_encode(array("message" => "User not logged in or incomplete data."));
}

// Cerrar la conexión
$conn->close();
?>
