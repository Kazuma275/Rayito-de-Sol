<?php
session_start();
require_once __DIR__ . "/../../../controllers/conection.php";

// Idioma por defecto
$default_lang = 'es';

// Obtención del idioma de la URL o sesión
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $default_lang;
$_SESSION['lang'] = $lang;

// Ruta del archivo de idioma
$lang_file = __DIR__ . "/../../../lang/{$lang}.php";

// Verifica la existencia del archivo de idioma
if (!file_exists($lang_file)) {
    die("Error: Archivo de idioma no encontrado.");
}

include $lang_file;

// Manejo de la reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_date = $conn->real_escape_string($_POST['reservation-date'] ?? '');
    $reservation_time = $conn->real_escape_string($_POST['reservation-time'] ?? '');

    if (empty($reservation_date) || empty($reservation_time)) {
        $error_message = "Por favor, complete todos los campos de la reserva.";
    } else {
        $username = $_SESSION['username'] ?? 'Invitado';
        $stmt = $conn->prepare("INSERT INTO reservations (username, reservation_date, reservation_time) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $reservation_date, $reservation_time);

        if ($stmt->execute()) {
            $success_message = "Reserva realizada con éxito.";
        } else {
            $error_message = "Error al realizar la reserva. Intenta nuevamente.";
        }

        $stmt->close();
        $conn->close();
    }
}
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
    <!-- Fontsawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <!-- JS -->
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="/js/eye.js"></script>
    <!-- Ajax -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="/index.php#parallax-section" class="active">Home</a>
                <a href="/index.php#amenities"><?php echo $lang['amenities']; ?></a>
                <a href="/index.php#gallery"><?php echo $lang['gallery']; ?></a>
                <a href="/index.php#reviews"><?php echo $lang['reviews']; ?></a>
                <a href="/index.php#ubication"><?php echo $lang['ubication']; ?></a>
                <a href="/build/functions/signup.php"><?php echo $lang['account']; ?></a>
                <?php if (!empty($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                    <a href="/build/crud/data/create.php"><?php echo $lang['make_reservation']; ?></a>
                    <a href="/build/functions/information.php" class="login-message"><?php echo "Hey, " . $_SESSION['username']; ?></a>
                    <a href="/build/functions/contact.php"><?php echo $lang['contact_title']; ?></a>
                <?php endif; ?>
                <div class="settings-container">
                    <div class="language-selector">
                        <img id="current-flag" src="/img/idiomas/<?php echo $lang; ?>.png" alt="<?php echo $lang['current_lang'] ?? 'Español'; ?>" class="flag">
                        <ul class="language-menu">
                            <?php foreach (['en', 'fr', 'es', 'cn', 'it', 'br', 'ua', 'ru'] as $lang_code): ?>
                                <li><a href="?lang=<?php echo $lang_code; ?>" data-lang="<?php echo $lang_code; ?>"><img src="/img/idiomas/<?php echo $lang_code; ?>.png" alt="<?php echo ucfirst($lang_code); ?>" class="flag-preview"></a></li>
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

        <!-- Sección de reserva -->
        <section id="reservation">
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <h2><?php echo $lang['reservation_title']; ?></h2>
            <p><?php echo $lang['reservation_description']; ?></p>
            <form class="reservation-form" method="POST">
                <label for="reservation-date"><?php echo $lang['reservation_date_label']; ?></label>
                <input type="date" id="reservation-date" name="reservation-date" required>
                <label for="reservation-time"><?php echo $lang['reservation_time_label']; ?></label>
                <input type="time" id="reservation-time" name="reservation-time" required>
                <button type="submit"><?php echo $lang['reservation_button']; ?></button>
            </form>
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
                <div class="copyright">
                    &copy; 2024 <a href="https://rayitodesol.es">Rayito de Sol</a>. <?php echo $lang['rights']; ?>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>