<?php
session_start();
require_once __DIR__ . "/../../controllers/conection.php";  // Ajusta la ruta de tu archivo de conexión

// Inicialización de las variables
$username = $password = $error_message = '';

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'es';
$_SESSION['lang'] = preg_replace('/[^a-z]/', '', $lang);

// Ruta del archivo de idioma
$lang_file = $_SERVER['DOCUMENT_ROOT'] . "/lang/{$lang}.php";

// Verifica si el archivo de idioma existe
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los valores del formulario y evitar valores nulos
    $username = trim($_POST['username'] ?? ''); // Nombre de usuario
    $password = trim($_POST['password'] ?? ''); // Contraseña

    // Validación básica
    if (empty($username) || empty($password)) {
        $error_message = $lang['username_password_required'];
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $error_message = $lang['username_invalid'];
    } elseif (strlen($username) < 4 && strlen($password) < 4) {
        $error_message = $lang['username_password_min_length'];
    } elseif (strlen($password) < 4) {
        $error_message = $lang['password_min_length'];
    } elseif (strlen($username) < 4) {
        $error_message = $lang['username_min_length'];
    }

    // Si hay un error, redirigir de vuelta a signup.php con el mensaje
    if ($error_message) {
        $_SESSION['error_message'] = $error_message;
        $_SESSION['username_value'] = $username;
        header("Location: signup.php");
        exit();
    }

    // Si no hay errores, proceder con la inserción de usuario
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hashear la contraseña

    // Verificar si el nombre de usuario ya existe
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "El nombre de usuario ya está en uso.";
        header("Location: signup.php");
        exit();
    }

     // Determinar el rol del usuario
    $role = ($username === 'sergio' || $username === 'alvaro') ? 'admin' : 'user';

    // Crear la consulta SQL para insertar el nuevo usuario
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        // Iniciar sesión automáticamente
        $_SESSION['user_id'] = $conn->insert_id; // ID del nuevo usuario
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role; /* Asigno el rol de la sesión a la base de datos */
        /* El inicio de sesión se hace automático al registrarse */
        $_SESSION['logged_in'] = true;

        // Redirigir a una página de inicio u otra ruta
        header("Location: /index.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error al registrar el usuario: " . $conn->error;
        header("Location: signup.php");
        exit();
    }
}

$conn->close();

?>
