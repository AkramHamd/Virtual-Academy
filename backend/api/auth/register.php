<?php
require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Obtener datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Validar datos
if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    // Hashear la contraseña
    $password_hash = password_hash($data->password, PASSWORD_BCRYPT);

    // Comprobar si el usuario ya existe
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $data->email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(array("message" => "User already exists."));
    } else {
        // Insertar en la base de datos
        $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $data->name, $data->email, $password_hash);

        if ($stmt->execute()) {
            echo json_encode(array("message" => "User registered successfully."));
        } else {
            echo json_encode(array("message" => "Unable to register the user."));
        }
    }
} else {
    echo json_encode(array("message" => "Incomplete data."));
}
?>
