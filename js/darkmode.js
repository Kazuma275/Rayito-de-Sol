// Obtener el botón de alternar modo oscuro
const toggleButton = document.getElementById('darkmode-toggle');

// Verificar si el botón existe en el DOM
if (toggleButton) {
    // Función para habilitar el modo oscuro
    const enableDarkMode = () => {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');

        // Cambiar el ícono a sol
        toggleButton.innerHTML = '<i class="fa fa-sun"></i>';
    };

    // Función para deshabilitar el modo oscuro
    const disableDarkMode = () => {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');

        // Cambiar el ícono a luna
        toggleButton.innerHTML = '<i class="fa fa-moon"></i>';
    };

    // Verificar el estado inicial desde localStorage
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'enabled') {
        enableDarkMode();
    }

    // Agregar evento al botón
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.getElementById('darkmode-toggle');
    
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                console.log('Botón clicado'); // Verifica que el clic se detecta
            });
        } else {
            console.error('No se encontró el botón con ID "darkmode-toggle".');
        }
    });
    
} else {
    console.error('No se encontró el botón con ID "darkmode-toggle".');
}
