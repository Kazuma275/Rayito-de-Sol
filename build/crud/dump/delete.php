<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

const SESSION_LIFETIME = 1800; // Tiempo de vida de la sesión en segundos

// Verifica si la sesión ha expirado
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_LIFETIME)) {
    session_unset();
    session_destroy();
    header("Location: /index.php?session_expired=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Actualiza el tiempo de la última actividad

require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/conection.php';

// Verifica que el usuario esté autenticado
if (empty($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    header("Location: /index.php?not_logged_in=true");
    exit();
}

// Manejo de la solicitud de eliminación
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $user_id = $_SESSION['user_id'];

    if ($stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?")) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            header("Location: /index.php?account_deleted=true");
            exit();
        } else {
            $error = "Error al eliminar la cuenta: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error en la consulta: " . $conn->error;
    }
}
$conn->close();

// Gestión de idioma
$default_lang = 'es';
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $default_lang;
$_SESSION['lang'] = $lang;

$lang_file = dirname(dirname(dirname(__DIR__))) . "/lang/{$lang}.php";
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

$dark_mode = $_SESSION['dark_mode'] ?? false;
if (isset($_GET['toggle_dark_mode'])) {
    $dark_mode = !$dark_mode;
    $_SESSION['dark_mode'] = $dark_mode;
}

?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <!-- Favicon -->
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">

    <!-- JS -->
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="/js/eye.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="#parallax-section" class="active"><?php echo $lang['home']?></a>
                <a href="#amenities"><?php echo $lang['amenities']?></a>
                <a href="#gallery"><?php echo $lang['gallery']?></a>
                <a href="#reviews"><?php echo $lang['reviews']?></a>
                <a href="#ubication"><?php echo $lang['ubication']; ?></a>
                <a href="/build/functions/login.php"><?php echo $lang['account']?></a>
                <?php if (!empty($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                    <a href="/build/crud/data/create.php"><?php echo $lang['make_reservation']?></a>
                    <a href="/build/functions/information.php" class="login-message">Hey, <?php echo $_SESSION['username']?></a>
                    <a href="/build/functions/contact.php"><?php echo $lang['contact_title']?></a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/build/functions/admin.php">Admin Panel</a>
                <?php endif; ?>
                <div class="settings-container" style="position: relative;">
                    <div class="language-selector">
                        <img id="current-flag" 
                                src="/img/idiomas/<?php echo $_SESSION['lang'] ?? 'es'; ?>.png" 
                                alt="<?php echo $lang['current_lang'] ?? 'Español'; ?>" 
                                class="flag">
                        <ul class="language-menu">
                            <?php 
                            $langs = ['en', 'fr', 'es', 'cn', 'it', 'br', 'ua', 'ru'];
                            foreach ($langs as $lang_code): ?>
                                <li><a href="?lang=<?php echo $lang_code; ?>" data-lang="<?php echo $lang_code; ?>"><img src="/img/idiomas/<?php echo $lang_code; ?>.png" alt="<?php echo ucfirst($lang_code); ?>" class="flag-preview"></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <label class="dayNight">
                        <input type="checkbox" id="darkmode-toggle" <?php echo $dark_mode ? 'checked' : ''; ?>>
                        <div></div>
                    </label>
                </div>
            </div>
        </nav>
        
        <!-- Sección "Sign Up" -->
        <section id="signup">
            <h2>Eliminar Cuenta</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <p>¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.</p>
            <form method="POST" action="">
                <input type="hidden" name="confirm_delete" value="1">
                <button type="submit">Eliminar Cuenta</button>
                <a href="/index.php">Cancelar</a>
            </form>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <!-- Links -->
                <div class="footer-links">
                    <a href="#" class="active"><?php echo $lang['home']; ?></a>
                    <a href="/index.php#amenities"><?php echo $lang['amenities']; ?></a>
                    <a href="/index.php#contact"><?php echo $lang['contact']; ?></a>
                    <a href="/index.php#reviews"><?php echo $lang['reviews']; ?></a>
                    <a href="/index.php#reservation"><?php echo $lang['booking']; ?></a>
                    <a href="/index.php#gallery"><?php echo $lang['gallery']; ?></a>
                    <a href="#"><?php echo $lang['account']; ?></a>
                </div>
                <!-- Social Media -->
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
