<?php
require_once "./controllers/conection.php"; // Asegúrate de que la conexión esté configurada correctamente

$sql = "SELECT author_name, review_text, author_image, review_date FROM reviews"; // Usa los nombres de columna correctos
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="review-card">';
        
        // Mostrar imagen del autor si existe
        if (!empty($row['author_image'])) {
            echo '<img src="' . htmlspecialchars($row['author_image']) . '" alt="Imagen del autor" class="review-img">';
        }

        echo '<p class="review-author">' . htmlspecialchars($row['author_name']) . '</p>';
        echo '<p class="review-text">' . nl2br(htmlspecialchars($row['review_text'])) . '</p>';
        echo '<p class="review-date">' . htmlspecialchars($row['review_date']) . '</p>';
        echo '</div>';
    }
} else {
    echo "No se encontraron resultados.";
}
?>
