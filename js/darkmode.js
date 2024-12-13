document.addEventListener('DOMContentLoaded', () => {
    const toggleCheckbox = document.querySelector('.dayNight input');
    const topNav = document.querySelector('.topnav'); // Selecciona la navbar
    const reviewsContainer = document.querySelector('.reviews-section'); // Contenedor de reviews
    const gallery = document.querySelector('#gallery'); // Contenedor de galleria
    const footer = document.querySelector('.footer'); // Footer
    const amenities = document.querySelector('#amenities'); // Footer


    toggleCheckbox.addEventListener('change', () => {
        if (toggleCheckbox.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            // Aplica estilos para modo oscuro
            topNav.style.backgroundColor = '#121212'; // Fondo negro
            topNav.style.color = '#ffffff'; // Texto blanco
            
            reviewsContainer.style.backgroundColor = '#20252D'; // Fondo oscuro
            reviewsContainer.style.color = '#dddddd'; // Texto claro
            
            gallery.style.backgroundColor = '#2F3843'; // Fondo oscuro
            gallery.style.style.color = '#dddddd'; // Texto

            footer.style.backgroundColor = '#000000'; // Fondo negro
            footer.style.color = '#ffffff'; // Texto blanco

            amenities.style.backgroundColor = '#000000'; // Fondo negro
            amenities.style.color = '#ffffff'; // Texto blanco

        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');

        }
    });
});
