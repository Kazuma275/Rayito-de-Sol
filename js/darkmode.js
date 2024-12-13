document.addEventListener('DOMContentLoaded', () => {
    const toggleCheckbox = document.querySelector('.dayNight input');
    const topNav = document.querySelector('.topnav'); // Selecciona la navbar

    // Verifica el estado del modo oscuro al cargar la página
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        toggleCheckbox.checked = true; // Marca el checkbox
        topNav.style.backgroundColor = '#121212'; // Fondo negro para navbar
        topNav.style.color = '#ffffff'; // Texto blanco
    }

    toggleCheckbox.addEventListener('change', () => {
        if (toggleCheckbox.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            // Aplica estilos para modo oscuro
            topNav.style.backgroundColor = '#121212'; // Fondo negro
            topNav.style.color = '#ffffff'; // Texto blanco
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
            // Restaura estilos para modo claro
            topNav.style.backgroundColor = '#007bff'; // Fondo azul
            topNav.style.color = '#ffffff'; // Texto blanco
        }
    });
});
