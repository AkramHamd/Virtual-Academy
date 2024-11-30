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
        if (!empty($data->module_id) && !empty($data->course_id)) {
            $module_id = (int) $data->module_id;
            $course_id = (int) $data->course_id;

            // Validar si el módulo existe en la base de datos
            $module_check_query = "SELECT id FROM modules WHERE id = ? AND course_id = ?";
            $module_check_stmt = $conn->prepare($module_check_query);
            $module_check_stmt->bind_param("ii", $module_id, $course_id);
            $module_check_stmt->execute();
            $module_check_stmt->store_result();

            if ($module_check_stmt->num_rows == 0) {
                // El módulo no existe
                echo json_encode(["message" => "Module not found or does not belong to the specified course."]);
                exit;
            }

            // Preparar la consulta para eliminar el módulo
            $delete_query = "DELETE FROM modules WHERE id = ? AND course_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("ii", $module_id, $course_id);

            // Ejecutar la consulta
            if ($delete_stmt->execute()) {
                echo json_encode([
                    "message" => "Module deleted successfully."
                ]);
            } else {
                throw new Exception("Error deleting module: " . $delete_stmt->error);
            }

            // Cerrar la consulta
            $delete_stmt->close();
            $module_check_stmt->close();

        } else {
            // Si falta algún dato en la solicitud
            echo json_encode(["message" => "Incomplete input data. Please provide module_id and course_id."]);
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
