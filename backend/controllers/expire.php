<?
session_start();

$session_lifetime = 360;

// Verifica si la sesión tiene un tiempo de expiración configurado
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
    // La sesión ha expirado
    session_unset();     // Borra todas las variables de sesión
    session_destroy();   // Destruye la sesión
    header("Location: /index.php?session_expired=true"); // Redirige a la página index.php en la raíz del proyecto
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Actualiza el tiempo de la última actividad

?>