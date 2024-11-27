<?php
require_once "../assets/classes/User.php"; // Verifica que la ruta y el nombre de la clase sean correctos
require_once "../controllers/connection.php"; // Verifica que la ruta sea correcta

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['name']); // Usamos 'name' porque es el nombre del campo en el formulario
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Create a User object
    $user = new User($username, $password);

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $user->getUsername(), $user->getPassword());

    // Execute the query
    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error registering user: " . $stmt->error;
    }

    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
    
}
?>
