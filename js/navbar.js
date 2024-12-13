function toggleNavbar() {
    var nav = document.getElementById("myTopnav");
    // Alternamos la clase 'responsive' para mostrar/ocultar los enlaces
    if (nav.className.indexOf("responsive") === -1) {
        nav.className += " responsive";  // Si 'responsive' no está presente, la agregamos
    } else {
        nav.className = "topnav";  // Si 'responsive' está presente, la eliminamos
    }
}
