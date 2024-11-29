<?php
// Este archivo puede incluir la conexión a la base de datos o configuración de idioma si es necesario
// Por ahora, asumimos que $lang ya está definido con los valores adecuados.
?>

<section id="ubication">
    <h2><?php echo $lang['location_title']; ?></h2>
    <p><?php echo $lang['location_description']; ?></p>
    <div id="map" style="width: 100%; height: 400px;"></div>
</section>

<!-- Código para el mapa de Google Maps -->
<script>
    function initMap() {
        // Coordenadas de Calahonda Alhamar
        var location = { lat: 36.5166, lng: -4.7132 };

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: location
        });

        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: 'Apartamento en Calahonda Alhamar'
        });
    }
</script>

<!-- API de Google Maps -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&callback=initMap">
</script>
