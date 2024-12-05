<?php
session_start();

// Verifica que el usuario esté autenticado y sea administrador
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/conection.php";

// Verifica si se ha enviado un ID de usuario para eliminar
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $delete_sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error al eliminar el usuario: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "ID de usuario no proporcionado.";
}

$conn->close();
?>
