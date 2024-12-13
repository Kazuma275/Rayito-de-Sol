function toggleNavbar() {
    var nav = document.getElementById("myTopnav");
    if (nav.className === "topnav") {
        nav.className += " responsive"; // Agrega la clase "responsive" para mostrar el menú
    } else {
        nav.className = "topnav"; // Remueve la clase "responsive" para ocultar el menú
    }
}
