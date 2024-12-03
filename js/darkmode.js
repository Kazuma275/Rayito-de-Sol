const toggleButton = document.getElementById('darkmode-toggle');

// Función para activar el modo oscuro
function enableDarkMode() {
    document.body.classList.add('dark-mode');
    localStorage.setItem('darkMode', 'enabled');
}

// Función para desactivar el modo oscuro
function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    localStorage.setItem('darkMode', 'disabled');
}

// Verificar el estado inicial desde localStorage
const darkMode = localStorage.getItem('darkMode');

if (darkMode === 'enabled') {
    enableDarkMode();
}

// Agregar evento al botón para alternar entre modos
if (toggleButton) {
    toggleButton.addEventListener('click', () => {
        const darkMode = localStorage.getItem('darkMode');

        if (darkMode !== 'enabled') {
            enableDarkMode();
        } else {
            disableDarkMode();
        }
    });
}
