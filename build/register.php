<?php
require_once "../assets/classes/User.php";
require_once "../controllers/conection.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Recoge el valor del campo 'username'
    $password = trim($_POST['password']); // Recoge el valor del campo 'password'
    $email = trim($_POST['email']); // Recoge el valor del campo 'email'

    // Validación básica
    if (empty($username) || empty($password) || empty($email)) {
        die("Username, password, and email are required.");
    }

    // Genera un token de verificación único
    function generarTokenVerificacion() {
        return bin2hex(random_bytes(16)); // Genera un token de 32 caracteres
    }
    $token = generarTokenVerificacion();

    // Crear un objeto de la clase User
    $user = new User($username, $password);

    // Obtener los valores del objeto User
    $usernameValue = $user->getUsername();
    $passwordValue = password_hash($user->getPassword(), PASSWORD_BCRYPT); // Asegura la contraseña
    $tokenVerificacion = $token;

    // Crear la consulta SQL para insertar el usuario y el token de verificación
    $sql = "INSERT INTO users (username, password, token_verificacion, verificado) VALUES ('$usernameValue', '$passwordValue', '$tokenVerificacion', FALSE)";

    // Ejecutar la consulta y verificar si se ejecutó correctamente
    if ($conn->query($sql) === TRUE) {
        // Enviar el correo de verificación
        $mail = new PHPMailer(true);
        try {
            $mail->setFrom('tu-correo@rayitodesol.es', 'Rayitodesol');
            $mail->addAddress($email);
            $mail->Subject = 'Verificación de tu cuenta';
            $mail->Body = 'Haz clic en el siguiente enlace para verificar tu cuenta: 
            http://rayitodesol.es/verificar.php?token=' . $token;

            $mail->send();
            echo 'Correo de verificación enviado. Por favor, revisa tu bandeja de entrada.';
        } catch (Exception $e) {
            echo 'Error al enviar el correo: ', $mail->ErrorInfo;
        }

        // Redirigir al usuario a la página de login
        header("Location: login.php");
    } else {
        echo "Error registrando el usuario: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
