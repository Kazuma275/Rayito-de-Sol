<?php
session_start();
require_once 'db_connection.php'; // Asegúrate de que este archivo conecte correctamente a tu base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirigir al login si no está autenticado
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados por el formulario
    $new_username = trim($_POST['username']);
    $current_password = trim($_POST['current_password']); // Contraseña actual para validar

    // Validar que los campos no estén vacíos
    if (empty($new_username) || empty($current_password)) {
        $_SESSION['update_error'] = "Todos los campos son obligatorios.";
        header("Location: information.php");
        exit();
    }

    // Obtener el username actual de la sesión
    $current_username = $_SESSION['username'];

    // Verificar la contraseña actual
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $current_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Validar la contraseña
        if (password_verify($current_password, $hashed_password)) {
            // Actualizar el nombre de usuario en la base de datos
            $update_stmt = $conn->prepare("UPDATE users SET username = ? WHERE username = ?");
            $update_stmt->bind_param("ss", $new_username, $current_username);

            if ($update_stmt->execute()) {
                // Actualizar el nombre de usuario en la sesión
                $_SESSION['username'] = $new_username;

                $_SESSION['update_success'] = "El nombre de usuario se ha actualizado correctamente.";
                header("Location: information.php");
                exit();
            } else {
                $_SESSION['update_error'] = "Error al actualizar el nombre de usuario.";
                header("Location: information.php");
                exit();
            }
        } else {
            $_SESSION['update_error'] = "La contraseña actual no es correcta.";
            header("Location: information.php");
            exit();
        }
    } else {
        $_SESSION['update_error'] = "Usuario no encontrado.";
        header("Location: information.php");
        exit();
    }
} else {
    $_SESSION['update_error'] = "Método no permitido.";
    header("Location: information.php");
    exit();
}
