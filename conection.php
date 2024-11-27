<?php
$servername = "rayitodesol.es"; // O la dirección de tu servidor de base de datos
$username = "sergio"; // Usuario de la base de datos
$password = "almaysergio"; // Contraseña del usuario
$dbname = "rayito_db"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("La conexión ha fallado: " . $conn->connect_error);
}
echo "Conexión exitosa";

?>
