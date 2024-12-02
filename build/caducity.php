<?php

session_start();

// Configurar la duración de la sesión a 5 minutos (300 segundos)
$session_lifetime = 300; // 5 minutos
ini_set('session.gc_maxlifetime', $session_lifetime);
session_set_cookie_params($session_lifetime);


?>