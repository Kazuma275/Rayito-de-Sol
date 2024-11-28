<?php
    session_start();
    require_once("./controllers/language.php");
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
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="../index.php#parallax-section" class="active"><?php echo $lang['home']; ?></a>
                <a href="../index.php#amenities"><?php echo $lang['amenities']; ?></a>
                <a href="../index.php#gallery"><?php echo $lang['gallery']; ?></a>
                <a href="../index.php#reviews"><?php echo $lang['reviews']; ?></a>
                <a href="../index.php#contact"><?php echo $lang['contact']; ?></a>
                <a href="../index.php#reservation"><?php echo $lang['booking']; ?></a>
                <a href="#"><?php echo $lang['account']; ?></a>

                <!-- Contenedor para la bandera y el modo oscuro -->
                <div class="settings-container">
                    <!-- Selector de idioma -->
                    <div class="language-selector">
                        <img id="current-flag" src="../img/idiomas/<?php echo $_SESSION['lang']; ?>.png" alt="<?php echo $lang['current_lang']; ?>" class="flag">
                        <ul class="language-menu">
                            <li><a href="?lang=en" data-lang="en"><img src="../img/idiomas/en.png" alt="English" class="flag-preview"></a></li>
                            <li><a href="?lang=fr" data-lang="fr"><img src="../img/idiomas/fr.png" alt="Français" class="flag-preview"></a></li>
                            <li><a href="?lang=es" data-lang="es"><img src="../img/idiomas/es.png" alt="Español" class="flag-preview"></a></li>
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
            <h2><?php echo $lang['signup']; ?></h2>
            <p><?php echo $lang['signup_message']; ?><a href="./login.php">ya tienes una cuenta?</a> </p>
            <form action="register.php" method="POST" class="signup-form">
                <label for="username"><?php echo $lang['username']; ?>:</label>
                <input type="text" id="username" name="username" required>

                <label for="password"><?php echo $lang['password']; ?>:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <i id="toggle-password" class="fa fa-eye"></i>
                </div>
                <input type="submit" value="<?php echo $lang['register']; ?>"></input>
            </form>
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
