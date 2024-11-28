
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "./controllers/conection.php"; // Asegúrate de que la conexión esté configurada correctamente

$sql = "SELECT author, review_text FROM reviews_table"; // Cambia el nombre de la tabla y las columnas si es necesario
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    echo "Consulta exitosa. Hay resultados:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "Autor: " . htmlspecialchars($row['author']) . "<br>";
        echo "Reseña: " . htmlspecialchars($row['review_text']) . "<br>";
    }
} else {
    echo "No se encontraron resultados.";
}
?>

<?php
echo "El archivo reviews.php se está cargando correctamente.";
?>

