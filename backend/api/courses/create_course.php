<?php
require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Obtener los datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que el nombre del curso y la descripción no estén vacíos
if (isset($_SESSION['user_id']) && !empty($data->name) && !empty($data->description)) {
    $user_id = $_SESSION['user_id'];
    $name = $data->name;
    $description = $data->description;

    // Verificar si la tabla courses existe
    $table_check_query = "SHOW TABLES LIKE 'courses'";
    $table_check_stmt = $conn->prepare($table_check_query);
    $table_check_stmt->execute();
    $table_check_stmt->store_result();

    if ($table_check_stmt->num_rows === 0) {
        echo json_encode(array("message" => "The 'courses' table does not exist."));
        exit();
    }

    // Verificar si ya existe un curso con el mismo nombre
    $name_check_query = "SELECT id FROM courses WHERE name = ?";
    $name_check_stmt = $conn->prepare($name_check_query);
    $name_check_stmt->bind_param('s', $name);
    $name_check_stmt->execute();
    $name_check_stmt->store_result();

    if ($name_check_stmt->num_rows > 0) {
        echo json_encode(array("message" => "Course name already in use."));
        exit();
    }

    // Insertar el nuevo curso en la base de datos
    $insert_query = "INSERT INTO courses (name, description, created_by) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);

    if (!$insert_stmt) {
        die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
    }

    $insert_stmt->bind_param('ssi', $name, $description, $user_id);
    if ($insert_stmt->execute()) {
        echo json_encode(array("message" => "Course created successfully."));
    } else {
        echo json_encode(array("message" => "Error creating course: " . $insert_stmt->error));
    }

    // Cerrar declaraciones
    $insert_stmt->close();
    $name_check_stmt->close();
    $table_check_stmt->close();
} else {
    echo json_encode(array("message" => "User not logged in or incomplete data."));
}

// Cerrar la conexión
$conn->close();
?>
