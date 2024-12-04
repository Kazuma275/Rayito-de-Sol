<?php
require_once "../controllers/conection.php";

// Verifica si el token se pasó por la URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica el token en la base de datos
    $query = "SELECT * FROM users WHERE token_verificacion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // Marca la cuenta como verificada y elimina el token de verificación
        $updateQuery = "UPDATE users SET verificado = 1, token_verificacion = NULL WHERE token_verificacion = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo 'Cuenta verificada exitosamente. Puedes iniciar sesión ahora.';
    } else {
        echo 'Token inválido o expirado';
    }

    // Cerrar la conexión
    $conn->close();
}
?>
