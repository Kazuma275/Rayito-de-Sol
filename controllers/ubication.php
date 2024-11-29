<?php
// Incluye la configuración de idioma si es necesario
// include_once './controllers/lang.php';
?>

<h2><?php echo $lang['location_title']; ?></h2>
<p><?php echo $lang['location_description']; ?></p>
<p>Estamos ubicados en Calahonda Alhamar, cerca de la playa y con excelentes vistas al mar.</p>

<!-- Incrustar el mapa de Google Maps -->
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d801.9384161512957!2d-4.718110630417203!3d36.48766537649933!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd731f8e245ede8f%3A0xa37947aa87978cdd!2sCl.%20Alhamar%2C%2010%2C%2029649%20Mijas%2C%20M%C3%A1laga!5e0!3m2!1ses!2ses!4v1732882714315!5m2!1ses!2ses" width="auto" height="auto" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

<!-- Cargar la API de Google Maps con la clave de API -->
<script src="https://maps.googleapis.com/maps/api/js?key=TU_CLAVE_API&callback=initMap" async defer></script>

<!-- Llamada al archivo JavaScript externo -->
<script src="js/ubication.js"></script>
