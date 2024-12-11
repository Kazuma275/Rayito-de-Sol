<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../assets/classes/User.php"; // Ajusta la ruta
require_once __DIR__ . "/../../controllers/conection.php";

// Validación del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validación básica
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Validación de nombre de usuario (mínimo 3 caracteres)
    if (strlen($username) < 3) {
        die("Username must be at least 3 characters long.");
    }

    // Validación de la contraseña (mínimo 6 caracteres)
    if (strlen($password) < 6) {
        die("Password must be at least 6 characters long.");
    }

    // Comprobación si el nombre de usuario ya existe
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Parámetro de tipo string ('s')
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Username is already taken.");
    }

    // Crear un objeto User
    $user = new User($username, $password);

    // Obtener el nombre de usuario y la contraseña del objeto User
    $usernameValue = $user->getUsername();
    $passwordValue = $user->getPassword();

    // Encriptar la contraseña
    $hashedPassword = password_hash($passwordValue, PASSWORD_DEFAULT);

    // Crear la consulta SQL para insertar el nuevo usuario
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usernameValue, $hashedPassword); // Parámetros de tipo string ('s')
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        header("Location: /build/functions/login.php?registration_success=true");
        exit();
    } else {
        echo "Error registering user: " . $stmt->error;
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>
