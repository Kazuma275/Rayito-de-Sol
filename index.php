<?php

session_start();

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

// Verifica si la variable de sesión para el modo oscuro está establecida
if (isset($_SESSION['dark_mode'])) {
    $dark_mode = $_SESSION['dark_mode'];
} else {
    $dark_mode = false; // Estado predeterminado, sin modo oscuro
}

// Si el usuario ha cambiado el modo, guarda la preferencia en la sesión
if (isset($_GET['toggle_dark_mode'])) {
    $dark_mode = !$dark_mode; // Alterna el estado
    $_SESSION['dark_mode'] = $dark_mode; // Guarda en la sesión
}

// Ruta del archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";

// Verifica si el archivo existe
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

    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.png" type="image/x-icon">

    <!-- JS -->
    <script defer src="./js/javascript.js"></script>
    <script defer src="./js/darkmode.js"></script>
    <script defer src="./js/languague.js"></script>

</head>
<body>
    
    <div class="container">

        <!-- Botón de Modo Oscuro/Claro en la parte superior derecha -->
        
        <!-- Navbar -->
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="#parallax-section" class="active">Home</a>
                <a href="#amenities"><?php echo $lang['amenities']?></a>
                <a href="#gallery"><?php echo $lang['gallery']?></a>
                <a href="#reviews"><?php echo $lang['reviews']?></a>
                <a href="#ubication"><?php echo $lang['ubication']; ?></a>
                <a href="./build/signup.php"><?php echo $lang['account']?></a>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                <!-- Mostrar el enlace de reservas solo si la sesión está activa -->
                    <a href="./build/create.php"><?php echo $lang['make_reservation']?></a>
                <?php endif; ?>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                    <a href="./build/information.php" class="login-message"><?php echo "Hey," . $_SESSION['username']?></a>
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
                    <button id="darkmode-toggle" class="dark-mode-toggle">
                        <i class="fa fa-moon"></i>
                    </button>

                </div>
			</div>
        </nav>

        <?php if (isset($_GET['session_expired']) && $_GET['session_expired'] == 'true'): ?>
    <div class="alert">
        La sesión ha expirado. Por favor, inicia sesión nuevamente.
    </div>
<?php endif; ?>

        
        <section id="parallax-section">
            <div class="parallax-content">
                <h1><?php echo $lang['parallax_title']; ?></h1>
                <p><?php echo $lang['parallax_subtitle']; ?></p>
            </div>
        </section>
        

        <section id="amenities" class="intro-section">
        <div class="container">
            <h2><?php echo $lang['amenities_title']; ?></h2>
            <p><?php echo $lang['amenities_description']; ?></p>
            
            <div class="services-container">
                <?php require_once "./controllers/amenities.php";?>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery">
        <h2><?php echo $lang['gallery']?></h2>
            <!-- Thumbnail image gallery -->
            <div class="gallery-large">
                    <img class="gallery-thumbnail" src="img/gallery/img-1.jpg" alt="Image 1">
                    <img class="gallery-thumbnail" src="img/gallery/img-2.jpg" alt="Image 2">
                    <img class="gallery-thumbnail" src="img/gallery/img-3.jpg" alt="Image 3">
                    <img class="gallery-thumbnail" src="img/gallery/img-5.jpg" alt="Image 4">
                    <img class="gallery-thumbnail" src="img/gallery/img-6.jpg" alt="Image 5">
                    <img class="gallery-thumbnail" src="img/gallery/img-7.jpg" alt="Image 6">
                    <img class="gallery-thumbnail" src="img/gallery/img-9.jpg" alt="Image 7">
                    <img class="gallery-thumbnail" src="img/gallery/img-10.jpg" alt="Image 8">
                    <img class="gallery-thumbnail" src="img/gallery/img-12.jpg" alt="Image 9">
                </div>
                
                <!-- Image Modal (Accordion style) -->
                <div id="imageAccordion" style="display: none;">
                    <span class="close">X</span>
                    <img id="expandedImg" src="" alt="Enlarged image">
                    <button id="prevBtn">←</button>
                    <button id="nextBtn">→</button>
                </div>
                
            </section>

            <!-- Reviews -->
            <section id="reviews" class="reviews-section">
        <h2><?php echo $lang['reviews_title']; ?></h2>
        
        <div class="reviews-container">
            <?php require_once "./controllers/reviews.php";?>
        </div>
        
    </section>

    <!-- Ubicación -->
        <section id="ubication">
            <h2><?php echo $lang['ubication_title']; ?></h2>
            <p><?php echo $lang['ubication_description']; ?></p>
            <?php require_once './controllers/ubication.php'; ?>
        </section>

        <footer class="footer">
    <div class="container">
        <!-- Enlaces a secciones -->
        <div class="footer-links">
            <a href="#" class="active"><?php echo $lang['home']; ?></a>
            <a href="#amenities"><?php echo $lang['amenities']; ?></a>
            <a href="#reviews"><?php echo $lang['reviews']; ?></a>
            <a href="#gallery"><?php echo $lang['gallery']; ?></a>
            <a href="./build/signup.php"><?php echo $lang['account']; ?></a>
        </div>

        <!-- Redes sociales -->
        <div class="social-media">
            <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>

        <!-- Derechos de autor -->
        <div class="copyright">
            &copy; 2024 <a href="https://rayitodesol.es"><?php echo $lang['site_name']; ?></a>. <?php echo $lang['rights']; ?>
        </div>
    </div>
</footer>
        

    </div>

</body>
</html>