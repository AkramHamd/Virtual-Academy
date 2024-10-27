<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Obtener los datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que el email y la contraseña no estén vacíos
if (!empty($data->email) && !empty($data->password)) {
    // Buscar al usuario por su email
    $query = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
    }

    $stmt->bind_param('s', $data->email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $email, $password_hash, $role);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($data->password, $password_hash)) {
            // Guardar información del usuario en la sesión
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = $role;

            echo json_encode(array("message" => "Login successful."));
        } else {
            echo json_encode(array("message" => "Invalid password."));
        }
    } else {
        echo json_encode(array("message" => "User not found."));
    }
} else {
    echo json_encode(array("message" => "Incomplete data."));
}
?>
