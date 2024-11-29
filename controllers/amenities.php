<?php
require_once "./controllers/conection.php";

// Idioma actual (predeterminado: 'es')
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es';

// Consulta las amenities (comodidades) con soporte para idiomas
$sql = "
    SELECT 
        service_id,
        service_name,
        service_description
    FROM 
        amenities
    WHERE 
        language_code = ?
    ORDER BY 
        service_id ASC
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
        echo '<h3>' . htmlspecialchars($row['service_name']) . '</h3>';
        echo '<p>' . nl2br(htmlspecialchars($row['service_description'])) . '</p>';
        echo '</div>';
    }
} else {
    echo "We didn't find any information, sorry.";
}

// Cierra la declaración
$stmt->close();
?>