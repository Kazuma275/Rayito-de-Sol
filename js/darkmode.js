// Modo Oscuro/Claro
const toggleDarkMode = document.getElementById("toggle-dark-mode");

// Verifica si el modo oscuro está activo a través de la clase en el body
document.addEventListener("DOMContentLoaded", () => {
    if (document.body.classList.contains("dark-mode")) {
        toggleDarkMode.querySelector("i").classList.remove("fa-moon");
        toggleDarkMode.querySelector("i").classList.add("fa-sun");
    }
});

toggleDarkMode.addEventListener("click", () => {
    // Alterna la clase dark-mode en el body
    document.body.classList.toggle("dark-mode");
    
    // Cambia el icono de la luna a sol
    const icon = toggleDarkMode.querySelector("i");
    if (document.body.classList.contains("dark-mode")) {
        icon.classList.remove("fa-moon");
        icon.classList.add("fa-sun");
        // Redirige para guardar el estado en la sesión
        window.location.href = "?toggle_dark_mode=true";
    } else {
        icon.classList.remove("fa-sun");
        icon.classList.add("fa-moon");
        // Redirige para guardar el estado en la sesión
        window.location.href = "?toggle_dark_mode=true";
    }
});
