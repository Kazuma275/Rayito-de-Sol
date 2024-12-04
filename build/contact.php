<?php

session_start();

require_once "../controllers/conection.php";

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

// Ruta del archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";

// Verifica si el archivo existe
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

// Procesa el formulario de contacto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    // Verifica que los campos no estén vacíos
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = $lang['contact_error_fields'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = $lang['contact_error_email'];
    } else {
        // Escapa los datos para evitar inyecciones SQL
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $message = $conn->real_escape_string($message);

        // Inserta los datos en la tabla "contact_messages"
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $success_message = $lang['contact_success'];
        } else {
            $error_message = $lang['contact_error_general'];
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
    <title><?php echo $lang['contact_title']; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="../css/darkmode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <!-- Favicon -->
    <link rel="icon" href="./img/favicon.png" type="image/x-icon">

    <!-- JS -->
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="/js/eye.js"></script>
</head>
<body>
    <div class="container">
    <nav>
            <div class="topnav" id="myTopnav">
                <a href="/index.php#parallax-section" class="active">Home</a>
                <a href="/index.php#amenities"><?php echo $lang['amenities']?></a>
                <a href="/index.php#gallery"><?php echo $lang['gallery']?></a>
                <a href="/index.php#reviews"><?php echo $lang['reviews']?></a>
                <a href="/index.php#ubication"><?php echo $lang['ubication']; ?></a>
                <a href="/build/signup.php"><?php echo $lang['account']?></a>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true): ?>
                <!-- Mostrar el enlace de reservas solo si la sesión está activa -->
                    <a href="/build/create.php"><?php echo $lang['make_reservation']?></a>
                    <a href="/build/information.php" class="login-message"><?php echo "Hey," . $_SESSION['username']?></a>
                    <a href="/build/contact.php">Contacta</a>
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

                    <label class="dayNight">
                        <input type="checkbox" id="darkmode-toggle">
                        <div></div>
                    </label>

                </div>
			</div>
        </nav>

        <!-- Sección "Contact" -->
        <section id="contact">
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                </div>
            <?php elseif (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <h2><?php echo $lang['contact_header']; ?></h2>
            <p><?php echo $lang['contact_description']; ?></p>

            <form class="contact-form" method="POST" action="">
                <label for="name"><?php echo $lang['contact_name_label']; ?></label>
                <input type="text" id="name" name="name" required>

                <label for="email"><?php echo $lang['contact_email_label']; ?></label>
                <input type="email" id="email" name="email" required>

                <label for="message"><?php echo $lang['contact_message_label']; ?></label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit"><?php echo $lang['contact_button']; ?></button>
            </form>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <!-- Sección de enlaces -->
                <div class="footer-links">
                    <a href="#" class="active"><?php echo $lang['home']; ?></a>
                    <a href="../index.php#amenities"><?php echo $lang['amenities']; ?></a>
                    <a href="../index.php#reviews"><?php echo $lang['reviews']; ?></a>
                    <a href="../index.php#gallery"><?php echo $lang['gallery']; ?></a>
                    <a href="/build/account.php"><?php echo $lang['account']; ?></a>
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
