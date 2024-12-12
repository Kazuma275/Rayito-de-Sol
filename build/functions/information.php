<?php

session_start();

// Inicializa el token de sesión
$_SESSION["token"] = md5(time());

// Idioma por defecto
$default_lang = 'es';

// Determina el idioma actual
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $default_lang;
$_SESSION['lang'] = $lang;

// Ruta del archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";

// Carga el archivo de idioma o muestra un error
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}

// Redirige al login si no está autenticado
if (!isset($_SESSION['username']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Obtén el nombre de usuario de la sesión
$username = htmlspecialchars($_SESSION['username']);

// Incluye la conexión a la base de datos
require_once dirname(dirname(__DIR__)) . "/../../controllers/conection.php";  

// Procesa la actualización de la contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $new_password = $_POST['password'];

    if (empty($new_password)) {
        echo "La contraseña no puede estar vacía.";
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('si', $hashed_password, $user_id);
        echo $stmt->execute() ? "Contraseña actualizada correctamente." : "Error al actualizar la contraseña: " . $stmt->error;
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conn->error;
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['manage_account']; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/darkmode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <script defer src="/js/javascript.js"></script>
    <script defer src="/js/darkmode.js"></script>
    <script defer src="/js/languague.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
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

        <section id="informacion">
            <h2>Gestión de Cuenta</h2>
            <p>Administra la información de tu cuenta y ajusta tus preferencias aquí.</p>
            <form id="reservation" method="POST" action="../crud/update/update_password.php" class="manage-form login-form">
                <label for="password">Actualizar contraseña:</label>
                <input type="password" id="password" name="password" required>
                <input type="submit" value="Guardar Cambios">
                <a href="../crud/dump/logout.php" class="logout-button">Cerrar Sesión</a>
            </form>
        </section>

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
