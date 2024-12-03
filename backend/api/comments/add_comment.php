<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); // Permite solicitudes solo desde localhost:3000
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Permite el envío de credenciales como cookies
header('Content-Type: application/json');

require '../../config/db_connection.php';

// Iniciar la sesión (si se utiliza la sesión para otras operaciones)
session_start();

// Obtener los datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que el ID del curso, el comentario y la calificación son válidos
if (!empty($data->user_id) && !empty($data->course_id) && !empty($data->comment) && isset($data->rating)) {
    $user_id = $data->user_id; // Usar el user_id enviado por el frontend
    $course_id = $data->course_id;
    $comment = $data->comment;
    $rating = $data->rating;

    // Verificar que la calificación está entre 1 y 10
    if ($rating < 1 || $rating > 10) {
        echo json_encode(array("message" => "Rating must be between 1 and 10."));
        exit();
    }

    // Verificar si el curso existe
    $course_check_query = "SELECT id FROM courses WHERE id = ?";
    $course_check_stmt = $conn->prepare($course_check_query);
    $course_check_stmt->bind_param('i', $course_id);
    $course_check_stmt->execute();
    $course_check_stmt->store_result();

    if ($course_check_stmt->num_rows === 0) {
        echo json_encode(array("message" => "The course does not exist."));
        $course_check_stmt->close();
        $conn->close();
        exit();
    }

    // Verificar si el usuario existe
    $user_check_query = "SELECT name FROM users WHERE id = ?"; // Solo obtenemos el nombre del usuario
    $user_check_stmt = $conn->prepare($user_check_query);
    $user_check_stmt->bind_param('i', $user_id);
    $user_check_stmt->execute();
    $user_check_stmt->bind_result($nameuser);
    $user_check_stmt->fetch();

    $user_check_stmt->close();

    // Añadir el comentario a la base de datos
    $insert_query = "INSERT INTO comments (user_id, nameuser, course_id, comment, rating) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    
    if (!$insert_stmt) {
        echo json_encode(array("message" => "Error preparing statement: " . $conn->error));
        $conn->close();
        exit();
    }

    // Asociar el comentario con el nombre del usuario
    $insert_stmt->bind_param('isisi', $user_id, $nameuser, $course_id, $comment, $rating);
    
    if ($insert_stmt->execute()) {
        $new_comment_id = $conn->insert_id; // Obtener el ID generado automáticamente
        echo json_encode(array(
            "message" => "Comment added successfully.",
            "id" => $new_comment_id // Devolver el ID al frontend
        ));
    } else {
        echo json_encode(array("message" => "Error adding comment: " . $insert_stmt->error));
    }

    $insert_stmt->close();
    $course_check_stmt->close();
} else {
    echo json_encode(array("message" => "Incomplete data."));
}

$conn->close();
?>
