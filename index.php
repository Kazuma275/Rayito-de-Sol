<?php
session_start();

// Configuración de idioma predeterminado y tiempo de vida de la sesión
$default_lang = 'es';
$session_lifetime = 360;

// Verificar expiración de la sesión
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
    session_unset();
    session_destroy();
    header("Location: /index.php?session_expired=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Determinar idioma
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $default_lang;
$_SESSION['lang'] = $lang;

// Determinar estado del modo oscuro
$dark_mode = $_SESSION['dark_mode'] ?? false;
if (isset($_GET['toggle_dark_mode'])) {
    $dark_mode = !$dark_mode;
    $_SESSION['dark_mode'] = $dark_mode;
}

// Incluir archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

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
    <script defer src="/js/navbar.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <nav>
            <div class="topnav responsive" id="myTopnav">
            <a href="javascript:void(0);" onclick="toggleNavbar()" class="active" id="home-link"><?php echo $lang['home']?></a>
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
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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

        <?php if (!empty($_GET['session_expired'])): ?>
            <div class="alert">La sesión ha expirado. Por favor, inicia sesión nuevamente.</div>
        <?php endif; ?>

        <section id="parallax-section">
            <div class="parallax-content">
                <?php if (!empty($_GET['registration_success']) && $_GET['registration_success'] === 'true'): ?>
                    <?php require_once "test.php"; ?>
                    <div class="success-message">¡Registro realizado con éxito!</div>
                <?php endif; ?>
                <h1><?php echo $lang['parallax_title']; ?></h1>
                <p><?php echo $lang['parallax_subtitle']; ?></p>
            </div>
        </section>

        <section id="amenities" class="intro-section">
            <div class="container">
                <h2><?php echo $lang['amenities_title']; ?></h2>
                <p><?php echo $lang['amenities_description']; ?></p>
                <div class="services-container">
                    <?php require_once "./controllers/amenities.php"; ?>
                </div>
            </div>
        </section>

        <section id="gallery">
            <h2><?php echo $lang['gallery']; ?></h2>
            <div class="gallery-large">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <?php if ($i !== 4 && $i !== 8): // Omitir imágenes específicas ?>
                        <img class="gallery-thumbnail" src="img/gallery/img-<?php echo $i; ?>.jpg" alt="Image <?php echo $i; ?>">
                    <?php endif; ?>
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
            <h2><?php echo $lang['reviews_title']; ?></h2>
            <div class="reviews-container">
                <?php require_once "./controllers/reviews.php"; ?>
            </div>
        </section>

        <section id="ubication">
            <h2><?php echo $lang['ubication_title']; ?></h2>
            <p><?php echo $lang['ubication_description']; ?></p>
            <div class="iframe-container">
                <?php require_once './controllers/ubication.php'; ?>
            </div>
        </section>



        <footer class="footer">
            <div class="container">
                <div class="footer-links">
                    <a href="#" class="active"><?php echo $lang['home']; ?></a>
                    <a href="#amenities"><?php echo $lang['amenities']; ?></a>
                    <a href="#gallery"><?php echo $lang['gallery']; ?></a>
                    <a href="#reviews"><?php echo $lang['reviews']; ?></a>
                    <a href="/build/functions/login.php"><?php echo $lang['account']; ?></a>
                </div>
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                <div class="copyright">
                    &copy; 2024 <a href="https://rayitodesol.es"><?php echo $lang['site_name']; ?></a>. <?php echo $lang['rights']; ?>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
