<?php
// Validar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Consultar el usuario en la base de datos
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña (asumiendo que está encriptada)
        if ($password === $user['password']) { // Si usas password_hash, usa password_verify aquí
            // Guardar datos en la sesión
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time(); // Guarda el tiempo actual
            
            // Redirigir a la página principal
            header("Location: ./index.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
    $stmt->close();
    $conn->close();
}

?>