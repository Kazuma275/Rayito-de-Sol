<?php
session_start();
    
# 1. eliminamos la información de la sesión
$_SESSION = [] ;

session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

header("Location: login.php"); // Redirige al formulario de inicio de sesión
exit();
?>
