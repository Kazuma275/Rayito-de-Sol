<?php
session_start();
require_once "../controllers/conection.php";

// Verifica si el formulario de inicio de sesión ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validación básica
    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Consultar el usuario en la base de datos
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña (asegurándose de que esté encriptada)
        if (password_verify($password, $user['password'])) {
            // Guarda la información de la sesión
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time(); // Guarda el tiempo de inicio de sesión

            // Redirige al usuario a la página principal o a la página protegida
            header("Location: ../../index.php");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
}
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
        <section id="login">
        <h2>Login</h2>
            <form action="session.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>

                <input type="submit" value="Login">
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
