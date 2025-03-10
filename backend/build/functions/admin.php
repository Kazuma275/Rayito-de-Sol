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

// Obtener la lista de usuarios
$sql = "SELECT user_id, username, role FROM users";
$result = $conn->query($sql);

// Verifica si la consulta se realizó correctamente
if (!$result) {
    die("Error al obtener los usuarios: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Gestión de Usuarios</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['user_id']; ?>">Editar</a>
                        <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
