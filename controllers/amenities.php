<?php
require_once "./controllers/conection.php";

// Idioma actual (predeterminado: 'es')
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es';

// Consulta las amenities (comodidades) con sus traducciones
$sql = "
    SELECT 
        a.amenity_id,
        a.icon_path,
        at.title,
        at.description
    FROM 
        amenities a
    JOIN 
        amenity_translations at
    ON 
        a.amenity_id = at.amenity_id
    WHERE 
        at.language_code = ?
    ORDER BY 
        a.amenity_id ASC
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
        echo '<div class="service-card">';

        // Mostrar icono si existe
        if (!empty($row['icon_path'])) {
            echo '<img src="' . htmlspecialchars($row['icon_path']) . '" alt="Icono de la comodidad" class="service-icon">';
        }

        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
        echo '<p>' . nl2br(htmlspecialchars($row['description'])) . '</p>';
        echo '</div>';
    }
} else {
    echo "We didn't find any information, sorry.";
}

// Cierra la declaración
$stmt->close();
?>