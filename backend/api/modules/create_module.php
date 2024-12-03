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
        if (!empty($data->course_id) && !empty($data->title) && !empty($data->video_url) && !empty($data->support_material_url)) {
            $course_id = (int) $data->course_id;
            $title = $data->title;
            $video_url = $data->video_url;
            $support_material_url = $data->support_material_url;

            // Validar si el curso existe
            $course_check_query = "SELECT id FROM courses WHERE id = ?";
            $course_check_stmt = $conn->prepare($course_check_query);
            $course_check_stmt->bind_param("i", $course_id);
            $course_check_stmt->execute();
            $course_check_stmt->store_result();

            if ($course_check_stmt->num_rows == 0) {
                // El curso no existe
                echo json_encode(["message" => "Course not found."]);
                exit;
            }

            // Validar las URLs
            if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
                echo json_encode(["message" => "Invalid video URL."]);
                exit;
            }

            if (!filter_var($support_material_url, FILTER_VALIDATE_URL)) {
                echo json_encode(["message" => "Invalid support material URL."]);
                exit;
            }

            // Preparar la consulta para insertar el nuevo módulo
            $insert_query = "INSERT INTO modules (course_id, title, video_url, support_material_url) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("isss", $course_id, $title, $video_url, $support_material_url);

            // Ejecutar la consulta
            if ($insert_stmt->execute()) {
                echo json_encode([
                    "message" => "Module created successfully.",
                    "module_id" => $insert_stmt->insert_id // Retornar el ID del nuevo módulo
                ]);
            } else {
                throw new Exception("Error creating module: " . $insert_stmt->error);
            }

            // Cerrar la consulta
            $insert_stmt->close();
            $course_check_stmt->close();

        } else {
            // Si falta algún dato en la solicitud
            echo json_encode(["message" => "Incomplete input data. Please provide course_id, title, video_url, and support_material_url."]);
        }

    } else {
        // Si el usuario no está logueado o no tiene el rol de admin
        echo json_encode(["message" => "Access denied. Admin privileges required."]);
    }

} catch (Exception $e) {
    // Capturar cualquier error inesperado y devolver el mensaje de error
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
} finally {
    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>
