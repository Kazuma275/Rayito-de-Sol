<?php
require_once "./controllers/conection.php";

// Idioma actual (predeterminado: 'es')
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es';

// Consulta los servicios con sus traducciones
$sql = "
    SELECT 
        s.service_id,
        st.service_name,
        st.service_description,
        s.icon_path
    FROM 
        services s
    JOIN 
        services_translations st 
    ON 
        s.service_id = st.service_id
    WHERE 
        st.language_code = ?
    ORDER BY 
        s.service_id ASC
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
        
        // Mostrar ícono si existe
        if (!empty($row['icon_path'])) {
            echo '<img src="' . htmlspecialchars($row['icon_path']) . '" alt="' . htmlspecialchars($row['service_name']) . '" class="service-icon">';
        }

        // Mostrar nombre y descripción del servicio
        echo '<h3>' . htmlspecialchars($row['service_name']) . '</h3>';
        echo '<p>' . nl2br(htmlspecialchars($row['service_description'])) . '</p>';
        echo '</div>';
    }
} else {
    echo "<p>No encontramos servicios disponibles en este idioma.</p>";
}

// Cierra la declaración
$stmt->close();
?>
