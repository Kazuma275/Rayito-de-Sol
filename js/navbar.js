function toggleNavbar() {
    var nav = document.getElementById("myTopnav");
    // Alternamos la clase 'responsive' para mostrar/ocultar los enlaces
    if (nav.className === "topnav") {
        nav.className += " responsive";  // Agregamos la clase 'responsive' que activa la visualización de los enlaces
    } else {
        nav.className = "topnav";  // Removemos la clase 'responsive', ocultando los enlaces
    }
}
