<?php
require '../../config/db_connection.php'; // Incluir la conexión a la base de datos

session_start(); // Iniciar sesión

// Obtener los datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que los campos necesarios no estén vacíos
if (!empty($data->id) && !empty($data->name) && !empty($data->description)) {
    // Preparar la consulta SQL para actualizar un curso
    $query = "UPDATE courses SET name = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
    }

    // Vincular los parámetros
    $stmt->bind_param('ssi', $data->name, $data->description, $data->id);
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(array("message" => "Course updated successfully."));
        } else {
            echo json_encode(array("message" => "No changes made or course not found."));
        }
    } else {
        echo json_encode(array("message" => "Failed to update course."));
    }

    $stmt->close(); // Cerrar la declaración
} else {
    echo json_encode(array("message" => "Incomplete data."));
}

$conn->close(); // Cerrar la conexión
?>
