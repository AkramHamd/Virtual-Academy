<?php
// Database credentials
$servername = "localhost";  // Para Windows usa esta
$username = "root";         // Default username in XAMPP is 'root'
$password = "";             // Default password is empty
$dbname = "virtual_academy"; // Cambia la base de datos si le pusiste otro nombre

// Create a connection using MySQLi with error handling
try {
    // Create a new MySQLi connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        // Throw an exception if connection fails
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    echo "Connected successfully to the database";
} catch (Exception $e) {
    // Log the error message (for production environments, logging should be used instead of displaying errors)
    error_log($e->getMessage());

    // Display a generic error message without exposing sensitive details
    echo "An error occurred while connecting to the database. Please try again later.";
} 
// finally {
//     // Optionally, close the connection if it's open (not mandatory here but useful in long scripts)
//     if (isset($conn) && $conn->ping()) {
//         $conn->close();
//     }
// }
?>
