<?php

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    exit("Debes iniciar sesión para realizar esta acción.");
}

// Incluir archivo de conexión a la base de datos
include(__DIR__ . '/../../../controllers/conection.php');

// Procesar el formulario si es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
    $user_id = $_SESSION['user_id'];
    $new_password = $_POST['password'];

    // Cifrar la nueva contraseña
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param('si', $hashed_password, $user_id);
        if ($stmt->execute()) {
            header("Location: /build/functions/information.php?password_updated=true");
            exit();
        } else {
            echo "Error al actualizar la contraseña: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>
