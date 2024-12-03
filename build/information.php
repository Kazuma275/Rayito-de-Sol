<?php

session_start();

$_SESSION["token"] = md5(time());

// Define un idioma predeterminado
$default_lang = 'es';

// Obtén el idioma de la URL o de la sesión
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang; // Guarda el idioma seleccionado en la sesión
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang']; // Usa el idioma almacenado en la sesión
} else {
    $lang = $default_lang; // Usa el idioma predeterminado
}

// Ruta del archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";

// Verifica si el archivo existe
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirigir al login si no está autenticado
    exit();
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    require_once './controllers/conection.php'; // Asegúrate de que exista este archivo para la conexión a la BD

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que los campos no estén vacíos
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Todos los campos son obligatorios.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "La nueva contraseña y la confirmación no coinciden.";
    } else {
        // Obtener el hash de la contraseña actual
        $username = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // Validar la contraseña actual
        if (!password_verify($current_password, $hashed_password)) {
            $error_message = "La contraseña actual es incorrecta.";
        } else {
            // Hashear la nueva contraseña
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $new_hashed_password, $username);

            if ($stmt->execute()) {
                $success_message = "Contraseña actualizada correctamente.";
            } else {
                $error_message = "Error al actualizar la contraseña. Por favor, intenta nuevamente.";
            }

            $stmt->close();
            $conn->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['manage_account']; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <!-- Favicon -->
    <link rel="icon" href="../img/favicon.png" type="image/x-icon">

    <!-- JS -->
    <script defer src="../js/javascript.js"></script>
    <script defer src="../js/darkmode.js"></script>
    <script defer src="../js/languague.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="../index.php#parallax-section"><?php echo $lang['home']; ?></a>
                <a href="../index.php#amenities"><?php echo $lang['amenities']; ?></a>
                <a href="../index.php#gallery"><?php echo $lang['gallery']; ?></a>
                <a href="../index.php#reviews"><?php echo $lang['reviews']; ?></a>
                <a href="#"><?php echo $lang['account']; ?></a>
            </div>
        </nav>
        
            <!-- Sección de gestión de cuenta -->
            <section id="manage-account">
            <h2>Gestión de Cuenta</h2>
            <p>Administra la información de tu cuenta y ajusta tus preferencias aquí.</p>

            <!-- Formulario para actualizar datos -->
            <form action="update_user.php" method="POST" class="manage-form">
                <label for="username">Actualizar Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?php echo $_SESSION['username']; ?>" required>
                
                <label for="email">Actualizar Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" required>

                <input type="submit" value="Guardar Cambios">
            </form>

            <!-- Formulario para cambiar contraseña -->
            <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
            <?php if (isset($success_message)) echo "<p class='success'>$success_message</p>"; ?>
            <form method="POST" class="password-form">
                <input type="hidden" name="change_password" value="1">
                <label for="current_password">Contraseña Actual:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">Nueva Contraseña:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <input type="submit" value="Actualizar Contraseña">
            </form>

            <!-- Enlace para cerrar sesión -->
            <a href="logout.php" class="logout-button">Cerrar Sesión</a>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-links">
                    <a href="../index.php#parallax-section"><?php echo $lang['home']; ?></a>
                    <a href="../index.php#amenities"><?php echo $lang['amenities']; ?></a>
                    <a href="../index.php#reviews"><?php echo $lang['reviews']; ?></a>
                    <a href="../index.php#gallery"><?php echo $lang['gallery']; ?></a>
                    <a href="#"><?php echo $lang['account']; ?></a>
                </div>
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                <div class="copyright">
                    &copy; 2024 <a href="https://rayitodesol.es">Rayito de Sol</a>. <?php echo $lang['rights']; ?>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
