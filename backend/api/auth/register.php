<?php
require '../../config/db_connection.php'; // Incluir conexión a la base de datos

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


session_start(); // Iniciar la sesión

// Verificar si la conexión está activa antes de continuar
if ($conn->connect_errno) {
    die(json_encode(array("message" => "Database connection error: " . $conn->connect_error)));
} else {
    // echo "Connected successfully to the database\n"; // Comprobación de depuración
}

// Obtener datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Validar que los datos no estén vacíos
if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    // Hashear la contraseña
    $password_hash = password_hash($data->password, PASSWORD_BCRYPT);

    // Comprobar si el usuario ya existe
    $query = "SELECT * FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {  // Verifica si la consulta se prepara correctamente
        $stmt->bind_param('s', $data->email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode(array("message" => "User already exists."));
        } else {
            // Insertar en la base de datos
            $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
            }
            $stmt->bind_param('sss', $data->name, $data->email, $password_hash);

            if ($stmt->execute()) {
                echo json_encode(array("message" => "User registered successfully."));
            } else {
                echo json_encode(array("message" => "Unable to register the user. Error: " . $stmt->error));
            }
        }
    } else {
        die(json_encode(array("message" => "Error preparing select query: " . $conn->error)));
    }
} else {
    echo json_encode(array("message" => "Incomplete data."));
}
?>
