<?php
session_start();
require_once __DIR__ . "/../../controllers/conection.php";

// Definir idioma por defecto
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'es';
$_SESSION['lang'] = $lang;

// Cargar archivo de idioma
$lang_file = dirname(dirname(__DIR__)) . "/lang/{$lang}.php";
if (!file_exists($lang_file)) {
    die("Error: Archivo de idioma no encontrado.");
}
include $lang_file;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($customer_name && $customer_email && $message) {
        $customer_name = $conn->real_escape_string($customer_name);
        $customer_email = $conn->real_escape_string($customer_email);
        $message = $conn->real_escape_string($message);

        $stmt = $conn->prepare("INSERT INTO contact (customer_name, customer_email, message) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $customer_name, $customer_email, $message);
            if ($stmt->execute()) {
                $success_message = "Mensaje enviado con éxito.";
            } else {
                $error_message = "Error al enviar el mensaje: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error en la preparación de la consulta: " . $conn->error;
        }
    } else {
        $error_message = "Todos los campos son obligatorios.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['contact_title']; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/darkmode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="/js/eye.js"></script>
</head>
<body>
    <div class="container">
        <nav>
            <div class="topnav" id="myTopnav">
                <!-- Navbar básico -->
                <a href="/index.php#parallax-section" class="active">Home</a>
                <a href="/index.php#amenities"><?php echo $lang['amenities']; ?></a>
                <a href="/index.php#gallery"><?php echo $lang['gallery']; ?></a>
                <a href="/index.php#reviews"><?php echo $lang['reviews']; ?></a>
                <a href="/index.php#ubication"><?php echo $lang['ubication']; ?></a>
                <a href="/build/functions/signup.php"><?php echo $lang['account']; ?></a>
                <!-- Si el usuario ha iniciado sesión saldrán estos menus -->
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in']): ?>
                    <a href="/build/crud/data/create.php"><?php echo $lang['make_reservation']; ?></a>
                    <a href="/build/functions/information.php" class="login-message">Hey, <?php echo $_SESSION['username']; ?></a>
                    <a href="/build/functions/contact.php"><?php echo $lang['contact_title']; ?></a>
                <?php endif; ?>
                <div class="settings-container" style="position: relative;">
                    <div class="language-selector">

                        <!-- Bandera actual -->
                        <img id="current-flag" src="/img/idiomas/<?php echo $_SESSION['lang']; ?>.png" alt="<?php echo $lang['current_lang'] ?? 'Español'; ?>" class="flag">
                        
                        <!-- Desplegable de idiomas -->
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

        <section id="contact">
            <!-- Contact tittle -->
            <h2><?php echo $lang['contact_title']; ?></h2>
            <p><?php echo $lang['contact_description']; ?></p>
            
            <!-- Mensaje de success -->
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <!-- Formulario de contacto -->
            <form method="POST" class="contact-form">
                <label for="customer_name"><?php echo $lang['contact_name_label']; ?></label>
                <input type="text" id="customer_name" name="customer_name" required>
                <label for="customer_email"><?php echo $lang['contact_email_label']; ?></label>
                <input type="email" id="customer_email" name="customer_email" required>
                <label for="message"><?php echo $lang['contact_message_label']; ?></label>
                <textarea id="message" name="message" required></textarea>
                <button type="submit"><?php echo $lang['contact_button']; ?></button>
            </form>
        </section>

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
