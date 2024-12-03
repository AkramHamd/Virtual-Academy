<?php
require '../../config/db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); // Return 200 OK with the CORS headers for preflight
}

session_start();
header('Content-Type: application/json');

try {
    // Verificar si el usuario está autenticado y es administrador
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
        $data = json_decode(file_get_contents("php://input"));

        // Validar el comment_id
        if (!empty($data->comment_id)) {
            $comment_id = $data->comment_id;

            // Verificar si el comentario existe en la base de datos
            $check_query = "SELECT id FROM comments WHERE id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param('i', $comment_id);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows === 0) {
                echo json_encode(["message" => "Comment not found."]);
                exit();
            }
            
            // Construir la consulta de actualización dinámicamente
            $query = "UPDATE comments SET ";
            $params = [];
            $types = '';

            if (!empty($data->comment)) {
                $query .= "comment = ?, ";
                $params[] = $data->comment;
                $types .= 's';
            }
            if (!empty($data->rating)) {
                // Verificar que el rating esté en un rango válido
                if ($data->rating < 1 || $data->rating > 10) {
                    echo json_encode(["message" => "Rating must be between 1 and 10."]);
                    exit();
                }
                $query .= "rating = ?, ";
                $params[] = $data->rating;
                $types .= 'i';
            }

            // Quitar la última coma y añadir la cláusula WHERE
            $query = rtrim($query, ", ") . " WHERE id = ?";
            $params[] = $comment_id;
            $types .= 'i';

            // Preparar y ejecutar la declaración
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Comment updated successfully."]);
            } else {
                throw new Exception("Failed to update comment.");
            }

            $stmt->close();
            $check_stmt->close();
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