<?php
require_once "./controllers/conection.php"; // Asegúrate de que la conexión esté configurada correctamente

$sql = "SELECT author, review_text, author_image, review_date FROM reviews";
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    echo "Consulta exitosa. Hay resultados:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "Autor: " . htmlspecialchars($row['author_name']) . "<br>";
        echo "Reseña: " . htmlspecialchars($row['review_text']) . "<br>";
        echo "" . htmlspecialchars($row['author_image']) . "<br>";
        echo "Reseña: " . htmlspecialchars($row['review_date']) . "<br>";
    }
} else {
    echo "No se encontraron resultados.";
}
?>