document.addEventListener('DOMContentLoaded', () => {
    const toggleCheckbox = document.querySelector('.dayNight input');
    const topNav = document.querySelector('.topnav'); // Selecciona la navbar
    const reviewsContainer = document.querySelector('.reviews-section'); // Contenedor de reviews

    // Verifica el estado del modo oscuro al cargar la página
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        toggleCheckbox.checked = true; // Marca el checkbox
        topNav.style.backgroundColor = '#121212'; // Fondo negro para navbar
        topNav.style.color = '#ffffff'; // Texto blanco
        reviewsContainer.style.backgroundColor = '#1e1e1e'; // Fondo oscuro
        reviewsContainer.style.color = '#dddddd'; // Texto claro
    }

    toggleCheckbox.addEventListener('change', () => {
        if (toggleCheckbox.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            // Aplica estilos para modo oscuro
            topNav.style.backgroundColor = '#121212'; // Fondo negro
            topNav.style.color = '#ffffff'; // Texto blanco
            reviewsContainer.style.backgroundColor = '#1e1e1e'; // Fondo oscuro
            reviewsContainer.style.color = '#dddddd'; // Texto claro
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
            // Restaura estilos para modo claro
            topNav.style.backgroundColor = '#9EBBE0'; // Fondo azul
            topNav.style.color = '#f2f2f2'; // Texto blanco
            // Estilos para reviews-container en modo claro
            reviewsContainer.style.backgroundColor = '#ffffff'; // Fondo claro
            reviewsContainer.style.color = '#000000'; // Texto oscuro
        }
    });
});
