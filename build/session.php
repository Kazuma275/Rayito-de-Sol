<?php
session_start();
require_once "../controllers/conection.php";

// Verifica si el formulario de inicio de sesión ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validación básica
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Consultar el usuario en la base de datos
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña (asegurándose de que esté encriptada)
        if ($password === $user['password']) { // Cambia esto a `password_verify($password, $user['password'])` si usas `password_hash()`
            // Guarda la información de la sesión
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;

            // Redirige al usuario a la página principal o a la página protegida
            header("Location: ../../index.php");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>