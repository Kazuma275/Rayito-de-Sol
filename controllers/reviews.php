<?php
require_once "./controllers/conection.php"; // Asegúrate de que la conexión esté configurada correctamente

// Obtén el idioma actual desde la sesión (usa 'en' como predeterminado si no está definido)
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Modifica la consulta para incluir el filtro por idioma
$sql = "SELECT author_name, review_text, author_image, review_date 
        FROM reviews 
        WHERE language = ?"; // Usa un marcador para evitar inyección SQL

// Prepara la consulta
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincula el idioma a la consulta
$stmt->bind_param("s", $currentLang); // "s" indica que el parámetro es una cadena (string)
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Error en la consulta: " . $stmt->error);
}

// Muestra los resultados
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
    echo "No se encontraron resultados para el idioma seleccionado.";
}

// Cierra la declaración
$stmt->close();
?>
