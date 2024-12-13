document.addEventListener('DOMContentLoaded', () => {
    const toggleCheckbox = document.querySelector('.dayNight input');
    const topNav = document.querySelector('.topnav'); // Selecciona la navbar
    const amenities = document.querySelector('#amenities'); // Amenidades
    const gallery = document.querySelector('#gallery'); // Contenedor de galleria
    const reviewsContainer = document.querySelector('#reviews'); // Contenedor de reviews
    const ubication = document.querySelector('#ubication'); // Ubicación
    const footer = document.querySelector('.footer'); // Footer

    toggleCheckbox.addEventListener('change', () => {
        if (toggleCheckbox.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');

            // Restaura estilos para modo claro
            topNav.style.backgroundColor = '#000000'; 
            topNav.style.color = '#f2f2f2'; // Texto blanco

            amenities.style.backgroundColor = '#000000';
            amenities.style.color = '#000000'; // Texto oscuro
            
            gallery.style.backgroundColor = '#101316';
            gallery.style.color = '#000000'; // Texto oscuro

            // Estilos para reviews-container en modo claro
            reviewsContainer.style.backgroundColor = '#20252D';
            reviewsContainer.style.color = '#000000'; // Texto oscuro

            ubication.style.backgroundColor = '#2F3843';
            ubication.style.color = '#000000'; // Texto oscuro

            footer.style.backgroundColor = '#3F4B5A';
            footer.style.color = '#000000'; // Texto oscuro

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

            footer.style.backgroundColor = '#9EBBE0'; // Fondo claro
            footer.style.color = '#000000'; // Texto oscuro

        }
    });
});
