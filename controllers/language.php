<?php

session_start();

// Configuración de idioma
$default_lang = 'es';

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang; // Guarda en la sesión
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = $default_lang; // Usa idioma predeterminado
}

// Limpia el idioma para evitar caracteres no válidos
$lang = preg_replace('/[^a-z]/', '', $lang);

// Ruta del archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";

// Verifica si el archivo de idioma existe
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado en: $lang_file");
}



?>

