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
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in']): ?>
                    <a href="/build/crud/data/create.php"><?php echo htmlspecialchars($lang['make_reservation']); ?></a>
                    <a href="/build/functions/information.php" class="login-message"><?php echo "Hey, " . htmlspecialchars($_SESSION['username']); ?></a>
                    <a href="/build/functions/contact.php"><?php echo htmlspecialchars($lang['contact_title']); ?></a>
                <?php endif; ?>

                <!-- Selector de idioma y modo oscuro -->
                <div class="settings-container">
                    <div class="language-selector">
                        <img id="current-flag" src="/img/idiomas/<?php echo htmlspecialchars($_SESSION['lang']); ?>.png" alt="<?php echo htmlspecialchars($lang['current_lang'] ?? 'Español'); ?>" class="flag">
                        <ul class="language-menu">
                            <?php foreach (['en', 'fr', 'es', 'cn', 'it', 'br', 'ua', 'ru'] as $code): ?>
                                <li><a href="?lang=<?php echo $code; ?>" data-lang="<?php echo $code; ?>"><img src="/img/idiomas/<?php echo $code; ?>.png" alt="<?php echo ucfirst($code); ?>" class="flag-preview"></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <label class="dayNight">
                        <input type="checkbox" id="darkmode-toggle">
                        <div></div>
                    </label>
                </div>
            </div>
        </nav>

        <!-- Sección de registro -->
        <section id="signup">
            <h2><?php echo htmlspecialchars($lang['signup']); ?></h2>
            <p><?php echo htmlspecialchars($lang['signup_message']); ?></p>
            <form action="register.php" method="POST" class="signup-form">
                <label for="username"><?php echo htmlspecialchars($lang['username']); ?>:</label>
                <input type="text" id="username" name="username" required>
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
