document.addEventListener('DOMContentLoaded', () => {
    const toggleCheckbox = document.querySelector('.dayNight input');
    const topNav = document.querySelector('.topnav'); // Selecciona la navbar
    const amenities = document.querySelector('#amenities'); // Amenidades
    const gallery = document.querySelector('#gallery'); // Contenedor de galleria
    const reviewsContainer = document.querySelector('#reviews'); // Contenedor de reviews
    const ubication = document.querySelector('#ubication'); // Ubicación
    const footer = document.querySelector('.footer'); // Footer

       // Verifica el estado del modo oscuro al cargar la página
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        toggleCheckbox.checked = true; // Marca el checkbox
        
        topNav.style.backgroundColor = '#121212'; // Fondo negro
        topNav.style.color = '#ffffff'; // Texto blanco

        amenities.style.backgroundColor = '#000000'; // Fondo negro
        amenities.style.color = '#ffffff'; // Texto blanco

        gallery.style.backgroundColor = '#101316'; // Fondo oscuro
        gallery.style.style.color = '#dddddd'; // Texto
        
        reviewsContainer.style.backgroundColor = '#20252D'; // Fondo oscuro
        reviewsContainer.style.color = '#dddddd'; // Texto claro

        ubication.style.backgroundColor = '#2F3843'; // Fondo
        ubication.style.color = '#ffffff'; // Texto blanco

        footer.style.backgroundColor = '#3F4B5A'; // Fondo negro
        footer.style.color = '#ffffff'; // Texto blanco

    }

    toggleCheckbox.addEventListener('change', () => {
        if (toggleCheckbox.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            // Aplica estilos para modo oscuro
            topNav.style.backgroundColor = '#121212'; // Fondo negro
            topNav.style.color = '#ffffff'; // Texto blanco

            amenities.style.backgroundColor = '#000000'; // Fondo negro
            amenities.style.color = '#ffffff'; // Texto blanco

            gallery.style.backgroundColor = '#101316'; // Fondo oscuro
            gallery.style.style.color = '#dddddd'; // Texto
            
            reviewsContainer.style.backgroundColor = '#20252D'; // Fondo oscuro
            reviewsContainer.style.color = '#dddddd'; // Texto claro

            ubication.style.backgroundColor = '#2F3843'; // Fondo
            ubication.style.color = '#ffffff'; // Texto blanco

            footer.style.backgroundColor = '#3F4B5A'; // Fondo negro
            footer.style.color = '#ffffff'; // Texto blanco


        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
            // Restaura estilos para modo claro
            topNav.style.backgroundColor = '#9EBBE0'; // Fondo azul
            topNav.style.color = '#f2f2f2'; // Texto blanco

            amenities.style.backgroundColor = '#D8E4F3'; // Fondo claro
            amenities.style.color = '#000000'; // Texto oscuro
            
            gallery.style.backgroundColor = '#E2EBF6'; // Fondo claro
            gallery.style.color = '#000000'; // Texto oscuro

            // Estilos para reviews-container en modo claro
            reviewsContainer.style.backgroundColor = '#ECF1F9'; // Fondo claro
            reviewsContainer.style.color = '#000000'; // Texto oscuro

            ubication.style.backgroundColor = '#F5F8FC'; // Fondo claro
            ubication.style.color = '#000000'; // Texto oscuro

            footer.style.backgroundColor = '#FCFCFC'; // Fondo claro
            footer.style.color = '#000000'; // Texto oscuro

        }
    });
});
