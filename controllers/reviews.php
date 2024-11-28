<?php
require_once "./controllers/conection.php";

// Idioma actual (predeterminado: 'es')
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es';

// Consulta las reseñas con sus traducciones
$sql = "
    SELECT 
        r.review_id,
        r.author_name,
        r.author_image,
        r.review_date,
        rt.review_text
    FROM 
        reviews r
    JOIN 
        review_translations rt 
    ON 
        r.review_id = rt.review_id
    WHERE 
        rt.language_code = ?
    ORDER BY 
        r.review_date DESC
";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincula el idioma a la consulta
$stmt->bind_param("s", $currentLang);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Error en la consulta: " . $stmt->error);
}

// Mostrar los resultados
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
