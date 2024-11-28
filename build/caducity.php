<?php
session_start();

// Configura la caducidad de la sesión (10 segundos)
$sessionTimeout = 10; // tiempo en segundos

// Verifica si la variable de sesión de la última actividad está definida
if (isset($_SESSION['last_activity'])) {
    // Calcula el tiempo de inactividad
    $inactiveTime = time() - $_SESSION['last_activity'];

    // Si la inactividad supera el tiempo de caducidad, cierra la sesión
    if ($inactiveTime > $sessionTimeout) {
        session_unset(); // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión
        header("Location: login.php"); // Redirige al login
        exit();
    }
}

// Actualiza la variable de última actividad con la hora actual
$_SESSION['last_activity'] = time();

// Verifica si la sesión está activa
if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true) {
    echo "Bienvenido, " . htmlspecialchars($_SESSION['username']) . "!<br>";
    echo "La sesión es activa y caducará en 10 segundos de inactividad.";
} else {
    header("Location: login.php"); // Redirige si no está autenticado
    exit();
}
?>
