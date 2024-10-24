<?php
require '../../config/db_connection.php'; // Incluir la conexión a la base de datos

session_start(); // Iniciar sesión

// Obtener el ID del curso enviado por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que el ID no esté vacío
if (!empty($data->id)) {
    // Preparar la consulta SQL para eliminar un curso
    $query = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
    }

    // Vincular el parámetro
    $stmt->bind_param('i', $data->id);
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(array("message" => "Course deleted successfully."));
        } else {
            echo json_encode(array("message" => "Course not found."));
        }
    } else {
        echo json_encode(array("message" => "Failed to delete course."));
    }

    $stmt->close(); // Cerrar la declaración
} else {
    echo json_encode(array("message" => "Incomplete data."));
}

$conn->close(); // Cerrar la conexión
?>
