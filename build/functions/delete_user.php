<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica que el usuario esté autenticado y sea administrador
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/conection.php";

// Verifica que la conexión sea exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si se ha pasado un ID de usuario en la URL
if (isset($_GET['id'])) {
    $user_id = (int) $_GET['id'];

    // Elimina el usuario de la base de datos
    $delete_sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "Usuario eliminado con éxito.";
    } else {
        echo "Error al eliminar el usuario: " . $conn->error;
    }
    $stmt->close();
} else {
    die("ID de usuario no especificado.");
}

$conn->close();
?>
