<?php

// Allow only specific origin (your React app's address)
header("Access-Control-Allow-Origin: http://localhost:3000");
// Allow credentials to be included (cookies, authorization headers, etc.)
header("Access-Control-Allow-Credentials: true");
// Allow necessary HTTP methods
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
// Allow necessary headers
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Handle preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); // Return 200 OK with the CORS headers for preflight
}



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
