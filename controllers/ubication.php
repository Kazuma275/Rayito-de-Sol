<?php 
session_start();
include './language.php';

echo "Idioma detectado: " . $lang;

?>

<div id="iframe">
    <iframe 
        src="https://www.google.com/maps/embed?pb=...&hl=<?php echo htmlspecialchars($lang); ?>"
        width="100%" 
        height="500px" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>


<!-- Cargar la API de Google Maps con la clave de API -->
<script src="https://maps.googleapis.com/maps/api/js?key=TU_CLAVE_API&callback=initMap" async defer></script>

<!-- Llamada al archivo JavaScript externo -->
<script src="js/ubication.js"></script>
