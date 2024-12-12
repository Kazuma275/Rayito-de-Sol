<?php
session_start();
require_once __DIR__ . "/../../controllers/conection.php";  // Ajusta la ruta de tu archivo de conexión

// Inicialización de las variables
$username = $password = $confirm_password = $error_message = '';

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los valores del formulario y evitar valores nulos
    $username = trim($_POST['username'] ?? ''); // Nombre de usuario
    $password = trim($_POST['password'] ?? ''); // Contraseña
    $confirm_password = trim($_POST['confirm_password'] ?? ''); // Confirmación de contraseña

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

    // Si hay un error, redirigir de vuelta a signup.php con el mensaje
    if ($error_message) {
        $_SESSION['error_message'] = $error_message;
        $_SESSION['username_value'] = $username;
        header("Location: signup.php");
        exit();
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
            $_SESSION['error_message'] = "El nombre de usuario ya está en uso.";
            $_SESSION['username_value'] = $username;
            $stmt_check->close();
            header("Location: signup.php");
            exit();
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
                    $_SESSION['error_message'] = "Error al registrar el usuario: " . $conn->error;
                }

                $stmt_insert->close();
            } else {
                $_SESSION['error_message'] = "Error en la consulta de inserción: " . $conn->error;
            }
        }
    } else {
        $_SESSION['error_message'] = "Error en la consulta de verificación: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
