<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require '../../config/db_connection.php';

session_start();

if ($conn->connect_errno) {
    die(json_encode(array("message" => "Database connection error: " . $conn->connect_error)));
}

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['name']) && !empty($data['email']) && !empty($data['password'])) {
    $name = htmlspecialchars(strip_tags($data['name']));
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $password = $data['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $query = "SELECT * FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode(array("message" => "User already exists."));
        } else {
            $insert_query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')";
            $insert_stmt = $conn->prepare($insert_query);
            if (!$insert_stmt) {
                die(json_encode(array("message" => "Error preparing statement: " . $conn->error)));
            }
            
            $insert_stmt->bind_param('sss', $name, $email, $password_hash);
            if ($insert_stmt->execute()) {
                if ($insert_stmt->affected_rows > 0) {
                    echo json_encode(array("message" => "User registered successfully."));
                } else {
                    echo json_encode(array("message" => "Registration failed: No rows affected."));
                }
            } else {
                echo json_encode(array("message" => "Execution failed: " . $insert_stmt->error));
            }
        }
    } else {
        die(json_encode(array("message" => "Error preparing select query: " . $conn->error)));
    }
} else {
    echo json_encode(array("message" => "Incomplete data."));
}
?>
