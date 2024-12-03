<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); // Return 200 OK with the CORS headers for preflight
}


session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_id'])) {
    // El usuario está autenticado
    echo json_encode(array(
        "id" => $_SESSION['user_id'],
        "name" => $_SESSION['user_name'],
        "email" => $_SESSION['user_email'],
        "role" => $_SESSION['role']
    ));
} else {
    // El usuario no está autenticado
    echo json_encode(array("message" => "Access denied. User is not logged in."));
}
?>
