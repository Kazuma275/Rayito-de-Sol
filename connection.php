<?php
// Conexión a la base de datos (ajusta los datos de conexión a tu servidor de base de datos)
$servername = "localhost";
$username = "sergio";
$password = "almaysergio";
$dbname = "rayito_db";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar los comentarios
$sql = "SELECT c.comentario, c.valoracion, u.nombre, u.foto_url FROM Comentarios c
        JOIN Usuarios u ON c.usuario_id = u.usuario_id
        ORDER BY c.fecha_comentario DESC LIMIT 3"; // Muestra las últimas 3 reseñas
$result = $conn->query($sql);

// Mostrar los comentarios en HTML
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<div class="review-card">
                <img src="' . htmlspecialchars($row['foto_url']) . '" alt="Foto de ' . htmlspecialchars($row['nombre']) . '" class="review-img">
                <div class="review-content">
                    <p class="review-text">"' . htmlspecialchars($row['comentario']) . '"</p>
                    <p class="review-author">- ' . htmlspecialchars($row['nombre']) . '</p>
                </div>
            </div>';
    }
} else {
    echo "No se encontraron reseñas.";
}

$conn->close();
?>
