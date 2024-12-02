<?php

/* DEPURACIÓN */
/* ini_set('display_errors', 1);
error_reporting(E_ALL); */

session_start();

$session_lifetime = 1800; // Tiempo de vida de la sesión en segundos


// Verifica si la sesión tiene un tiempo de expiración configurado
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
    // La sesión ha expirado
    session_unset();     // Borra todas las variables de sesión
    session_destroy();   // Destruye la sesión
    header("Location: ../index.php?session_expired=true"); // Redirige a una página o muestra un mensaje
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Actualiza el tiempo de la última actividad


require_once "../controllers/conection.php";

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

                // Verifica la contraseña
                if (password_verify($password, $user['password'])) {
                    // Credenciales válidas, establece variables de sesión
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['logged_in'] = true;

                    // Redirige al index
                    header("Location: ../index.php");

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
$lang_file = __DIR__ . "/lang/{$lang}.php";

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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <!-- Favicon -->
    <link rel="icon" href="../img/favicon.png" type="image/x-icon">

    <!-- JS -->
    <script defer src="../js/javascript.js"></script>
    <script defer src="../js/darkmode.js"></script>
    <script defer src="../js/languague.js"></script>
    <script defer src="../js/eye.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
<!-- Navbar -->
<nav>
            <div class="topnav" id="myTopnav">
                <a href="#parallax-section" class="active">Home</a>
                <a href="#amenities"><?php echo $lang['amenities']?></a>
                <a href="#gallery"><?php echo $lang['gallery']?></a>
                <a href="#reviews"><?php echo $lang['reviews']?></a>
                <a href="#ubication"><?php echo $lang['ubication']; ?></a>
                <a href="/build/signup.php"><?php echo $lang['account']?></a>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                <!-- Mostrar el enlace de reservas solo si la sesión está activa -->
                    <a href="/build/create.php"><?php echo $lang['make_reservation']?></a>
                <?php endif; ?>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                    <a href="/build/information.php" class="login-message"><?php echo "Hey," . $_SESSION['username']?></a>
                <?php endif; ?>

                <!-- Contenedor para la bandera y el modo oscuro -->
                <div class="settings-container" style="position: relative;">
                    <!-- Selector de idioma -->
                    <div class="language-selector">
                        <img id="current-flag" src="../img/idiomas/<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es'; ?>.png" alt="<?php echo $lang['current_lang'] ?? 'Español'; ?>" class="flag">
                        <ul class="language-menu">
                            <li><a href="?lang=en" data-lang="en"><img src="../img/idiomas/en.png" alt="English" class="flag-preview"></a></li>
                            <li><a href="?lang=fr" data-lang="fr"><img src="../img/idiomas/fr.png" alt="Français" class="flag-preview"></a></li>
                            <li><a href="?lang=es" data-lang="es"><img src="../img/idiomas/es.png" alt="Español" class="flag-preview"></a></li>
                            <li><a href="?lang=cn" data-lang="cn"><img src="../img/idiomas/cn.png" alt="中国人" class="flag-preview"></a></li>
                            <li><a href="?lang=it" data-lang="it"><img src="../img/idiomas/it.png" alt="Italiano" class="flag-preview"></a></li>
                            <li><a href="?lang=br" data-lang="br"><img src="../img/idiomas/br.png" alt="Brasileiro" class="flag-preview"></a></li>
                            <li><a href="?lang=ua" data-lang="ua"><img src="../img/idiomas/ua.png" alt="українська" class="flag-preview"></a></li>
                            <li><a href="?lang=ru" data-lang="ru"><img src="../img/idiomas/ru.png" alt="Русский" class="flag-preview"></a></li>
                        </ul>
                    </div>

                    <!-- Botón de Modo Oscuro/Claro -->
                    <button id="toggle-dark-mode" class="dark-mode-toggle">
                        <i class="fa fa-moon"></i>
                    </button>
                </div>
			</div>
        </nav>
        
        <!-- Sección "Sign Up" -->
        <section id="signup">
            <h2><?php echo $lang['login']; ?></h2>
            <p><?php echo $lang['login_message']; ?></p>
            <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        <form action="login.php" method="POST" class="login-form">
            <!-- Usuario -->
            <label for="username"><?php echo $lang['login_username']; ?></label>
            <input type="text" id="username" name="username" required>

            <!-- Contraseña -->
            <label for="password"><?php echo $lang['login_password']; ?></label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i id="toggle-password" class="fa fa-eye"></i>
            </div>

            <input type="submit" value="Iniciar Sesión">
        </form>
        
        <div class="links">
            <?php echo $lang['register_prompt']; ?> <a href="signup.php"><?php echo $lang['register_link']; ?></a>
        </div>

        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <!-- Sección de enlaces -->
                <div class="footer-links">
                    <a href="#" class="active"><?php echo $lang['home']; ?></a>
                    <a href="../index.php#amenities"><?php echo $lang['amenities']; ?></a>
                    <a href="../index.php#contact"><?php echo $lang['contact']; ?></a>
                    <a href="../index.php#reviews"><?php echo $lang['reviews']; ?></a>
                    <a href="../index.php#reservation"><?php echo $lang['booking']; ?></a>
                    <a href="../index.php#gallery"><?php echo $lang['gallery']; ?></a>
                    <a href="#"><?php echo $lang['account']; ?></a>
                </div>

                <!-- Redes Sociales -->
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>

                <!-- Derechos de autor -->
                <div class="copyright">
                    &copy; 2024 <a href="https://rayitodesol.es">Rayito de Sol</a>. <?php echo $lang['rights']; ?>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
