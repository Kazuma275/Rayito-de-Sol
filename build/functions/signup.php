<?php
session_start();

// Generar un token de sesión único
$_SESSION["token"] = md5(time());

// Configuración de idioma predeterminado
$default_lang = 'es';
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $default_lang;

// Guardar idioma en la sesión si se especifica en la URL
$_SESSION['lang'] = $lang;

// Ruta del archivo de idioma y verificación de existencia
$lang_file = __DIR__ . "/lang/{$lang}.php";
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

// Variables para mostrar mensajes de error
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validación del nombre de usuario y la contraseña
    if (empty($username) || empty($password)) {
        $error_message = $lang['error_empty_fields']; // Mensaje de error: "Todos los campos son obligatorios."
    } elseif (strlen($username) < 3) {
        $error_message = $lang['error_username_short']; // Mensaje de error: "El nombre de usuario debe tener al menos 3 caracteres."
    } elseif (strlen($password) < 6) {
        $error_message = $lang['error_password_short']; // Mensaje de error: "La contraseña debe tener al menos 6 caracteres."
    }

    // Si no hay errores, redirigir al registro
    if (empty($error_message)) {
        // Redirigir al archivo register.php para realizar la inserción
        header("Location: register.php?username=" . urlencode($username) . "&password=" . urlencode($password));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($_SESSION['lang']); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lang['title']); ?></title>
    <!-- CSS y JS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/darkmode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="/js/eye.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="/index.php#parallax-section" class="active">Home</a>
                <a href="/index.php#amenities"><?php echo htmlspecialchars($lang['amenities']); ?></a>
                <a href="/index.php#gallery"><?php echo htmlspecialchars($lang['gallery']); ?></a>
                <a href="/index.php#reviews"><?php echo htmlspecialchars($lang['reviews']); ?></a>
                <a href="/index.php#ubication"><?php echo htmlspecialchars($lang['ubication']); ?></a>
                <a href="#"><?php echo htmlspecialchars($lang['account']); ?></a>
            </div>
        </nav>

        <!-- Sección de registro -->
        <section id="signup">
            <h2><?php echo htmlspecialchars($lang['signup']); ?></h2>
            <p><?php echo htmlspecialchars($lang['signup_message']); ?></p>

            <!-- Mensaje de error si hay alguna validación fallida -->
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="signup-form">
                <label for="username"><?php echo htmlspecialchars($lang['username']); ?>:</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">

                <label for="password"><?php echo htmlspecialchars($lang['password']); ?>:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <i id="toggle-password" class="fa fa-eye"></i>
                </div>

                <input type="submit" value="<?php echo htmlspecialchars($lang['register']); ?>">
            </form>
            <div class="links">
                <a href="./login.php"><?php echo htmlspecialchars($lang['login_account']); ?></a>
                <a href="/build/crud/dump/delete.php"><?php echo htmlspecialchars($lang['delete_account']); ?></a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-links">
                    <a href="/index.php#parallax-section" class="active"><?php echo htmlspecialchars($lang['home']); ?></a>
                    <a href="/index.php#amenities"><?php echo htmlspecialchars($lang['amenities']); ?></a>
                    <a href="/index.php#gallery"><?php echo htmlspecialchars($lang['gallery']); ?></a>
                    <a href="/index.php#reviews"><?php echo htmlspecialchars($lang['reviews']); ?></a>
                    <a href="#"><?php echo htmlspecialchars($lang['account']); ?></a>
                </div>
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                <div class="copyright">
                    &copy; 2024 <a href="https://rayitodesol.es">Rayito de Sol</a>. <?php echo htmlspecialchars($lang['rights']); ?>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
