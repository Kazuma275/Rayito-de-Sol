<?php
// Incluye la configuración de idioma si es necesario
// include_once './controllers/lang.php';
?>

<h2><?php echo $lang['location_title']; ?></h2>
<p><?php echo $lang['location_description']; ?></p>
<p>Estamos ubicados en Calahonda Alhamar, cerca de la playa y con excelentes vistas al mar.</p>

<!-- Contenedor para el mapa -->
<div id="map" style="width: 100%; height: 400px;"></div>

<!-- Cargar la API de Google Maps con la clave de API -->
<script src="https://maps.googleapis.com/maps/api/js?key=TU_CLAVE_API&callback=initMap" async defer></script>

<!-- Llamada al archivo JavaScript externo -->
<script src="js/ubication.js"></script>
