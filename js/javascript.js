// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    
    // Variables para el acordeón y la galería
    const galleryItems = document.querySelectorAll('.gallery-thumbnail'); // Todas las imágenes de la galería
    const accordion = document.getElementById('imageAccordion'); // El contenedor del acordeón
    const expandedImg = document.getElementById('expandedImg'); // La imagen ampliada
    const closeButton = document.querySelector('.close'); // El botón de cierre del acordeón
    const prevButton = document.getElementById('prevBtn'); // El botón para ir a la imagen anterior
    const nextButton = document.getElementById('nextBtn'); // El botón para ir a la imagen siguiente
    let currentIndex = 0; // Índice de la imagen actual
    
    // Muestra la imagen ampliada al hacer clic en una de las miniaturas
    galleryItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            expandedImg.src = item.src; // Asigna la imagen clickeada al acordeón
            accordion.style.display = 'flex'; // Muestra el acordeón
            currentIndex = index; // Actualiza el índice de la imagen
        });
    });
    
    // Cierra el acordeón al hacer clic en el botón de cierre
    closeButton.addEventListener('click', () => {
        accordion.style.display = 'none'; // Oculta el acordeón
    });
    
    // Cambia la imagen al navegar entre las imágenes
    function changeImage(direction) {
        const images = Array.from(galleryItems); // Convierte el NodeList de imágenes en un array
        currentIndex = (currentIndex + direction + images.length) % images.length; // Cambia el índice de la imagen
        expandedImg.src = images[currentIndex].src; // Muestra la nueva imagen en el acordeón
    }
    
    // Función para ir a la imagen anterior
    prevButton.addEventListener('click', () => {
        changeImage(-1); // Mover a la imagen anterior
    });
    
    // Función para ir a la imagen siguiente
    nextButton.addEventListener('click', () => {
        changeImage(1); // Mover a la imagen siguiente
    });
    
    // Funcionalidad de navegación por teclado
    document.addEventListener('keydown', (event) => {
        if (accordion.style.display === 'flex') { // Solo cuando el acordeón está visible
            if (event.key === 'ArrowLeft') {
                changeImage(-1); // Mover a la imagen anterior
            } else if (event.key === 'ArrowRight') {
                changeImage(1); // Mover a la imagen siguiente
            } else if (event.key === 'Escape') {
                accordion.style.display = 'none'; // Cerrar el acordeón con la tecla Escape
            }
        }
    });

});
