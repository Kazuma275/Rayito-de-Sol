document.addEventListener('DOMContentLoaded', () => {
    const toggleCheckbox = document.querySelector('.dayNight input');
    const topNav = document.querySelector('.topnav');
    const amenities = document.querySelector('#amenities');
    const gallery = document.querySelector('#gallery');
    const reviewsContainer = document.querySelector('#reviews');
    const ubication = document.querySelector('#ubication');
    const footer = document.querySelector('.footer');

    // Función para habilitar modo oscuro
    const enableDarkMode = () => {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');

        // Estilos para elementos específicos
        applyStyles(topNav, { backgroundColor: '#000000', color: '#f2f2f2' });
        applyStyles(amenities, { backgroundColor: '#000000', color: '#dddddd' });
        applyStyles(gallery, { backgroundColor: '#101316', color: '#dddddd' });
        applyStyles(reviewsContainer, { backgroundColor: '#20252D', color: '#dddddd' });
        applyStyles(ubication, { backgroundColor: '#2F3843', color: '#dddddd' });
        applyStyles(footer, { backgroundColor: '#3F4B5A', color: '#dddddd' });
    };

    // Función para deshabilitar modo oscuro
    const disableDarkMode = () => {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');

        // Restaurar estilos para modo claro
        applyStyles(topNav, { backgroundColor: '#9EBBE0', color: '#f2f2f2' });
        applyStyles(amenities, { backgroundColor: '#D8E4F3', color: '#000000' });
        applyStyles(gallery, { backgroundColor: '#E2EBF6', color: '#000000' });
        applyStyles(reviewsContainer, { backgroundColor: '#ECF1F9', color: '#000000' });
        applyStyles(ubication, { backgroundColor: '#F5F8FC', color: '#000000' });
        applyStyles(footer, { backgroundColor: '#9EBBE0', color: '#000000' });
    };

    // Función genérica para aplicar estilos a un elemento
    const applyStyles = (element, styles) => {
        if (element) {
            Object.assign(element.style, styles);
        }
    };

    // Inicializar el estado del modo oscuro
    const initializeDarkMode = () => {
        if (localStorage.getItem('darkMode') === 'enabled') {
            enableDarkMode();
            toggleCheckbox.checked = true;
        } else {
            disableDarkMode();
            toggleCheckbox.checked = false;
        }
    };

    // Evento para alternar modo oscuro
    toggleCheckbox.addEventListener('change', () => {
        if (toggleCheckbox.checked) {
            enableDarkMode();
        } else {
            disableDarkMode();
        }
    });

    // Llamar a la inicialización
    initializeDarkMode();
});
