<?php
require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

// Obtener datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que se envió el email y la contraseña
if (!empty($data->email) && !empty($data->password)) {
    // Buscar el usuario por su email
    $query = "SELECT id, name, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $data->email);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $email, $password_hash);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($data->password, $password_hash)) {
            // Guardar datos en la sesión
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

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
