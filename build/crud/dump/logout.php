<?php
session_start();

$_SESSION = [];

session_unset();
session_destroy();

header("Location: ./build/functions/login.php"); // Redirige al formulario de inicio de sesión
exit();
?>
