document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('darkmode-toggle');

    if (toggleButton) {

        const enableDarkMode = () => {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            toggleButton.innerHTML = '<i class="fa fa-sun"></i>'; // Cambia el ícono a sol
        };

        const disableDarkMode = () => {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
            toggleButton.innerHTML = '<i class="fa fa-moon"></i>'; // Cambia el ícono a luna
        };

        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'enabled') {
            enableDarkMode();
        }

        toggleButton.addEventListener('click', () => {
            console.log('Botón clicado');
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
});
