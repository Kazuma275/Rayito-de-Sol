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

    // Obtiene los detalles del usuario
    $sql = "SELECT user_id, username, role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        die("Usuario no encontrado.");
    }
    $stmt->close();

    // Procesa la actualización si se envía el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $role = trim($_POST['role']);

        // Verifica que los campos no estén vacíos
        if (!empty($username) && !empty($role)) {
            // Actualiza los detalles del usuario
            $update_sql = "UPDATE users SET username = ?, role = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssi", $username, $role, $user_id);
            if ($update_stmt->execute()) {
                echo "Usuario actualizado con éxito.";
            } else {
                echo "Error al actualizar el usuario: " . $conn->error;
            }
            $update_stmt->close();
        } else {
            echo "Por favor, rellena todos los campos.";
        }
    }
} else {
    die("ID de usuario no especificado.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
</head>
<body>
    <h1>Editar Usuario</h1>
    <form action="edit_user.php?id=<?php echo $user['user_id']; ?>" method="POST">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="role">Rol:</label>
        <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" required>

        <input type="submit" value="Actualizar Usuario">
    </form>
</body>
</html>
