<?php
// Iniciar la sesión al principio de la página
session_start();

// Suponiendo que $conn es tu objeto de conexión a la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        // Consulta para verificar las credenciales
        $sql = "SELECT user_id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verifica la contraseña
                if (password_verify($password, $user['password'])) {
                    // Credenciales válidas, establece variables de sesión
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['logged_in'] = true;

                    // Verifica si el usuario es 'sergio' y asigna el rol de admin
                    if (($username === 'sergio' && $password === '1234') || ($username === 'alvaro' && $password === '1234')) {
                        $_SESSION['role'] = 'admin';
                    } else {
                        // Asigna el rol de usuario normal si no es 'sergio'
                        $_SESSION['role'] = 'user';
                    }

                    // Redirige al index
                    header("Location: /index.php");
                    exit();
                } else {
                    echo "Contraseña incorrecta.";
                }
            } else {
                echo "Usuario no encontrado.";
            }
            $stmt->close();
        } else {
            echo "Error en la consulta: " . $conn->error;
        }
    } else {
        echo "Por favor, rellena todos los campos.";
    }
}
?>
