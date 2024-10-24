<?php
require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Verificar si la tabla courses existe
$table_check_query = "SHOW TABLES LIKE 'courses'";
$table_check_stmt = $conn->prepare($table_check_query);
$table_check_stmt->execute();
$table_check_stmt->store_result();

if ($table_check_stmt->num_rows === 0) {
    echo json_encode(array("message" => "The 'courses' table does not exist."));
    exit();
}

// Obtener la lista de cursos
$select_query = "SELECT id, name, description, created_by FROM courses";
$select_stmt = $conn->prepare($select_query);
$select_stmt->execute();
$result = $select_stmt->get_result();

// Verificar si hay cursos
if ($result->num_rows > 0) {
    $courses = array();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row; // Almacenar cada curso en un array
    }
    echo json_encode(array("courses" => $courses));
} else {
    echo json_encode(array("message" => "No courses available."));
}

// Cerrar declaraciones
$select_stmt->close();
$table_check_stmt->close();

// Cerrar la conexión
$conn->close();
?>
