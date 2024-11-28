document.addEventListener("DOMContentLoaded", function () {
    const languageSelector = document.querySelector(".language-selector");
    const languageMenu = document.querySelector(".language-menu");
    const currentFlag = document.getElementById("current-flag");

    // Mostrar u ocultar el menú de idiomas al hacer clic en la bandera
    languageSelector.addEventListener("click", function (event) {
        event.stopPropagation(); // Evita que el clic se propague y cierre inmediatamente el menú
        languageMenu.classList.toggle("active"); // Alterna la clase "active" para mostrar/ocultar
        console.log("Menú activado", languageMenu.classList.contains("active"));
    });

    // Manejar la selección de un idioma
    languageMenu.addEventListener("click", function (event) {
        const selectedItem = event.target.closest('li');
        if (selectedItem) {
            const newFlagSrc = selectedItem.getAttribute("data-flag");
            
            // Cambia la imagen de la bandera actual
            currentFlag.src = newFlagSrc;
            
            // Cierra el menú después de la selección
            languageMenu.classList.remove("active");
        }
    });

    // Cierra el menú si se hace clic fuera de él
    document.addEventListener("click", function () {
        if (languageMenu.classList.contains("active")) {
            languageMenu.classList.remove("active");
        }
    });
});