<?php

// Asegúrate de que estas rutas sean correctas según la estructura de tu proyecto.
require_once __DIR__ . "/../../assets/classes/User.php"; // Ajusta el número de ../ según la ubicación de tus archivos
require_once __DIR__ . "/../../controllers/conection.php";

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

    // Ejecuto la consulta
    if ($conn->query($sql) === TRUE) {
        header("Location: ./index.php?registration_success=true");
        exit();
    } else {
        echo "Error registering user: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
