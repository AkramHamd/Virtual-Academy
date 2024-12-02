<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); // Permite solicitudes solo desde localhost:3000
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Permite el envío de credenciales como cookies
header('Content-Type: application/json');
require '../../config/db_connection.php';

session_start(); // Iniciar la sesión

header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

try {
    // Verificar si el usuario está logueado y tiene rol de admin
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents("php://input"));

        // Validar que los datos esenciales están presentes
        if (!empty($data->title) && !empty($data->description) && !empty($data->category) && !empty($data->cover_image_url)) {
            $title = $data->title;
            $description = $data->description;
            $category = $data->category;
            $cover_image_url = $data->cover_image_url; // Nuevo campo para la URL de la imagen de portada

            // Validar la URL de la imagen de portada
            if (!filter_var($cover_image_url, FILTER_VALIDATE_URL)) {
                echo json_encode(["message" => "Invalid cover image URL."]);
                exit;
            }

            // Verificar si el título del curso ya existe
            $name_check_query = "SELECT id FROM courses WHERE title = ?";
            $name_check_stmt = $conn->prepare($name_check_query);
            $name_check_stmt->bind_param('s', $title);
            $name_check_stmt->execute();
            $name_check_stmt->store_result();

            if ($name_check_stmt->num_rows > 0) {
                // El título del curso ya está en uso
                echo json_encode(array("message" => "Course title already in use."));
            } else {
                // Insertar el nuevo curso en la base de datos
                $insert_query = "INSERT INTO courses (title, description, category, cover_image_url) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);

                if (!$insert_stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }

                $insert_stmt->bind_param('ssss', $title, $description, $category, $cover_image_url);

                if ($insert_stmt->execute()) {
                    echo json_encode(array("message" => "Course created successfully."));
                } else {
                    throw new Exception("Error creating course: " . $insert_stmt->error);
                }

                // Cerrar la consulta
                $insert_stmt->close();
            }

            // Cerrar la consulta de verificación de nombre
            $name_check_stmt->close();

        } else {
            // Si falta algún dato en la solicitud
            echo json_encode(array("message" => "Incomplete input data. Please provide title, description, category, and cover_image_url."));
        }

    } else {
        // Si el usuario no está logueado o no tiene el rol de admin
        echo json_encode(array("message" => "Access denied. Admin privileges required."));
    }

} catch (Exception $e) {
    // Capturar cualquier error inesperado y devolver el mensaje de error
    echo json_encode(array("message" => "An error occurred: " . $e->getMessage()));
} finally {
    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>
