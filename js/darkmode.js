// Selecciona el botón de cambio de modo oscuro
const toggleDarkMode = document.getElementById("toggle-dark-mode");

// Verifica si el modo oscuro está activo a través de la clase en el body
document.addEventListener("DOMContentLoaded", () => {
    if (document.body.classList.contains("dark-mode")) {
        toggleDarkMode.querySelector("i").classList.remove("fa-moon");
        toggleDarkMode.querySelector("i").classList.add("fa-sun");
    }
});

// Evento para alternar el modo oscuro
toggleDarkMode.addEventListener("click", () => {
    // Alterna la clase dark-mode en el body
    document.body.classList.toggle("dark-mode");
    
    // Cambia el icono de la luna a sol y viceversa
    const icon = toggleDarkMode.querySelector("i");
    if (document.body.classList.contains("dark-mode")) {
        icon.classList.remove("fa-moon");
        icon.classList.add("fa-sun");
    } else {
        icon.classList.remove("fa-sun");
        icon.classList.add("fa-moon");
    }
});
