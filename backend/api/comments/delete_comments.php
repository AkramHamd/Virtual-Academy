<?php
require '../../config/db_connection.php';

session_start(); 
header('Content-Type: application/json'); 

try {
    // Verifica si el usuario está logueado y es admin
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
        $data = json_decode(file_get_contents("php://input"));

        // Verifica que se haya enviado comment_id
        if (!empty($data->comment_id)) {
            $comment_id = $data->comment_id; // Cambiar a comment_id

            // Prepara la consulta para eliminar el comentario
            $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->bind_param("i", $comment_id); // Cambiar a comment_id

            // Ejecuta la consulta
            if ($stmt->execute()) {
                // Comprueba si se eliminó algún comentario
                if ($stmt->affected_rows > 0) {
                    echo json_encode(["message" => "Comment deleted successfully."]);
                } else {
                    echo json_encode(["message" => "Comment not found or already deleted."]);
                }
            } else {
                throw new Exception("Failed to delete comment.");
            }

            $stmt->close();
        } else {
            echo json_encode(["message" => "Comment ID is required."]);
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
