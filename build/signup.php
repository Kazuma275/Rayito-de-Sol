<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../assets/classes/User.php";
require_once "../controllers/connection.php";

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Recoge el valor del campo 'username'
    $password = trim($_POST['password']); // Recoge el valor del campo 'password'

    // Validación básica
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Crear un objeto de la clase User
    $user = new User($username, $password);

    // Obtener los valores del objeto User
    $usernameValue = $user->getUsername();
    $passwordValue = $user->getPassword();

    // Crear la consulta SQL
    $sql = "INSERT INTO users (username, password) VALUES ('$usernameValue', '$passwordValue')";

    // Ejecutar la consulta y verificar si se ejecutó correctamente
    if ($conn->query($sql) === TRUE) {
        echo "User registered successfully!";
    } else {
        echo "Error registering user: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
