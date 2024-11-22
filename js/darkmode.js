// Modo Oscuro/Claro
const toggleDarkMode = document.getElementById("toggle-dark-mode");

toggleDarkMode.addEventListener("click", () => {
    // Alterna la clase dark-mode en el body
    document.body.classList.toggle("dark-mode");
    
    // Cambiar el icono de la luna a sol
    const icon = document.querySelector("#toggle-dark-mode i");
    if (document.body.classList.contains("dark-mode")) {
        icon.classList.remove("fa-moon");
        icon.classList.add("fa-sun");
    } else {
        icon.classList.remove("fa-sun");
        icon.classList.add("fa-moon");
    }
});
