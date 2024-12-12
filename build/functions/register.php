<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Asegúrate de que estas rutas sean correctas según la estructura de tu proyecto.
require_once __DIR__ . "/../../assets/classes/User.php"; // Ajusta el número de ../ según la ubicación de tus archivos
require_once __DIR__ . "/../../controllers/conection.php";

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');  // Confirmación de la contraseña
    $error_message = '';

    // Validación básica de los campos
    if ($username && $password && $confirm_password) {
        // Validación del nombre de usuario (solo letras, números y guiones bajos)
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            $error_message = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
        }
        // Validación de la longitud de la contraseña
        elseif (strlen($password) < 8) {
            $error_message = "La contraseña debe tener al menos 8 caracteres.";
        }
        // Verificar que las contraseñas coincidan
        elseif ($password !== $confirm_password) {
            $error_message = "Las contraseñas no coinciden.";
        } else {
            // Verificar si el nombre de usuario ya está en uso
            $sql = "SELECT user_id FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                // Si el nombre de usuario ya existe, mostrar un error
                if ($result->num_rows > 0) {
                    $error_message = "El nombre de usuario ya está en uso.";
                } else {
                    // Si el nombre de usuario es único, registrarlo
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash de la contraseña

                    // Insertar el nuevo usuario en la base de datos
                    $sql_insert = "INSERT INTO users (username, password) VALUES (?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    if ($stmt_insert) {
                        $stmt_insert->bind_param("ss", $username, $hashed_password);
                        $stmt_insert->execute();

                        // Verificar si la inserción fue exitosa
                        if ($stmt_insert->affected_rows === 1) {
                            // Regenerar ID de sesión para evitar ataques de fijación de sesión
                            session_regenerate_id(true);

                            $_SESSION['username'] = $username;
                            $_SESSION['logged_in'] = true;
                            $_SESSION['role'] = 'user';  // Rol por defecto es 'user'

                            header("Location: /index.php");
                            exit();
                        } else {
                            $error_message = "Error al registrar el usuario.";
                        }
                    } else {
                        $error_message = "Error en la consulta de registro: " . $conn->error;
                    }
                }
                $stmt->close();
            } else {
                $error_message = "Error en la consulta: " . $conn->error;
            }
        }
    } else {
        $error_message = "Por favor, rellena todos los campos.";
    }
}
?>
