<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Asegúrate de que estas rutas sean correctas según la estructura de tu proyecto.
require_once __DIR__ . "/../../assets/classes/User.php"; // Ajusta el número de ../ según la ubicación de tus archivos
require_once __DIR__ . "/../../controllers/conection.php";

// Variable para mensajes de error
$error_message = '';

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Recoge el valor del campo 'username'
    $password = trim($_POST['password']); // Recoge el valor del campo 'password'
    $confirm_password = trim($_POST['confirm_password']); // Recoge el valor del campo 'confirm_password'

    // Validación básica
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "El nombre de usuario y la contraseña son requeridos.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $error_message = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } elseif (strlen($password) < 8) {
        $error_message = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Las contraseñas no coinciden.";
    }

    // Si hubo algún error, mostramos el mensaje de error
    if ($error_message) {
        echo $error_message;
        exit; // Detenemos la ejecución si hay error
    }

    // Crear un objeto de la clase User
    $user = new User($username, $password);

    // Obtener los valores del objeto User (sin cifrar la contraseña aún)
    $usernameValue = $user->getUsername();
    $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT); // Hacer hash de la contraseña

    // Verificar si el nombre de usuario ya existe
    $sql_check = "SELECT user_id FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param("s", $usernameValue);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Si el nombre de usuario ya está registrado, mostrar un error
            $error_message = "El nombre de usuario ya está en uso.";
            $stmt_check->close();
        } else {
            $stmt_check->close();

            // Crear la consulta SQL para insertar el nuevo usuario
            $sql_insert = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            
            if ($stmt_insert) {
                // Insertar el nuevo usuario en la base de datos de forma segura
                $stmt_insert->bind_param("ss", $usernameValue, $hashedPassword);
                if ($stmt_insert->execute()) {
                    header("Location: /build/functions/login.php?registration_success=true");
                    exit();
                } else {
                    $error_message = "Error al registrar el usuario: " . $conn->error;
                }

                $stmt_insert->close();
            } else {
                $error_message = "Error en la consulta de inserción: " . $conn->error;
            }
        }
    } else {
        $error_message = "Error en la consulta de verificación: " . $conn->error;
    }

    // Mostrar el mensaje de error si hay alguno
    if ($error_message) {
        echo $error_message;
        exit; // Detenemos la ejecución si hay error
    }

    // Cerrar la conexión
    $conn->close();
}
?>
