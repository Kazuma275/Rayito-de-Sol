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

// Imprime la variable para ver qué valor tiene
var_dump($lang);

// Verifica si $lang es una cadena y no un array
if (!is_string($lang)) {
    $lang = $default_lang; // Asegúrate de que sea una cadena válida
}
?>
