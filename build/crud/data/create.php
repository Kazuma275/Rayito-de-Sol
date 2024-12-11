<?php
session_start();
require_once __DIR__ . "/../../../controllers/conection.php";  

// Define un idioma predeterminado
$default_lang = 'es';

// Obtén el idioma de la URL o de la sesión
$lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['lang'] ?? $default_lang);

// Guarda el idioma en la sesión si es una nueva selección
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $lang;
}

// Ruta del archivo de idioma y carga con manejo de errores
$lang_file = __DIR__ . "/lang/{$lang}.php";
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

// Manejo de la lógica de inserción de datos si se recibe una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_date = $_POST['reservation-date'] ?? '';
    $reservation_time = $_POST['reservation-time'] ?? '';

    // Validación de campos
    if (empty($reservation_date) || empty($reservation_time)) {
        die("Por favor, complete todos los campos de la reserva.");
    }

    // Escape de datos para evitar inyecciones SQL
    $reservation_date = $conn->real_escape_string($reservation_date);
    $reservation_time = $conn->real_escape_string($reservation_time);

    // Captura el nombre de usuario de la sesión
    $username = $_SESSION['username'] ?? 'Invitado';

    // Inserta los datos de forma segura usando una declaración preparada
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
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($_SESSION['lang'], ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lang['title'], ENT_QUOTES, 'UTF-8'); ?></title>

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
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="/index.php#parallax-section" class="active">Home</a>
                <a href="/index.php#amenities"><?php echo htmlspecialchars($lang['amenities'], ENT_QUOTES, 'UTF-8'); ?></a>
                <a href="/index.php#gallery"><?php echo htmlspecialchars($lang['gallery'], ENT_QUOTES, 'UTF-8'); ?></a>
                <a href="/index.php#reviews"><?php echo htmlspecialchars($lang['reviews'], ENT_QUOTES, 'UTF-8'); ?></a>
                <a href="/index.php#ubication"><?php echo htmlspecialchars($lang['ubication'], ENT_QUOTES, 'UTF-8'); ?></a>
                <a href="/build/functions/signup.php"><?php echo htmlspecialchars($lang['account'], ENT_QUOTES, 'UTF-8'); ?></a>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                    <a href="/build/crud/data/create.php"><?php echo htmlspecialchars($lang['make_reservation'], ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="/build/functions/information.php" class="login-message"><?php echo "Hey, " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="/build/functions/contact.php"><?php echo htmlspecialchars($lang['contact_title'], ENT_QUOTES, 'UTF-8'); ?></a>
                <?php endif; ?>

                <!-- Selector de idioma y modo oscuro -->
                <div class="settings-container" style="position: relative;">
                    <div class="language-selector">
                        <img id="current-flag" src="/img/idiomas/<?php echo htmlspecialchars($_SESSION['lang'], ENT_QUOTES, 'UTF-8'); ?>.png" alt="<?php echo htmlspecialchars($lang['current_lang'] ?? 'Español', ENT_QUOTES, 'UTF-8'); ?>" class="flag">
                        <ul class="language-menu">
                            <?php 
                            $languages = ['en' => 'English', 'fr' => 'Français', 'es' => 'Español', 'cn' => '中国人', 'it' => 'Italiano', 'br' => 'Brasileiro', 'ua' => 'українська', 'ru' => 'Русский'];
                            foreach ($languages as $code => $label) {
                                echo "<li><a href=\"?lang=$code\" data-lang=\"$code\"><img src=\"/img/idiomas/$code.png\" alt=\"$label\" class=\"flag-preview\"></a></li>";
                            }
                            ?>
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
                <div class="success-message"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($lang['reservation_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><?php echo htmlspecialchars($lang['reservation_description'], ENT_QUOTES, 'UTF-8'); ?></p>
            <form class="reservation-form" method="POST" action="">
                <label for="reservation-date"><?php echo htmlspecialchars($lang['reservation_date_label'], ENT_QUOTES, 'UTF-8'); ?></label>
                <input type="date" id="reservation-date" name="reservation-date" required>

                <label for="reservation-time"><?php echo htmlspecialchars($lang['reservation_time_label'], ENT_QUOTES, 'UTF-8'); ?></label>
                <input type="time" id="reservation-time" name="reservation-time" required>

                <button type="submit"><?php echo htmlspecialchars($lang['reservation_button'], ENT_QUOTES, 'UTF-8'); ?></button>
            </form>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-links">
                    <a href="/index.php#parallax-section"><?php echo $lang['home']; ?></a>
                    <a href="/index.php#amenities"><?php echo $lang['amenities']; ?></a>
                    <a href="/index.php#reviews"><?php echo $lang['reviews']; ?></a>
                    <a href="/index.php#gallery"><?php echo $lang['gallery']; ?></a>
                    <a href="#"><?php echo $lang['account']; ?></a>
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
