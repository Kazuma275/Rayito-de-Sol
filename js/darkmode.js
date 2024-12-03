// Obtener el botón de alternar modo oscuro
const toggleButton = document.getElementById('darkmode-toggle');

// Verificar si el botón existe en el DOM
if (toggleButton) {
    // Función para habilitar el modo oscuro
    const enableDarkMode = () => {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
    };

    // Función para deshabilitar el modo oscuro
    const disableDarkMode = () => {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
    };

    // Verificar el estado inicial desde localStorage
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'enabled') {
        enableDarkMode();
    }

    // Agregar evento al botón
    toggleButton.addEventListener('click', () => {
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode !== 'enabled') {
            enableDarkMode();
        } else {
            disableDarkMode();
        }
    });
} else {
    console.error('No se encontró el botón con ID "darkmode-toggle".');
}
