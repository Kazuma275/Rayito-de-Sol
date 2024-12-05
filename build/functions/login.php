<?php

session_start();

$session_lifetime = 360; // Tiempo de vida de la sesión en segundos

// Verifica si la sesión tiene un tiempo de expiración configurado
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
    session_unset();     // Borra todas las variables de sesión
    session_destroy();   // Destruye la sesión
    header("Location: /index.php?session_expired=true"); // Redirige a la página index.php
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Actualiza el tiempo de la última actividad

require_once __DIR__ . "/../../controllers/conection.php";

// Manejo de autenticación
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

                // Verifica la contraseña con password_verify
                if (password_verify($password, $user['password'])) {
                    // Credenciales válidas, establece variables de sesión
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['logged_in'] = true;

                    // Asigna el rol basado en el nombre de usuario desde la base de datos
                    // Aquí puedes usar un campo adicional en la tabla 'users' para roles si es necesario.
                    $_SESSION['role'] = 'user'; // Por defecto es un usuario normal, puedes personalizar esto según tus necesidades

                    // Redirige al index
                    header("Location: /index.php");
                    exit();
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "Usuario no encontrado.";
            }
            $stmt->close();
        } else {
            $error = "Error en la consulta: " . $conn->error;
        }
    } else {
        $error = "Por favor, rellena todos los campos.";
    }
}

// Configuración de idioma
$default_lang = 'es';
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang; // Guarda en la sesión
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = $default_lang; // Usa idioma predeterminado
}

// Limpia el idioma para evitar caracteres no válidos
$lang = preg_replace('/[^a-z]/', '', $lang);

// Ruta del archivo de idioma
$lang_file = $_SERVER['DOCUMENT_ROOT'] . "/lang/{$lang}.php";

// Verifica si el archivo de idioma existe
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado en: $lang_file");
}

$conn->close(); // Cierra la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/darkmode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <!-- Favicon -->
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">

    <!-- JS -->
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="/js/eye.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="/index.php#parallax-section" class="active">Home</a>
                <a href="/index.php#amenities"><?php echo $lang['amenities']?></a>
                <a href="/index.php#gallery"><?php echo $lang['gallery']?></a>
                <a href="/index.php#reviews"><?php echo $lang['reviews']?></a>
                <a href="/index.php#ubication"><?php echo $lang['ubication']; ?></a>
                <a href="/build/functions/signup.php"><?php echo $lang['account']?></a>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                    <a href="/build/crud/data/create.php"><?php echo $lang['make_reservation']?></a>
                    <a href="/build/functions/information.php" class="login-message"><?php echo "Hey, " . htmlspecialchars($_SESSION['username']); ?></a>
                    <a href="/build/functions/contact.php"><?php echo $lang['contact_title']?></a>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Sección login -->
        <section id="login">
            <h2><?php echo $lang['login']; ?></h2>
            <p><?php echo $lang['login_message']; ?></p>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <!-- Formulario login -->
            <form action="/build/functions/login.php" method="POST" class="login-form">
                <label for="username"><?php echo $lang['login_username']; ?></label>
                <input type="text" id="username" name="username" required>

                <label for="password"><?php echo $lang['login_password']; ?></label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <i id="toggle-password" class="fa fa-eye"></i>
                </div>

                <input type="submit" value="Iniciar Sesión">
            </form>
            
            <div class="links">
                <?php echo $lang['register_prompt']; ?> <a href="/build/functions/signup.php"><?php echo $lang['register_link']; ?></a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-links">
                    <a href="/index.php#parallax-section" class="active"><?php echo $lang['home']; ?></a>
                    <a href="/index.php#amenities"><?php echo $lang['amenities']; ?></a>
                    <a href="/index.php#gallery"><?php echo $lang['gallery']; ?></a>
                    <a href="/index.php#reviews"><?php echo $lang['reviews']; ?></a>
                    <a href="/build/functions/signup.php"><?php echo $lang['account']; ?></a>
                </div>

                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
