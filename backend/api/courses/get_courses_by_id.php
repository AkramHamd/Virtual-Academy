<?php
require '../../config/db_connection.php'; // Incluir la conexión a la base de datos

session_start(); // Iniciar sesión

// Obtener el ID del curso enviado por GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar que el ID sea válido
if ($id > 0) {
    // Preparar la consulta SQL para obtener un curso por ID
    $query = "SELECT id, name, description FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
    }

    // Vincular el parámetro
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    // Almacenar el resultado
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc(); // Obtener el curso
        echo json_encode($course); // Devolver el curso en formato JSON
    } else {
        echo json_encode(array("message" => "Course not found."));
    }

    $stmt->close(); // Cerrar la declaración
} else {
    echo json_encode(array("message" => "Invalid course ID."));
}

$conn->close(); // Cerrar la conexión
?>
