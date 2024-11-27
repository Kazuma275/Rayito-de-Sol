<?php
require_once "../assets/classes/User.php"; // Verifica que la ruta y el nombre de la clase sean correctos
require_once "../controllers/connection.php"; // Verifica que la ruta sea correcta

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Recoge el valor del campo 'username'
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Create a User object
    $user = new User($username, $password);

    // Asignar valores de las propiedades del objeto a variables
    $usernameValue = $user->getUsername();
    $passwordValue = $user->getPassword();

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }
    
    // Bind parameters using variables
    $stmt->bind_param("ss", $usernameValue, $passwordValue);

    // Execute the query
    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error registering user: " . $stmt->error;
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>
