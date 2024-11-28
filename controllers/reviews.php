<?php
// Consulta para obtener las reseñas
$query = "SELECT review_id, author_name, review_text, author_image, review_date FROM reviews ORDER BY review_date DESC";
$result = $mysqli->query($query);

// Verifica si se encontraron resultados
if ($result->num_rows > 0) {
    // Bucle para mostrar cada reseña
    while ($row = $result->fetch_assoc()) {
        $authorName = htmlspecialchars($row['author_name']); // Escapa para evitar XSS
        $reviewText = nl2br(htmlspecialchars($row['review_text'])); // Escapa y convierte saltos de línea
        $authorImage = htmlspecialchars($row['author_image']);
        $reviewDate = date('F j, Y', strtotime($row['review_date'])); // Formatea la fecha
        
        // Muestra la reseña
        echo '
        <div class="review-card">
            <img src="' . $authorImage . '" alt="' . $authorName . '" class="review-img">
            <div class="review-content">
                <p class="review-text">' . $reviewText . '</p>
                <p class="review-author">' . $authorName . ' - <span class="review-date">' . $reviewDate . '</span></p>
            </div>
        </div>';
    }
} else {
    echo '<p>No hay reseñas disponibles.</p>';
}
?>
