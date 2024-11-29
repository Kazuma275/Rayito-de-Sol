// Función para inicializar el mapa
function initMap() {
    // Coordenadas de Calahonda Alhamar
    var location = { lat: 36.5152, lng: -4.7046 };

    // Crear el mapa y centrarlo en las coordenadas
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: location
    });

    // Agregar un marcador en la ubicación
    var marker = new google.maps.Marker({
        position: location,
        map: map,
        title: 'Calahonda Alhamar'
    });
}
