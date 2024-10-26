<?php
require '../../config/db_connection.php';

session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté autenticado
if (isset($_SESSION['user_id'])) {
    // Obtener el ID del comentario de los parámetros de la URL
    $comment_id = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : null;

    // Verificar que se haya proporcionado el ID del comentario
    if ($comment_id === null) {
        echo json_encode(array("message" => "Comment ID is required."));
        exit();
    }

    // Preparar la consulta para obtener el comentario específico
    $query = "SELECT * FROM comments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el comentario existe
    if ($result->num_rows > 0) {
        $comment = $result->fetch_assoc();
        echo json_encode($comment);
    } else {
        echo json_encode(array("message" => "Comment not found."));
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo json_encode(array("message" => "User not logged in."));
}

// Cerrar la conexión
$conn->close();
?>
