<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_id'])) {
    // El usuario está autenticado
    echo json_encode(array(
        "id" => $_SESSION['user_id'],
        "name" => $_SESSION['user_name'],
        "email" => $_SESSION['user_email']
    ));
} else {
    // El usuario no está autenticado
    echo json_encode(array("message" => "Access denied. User is not logged in."));
}
?>
