<?php
session_start(); // Iniciar la sesión

// Destruir la sesión
session_destroy();

// Enviar mensaje de confirmación
echo json_encode(array("message" => "Logged out successfully."));
?>
