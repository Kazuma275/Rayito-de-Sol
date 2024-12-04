<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Asegúrate de iniciar la sesión para obtener los datos del usuario
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo "Debes iniciar sesión para realizar esta acción.";
    exit;
}

// Incluye el archivo de conexión a la base de datos
include(__DIR__ . '/../../controllers/conection.php');

// Comprueba si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén el user_id desde la sesión y la nueva contraseña desde el formulario
    $user_id = $_SESSION['user_id']; // El ID del usuario en sesión
    $new_password = $_POST['password']; // La nueva contraseña enviada por el formulario

    // Valida que la nueva contraseña no esté vacía
    if (empty($new_password)) {
        echo "La contraseña no puede estar vacía.";
        exit;
    }

    // Cifra la contraseña usando password_hash
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Prepara la consulta SQL para actualizar la contraseña
    $sql = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Asocia los parámetros a la consulta
        $stmt->bind_param('si', $hashed_password, $user_id);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            header("Location:../build/information.php?password_updated=true");
        } else {
            echo "Error al actualizar la contraseña: " . $stmt->error;
        }

        // Cierra el statement
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
}

// Cierra la conexión a la base de datos
$conn->close();
?>
