<?php
session_start();

// Verifica que el usuario esté autenticado y sea administrador
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/conection.php";

// Verifica si se ha enviado un ID de usuario para editar
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $sql = "SELECT user_id, username, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        die("Usuario no encontrado.");
    }
} else {
    die("ID de usuario no proporcionado.");
}

// Procesa la actualización de los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (!empty($username) && !empty($email)) {
        $update_sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $username, $email, $user_id);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Error al actualizar el usuario: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Por favor, rellena todos los campos.";
    }
}

$conn->close();
?>
