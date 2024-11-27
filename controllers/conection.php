<?php

// Configuración de la base de datos
$servername = "localhost";
$username = "sergio"; // Cambia esto si tu usuario de base de datos es diferente
$password = "almaysergio"; // Cambia esto si tu contraseña es diferente
$dbname = "rayito_db"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>