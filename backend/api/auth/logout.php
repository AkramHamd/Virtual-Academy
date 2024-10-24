<?php
session_start(); // Iniciar la sesión

// Eliminar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Destruir la cookie de sesión en el cliente, si es necesario
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

// Enviar mensaje de confirmación
echo json_encode(array("message" => "Logged out successfully."));
?>
