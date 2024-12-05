<?php
session_start();

// Configuración de idioma y tiempo de sesión
$default_lang = 'es';
$session_lifetime = 360;

// Expiración de la sesión
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
    session_unset();
    session_destroy();
    header("Location: /index.php?session_expired=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Configuración del idioma
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $default_lang;
$_SESSION['lang'] = $lang;

// Modo oscuro
$dark_mode = $_SESSION['dark_mode'] ?? false;
if (isset($_GET['toggle_dark_mode'])) {
    $_SESSION['dark_mode'] = !$dark_mode;
    $dark_mode = $_SESSION['dark_mode'];
}

// Inclusión del archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";
if (!file_exists($lang_file)) {
    die("Error: Archivo de idioma no encontrado.");
}
include $lang_file;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rayito de Sol</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/darkmode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="#parallax-section" class="active">Home</a>
                <a href="#amenities"><?= $lang['amenities'] ?></a>
                <a href="#gallery"><?= $lang['gallery'] ?></a>
                <a href="#reviews"><?= $lang['reviews'] ?></a>
                <a href="#ubication"><?= $lang['ubication'] ?></a>
                <a href="/build/functions/signup.php"><?= $lang['account'] ?></a>

                <?php if ($_SESSION['logged_in'] ?? false): ?>
                    <a href="/build/crud/data/create.php"><?= $lang['make_reservation'] ?></a>
                    <a href="/build/functions/information.php" class="login-message">Hey, <?= $_SESSION['username'] ?></a>
                    <a href="/build/functions/contact.php"><?= $lang['contact_title'] ?></a>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/build/functions/admin.php">Admin Panel</a>
                <?php endif; ?>

                <div class="settings-container">
                    <div class="language-selector">
                        <img id="current-flag" src="/img/idiomas/<?= $lang ?>.png" alt="<?= $lang ?>" class="flag">
                        <ul class="language-menu">
                            <?php foreach (['en', 'fr', 'es', 'cn', 'it', 'br', 'ua', 'ru'] as $code): ?>
                                <li><a href="?lang=<?= $code ?>"><img src="/img/idiomas/<?= $code ?>.png" alt="<?= $code ?>" class="flag-preview"></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <label class="dayNight">
                        <input type="checkbox" id="darkmode-toggle" <?= $dark_mode ? 'checked' : '' ?>>
                        <div></div>
                    </label>
                </div>
            </div>
        </nav>

        <?php if (isset($_GET['session_expired'])): ?>
            <div class="alert">La sesión ha expirado. Por favor, inicia sesión nuevamente.</div>
        <?php endif; ?>

        <section id="parallax-section">
            <div class="parallax-content">
                <?php if (isset($_GET['registration_success'])): ?>
                    <div class="success-message">¡Registro realizado con éxito!</div>
                <?php endif; ?>
                <h1><?= $lang['parallax_title'] ?></h1>
                <p><?= $lang['parallax_subtitle'] ?></p>
            </div>
        </section>

        <section id="amenities" class="intro-section">
            <h2><?= $lang['amenities_title'] ?></h2>
            <p><?= $lang['amenities_description'] ?></p>
            <div class="services-container">
                <?php require_once "./controllers/amenities.php"; ?>
            </div>
        </section>

        <section id="gallery">
            <h2><?= $lang['gallery'] ?></h2>
            <div class="gallery-large">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <img class="gallery-thumbnail" src="img/gallery/img-<?= $i ?>.jpg" alt="Image <?= $i ?>">
                <?php endfor; ?>
            </div>
            <div id="imageAccordion" style="display: none;">
                <span class="close">X</span>
                <img id="expandedImg" src="" alt="Enlarged image">
                <button id="prevBtn">←</button>
                <button id="nextBtn">→</button>
            </div>
        </section>

        <section id="reviews" class="reviews-section">
            <h2><?= $lang['reviews_title'] ?></h2>
            <div class="reviews-container">
                <?php require_once "./controllers/reviews.php"; ?>
            </div>
        </section>

        <section id="ubication">
            <h2><?= $lang['ubication_title'] ?></h2>
            <p><?= $lang['ubication_description'] ?></p>
            <?php require_once './controllers/ubication.php'; ?>
        </section>

        <footer class="footer">
            <div class="container">
                <div class="footer-links">
                    <a href="#" class="active"><?= $lang['home'] ?></a>
                    <a href="#amenities"><?= $lang['amenities'] ?></a>
                    <a href="#gallery"><?= $lang['gallery'] ?></a>
                    <a href="#reviews"><?= $lang['reviews'] ?></a>
                    <a href="/build/functions/signup.php"><?= $lang['account'] ?></a>
                </div>
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                <div class="copyright">
                    &copy; 2024 <a href="https://rayitodesol.es"><?= $lang['site_name'] ?></a>. <?= $lang['rights'] ?>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
