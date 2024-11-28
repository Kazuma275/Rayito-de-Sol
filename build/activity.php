<?php
session_start();

// Configura la caducidad de la sesión (en segundos)
$sessionTimeout = 10; // 10 segundos

// Verifica si la variable de sesión de la última actividad está definida
if (isset($_SESSION['last_activity'])) {
    $inactiveTime = time() - $_SESSION['last_activity'];

    // Si la inactividad supera el tiempo de caducidad, cierra la sesión y redirige
    if ($inactiveTime > $sessionTimeout) {
        session_unset(); // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión
        header("Location: ../index.php"); // Redirige al inicio
        exit();
    }
}

// Actualiza la variable de última actividad con la hora actual
$_SESSION['last_activity'] = time();
?>
