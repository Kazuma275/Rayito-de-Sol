<?php
session_start();

// Define un idioma predeterminado
$default_lang = 'es';

// Obtén el idioma de la URL o de la sesión
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang; // Guarda el idioma seleccionado en la sesión
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang']; // Usa el idioma almacenado en la sesión
} else {
    $lang = $default_lang; // Usa el idioma predeterminado
}

// Verifica si el archivo de idioma existe y se incluye
$lang_file = __DIR__ . "/lang/{$lang}.php";
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    // Si el archivo de idioma no existe, se usa el idioma por defecto
    include __DIR__ . "/lang/{$default_lang}.php";
    $lang = $default_lang;
}

// Verifica el tipo de la variable $lang para depuración
if (!is_string($lang)) {
    die('Error: $lang no es un string.');
}
?>
