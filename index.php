<?php

session_start();

// Define un idioma predeterminado
$default_lang = 'es';

// Obtén el idioma de la URL o de la sesión
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang; // Guarda el idioma seleccionado en la sesión
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang']; // Usa el idioma almacenado en la sesión
} else {
    $lang = $default_lang; // Usa el idioma predeterminado
}

// Ruta del archivo de idioma
$lang_file = __DIR__ . "/lang/{$lang}.php";

// Verifica si el archivo existe
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    die("Error: Archivo de idioma no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rayito de Sol</title>

    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.css">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.png" type="image/x-icon">

    <!-- JS -->
    <script defer src="./js/javascript.js"></script>
    <script defer src="./js/darkmode.js"></script>

</head>
<body>
    
    <div class="container">

        <!-- Botón de Modo Oscuro/Claro en la parte superior derecha -->
        
        <!-- Navbar -->
        <nav>
            <div class="topnav" id="myTopnav">
                <a href="#parallax-section" class="active">Home</a>
                <a href="#amenities"><?php echo $lang['amenities']?></a>
                <a href="#gallery"><?php echo $lang['gallery']?></a>
                <a href="#reviews"><?php echo $lang['reviews']?></a>
                <a href="#contact"><?php echo $lang['contact']?></a>
                <a href="#reservation"><?php echo $lang['booking']?></a>
                <a href="./build/signup.php"><?php echo $lang['account']?></a>

                <button id="toggle-dark-mode" class="dark-mode-toggle">
                    <i class="fa fa-moon"></i>
                </button>
			</div>
        </nav>
        
        <section id="parallax-section">
            <div class="parallax-content">
                <h1><?php echo $lang['parallax_title']?></h1>
                <p><?php echo $lang['parallax_subtitle']?></p>
            </div>
        </section>
        

        <section id="amenities" class="intro-section">
        <div class="container">
            <h2><?php echo $lang['amenities_title']; ?></h2>
            <p><?php echo $lang['amenities_description']; ?></p>
            
            <div class="services-container">
                <!-- Main services of the apartment -->
                <div class="service-card">
                    <h3><?php echo $lang['bedroom_title']; ?></h3>
                    <p><?php echo $lang['bedroom_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['bathroom_title']; ?></h3>
                    <p><?php echo $lang['bathroom_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['lounge_title']; ?></h3>
                    <p><?php echo $lang['lounge_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['sea_views_title']; ?></h3>
                    <p><?php echo $lang['sea_views_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['laundry_title']; ?></h3>
                    <p><?php echo $lang['laundry_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['kitchen_extras_title']; ?></h3>
                    <p><?php echo $lang['kitchen_extras_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['lock_title']; ?></h3>
                    <p><?php echo $lang['lock_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['other_amenities_title']; ?></h3>
                    <p><?php echo $lang['other_amenities_description']; ?></p>
                </div>

                <div class="service-card">
                    <h3><?php echo $lang['pool_title']; ?></h3>
                    <p><?php echo $lang['pool_description']; ?></p>
                </div>
                
                <div class="service-card">
                    <h3><?php echo $lang['wifi_title']; ?></h3>
                    <p><?php echo $lang['wifi_description']; ?></p>
                </div>
                
                <div class="service-card">
                    <h3><?php echo $lang['restaurant_title']; ?></h3>
                    <p><?php echo $lang['restaurant_description']; ?></p>
                </div>
                
                <div class="service-card">
                    <h3><?php echo $lang['beach_title']; ?></h3>
                    <p><?php echo $lang['beach_description']; ?></p>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery">
        <h2><?php echo $lang['gallery']?></h2>
            <!-- Thumbnail image gallery -->
            <div class="gallery-large">
                    <img class="gallery-thumbnail" src="img/gallery/img-1.jpg" alt="Image 1">
                    <img class="gallery-thumbnail" src="img/gallery/img-2.jpg" alt="Image 2">
                    <img class="gallery-thumbnail" src="img/gallery/img-3.jpg" alt="Image 3">
                    <img class="gallery-thumbnail" src="img/gallery/img-5.jpg" alt="Image 4">
                    <img class="gallery-thumbnail" src="img/gallery/img-6.jpg" alt="Image 5">
                    <img class="gallery-thumbnail" src="img/gallery/img-7.jpg" alt="Image 6">
                    <img class="gallery-thumbnail" src="img/gallery/img-9.jpg" alt="Image 7">
                    <img class="gallery-thumbnail" src="img/gallery/img-10.jpg" alt="Image 8">
                    <img class="gallery-thumbnail" src="img/gallery/img-12.jpg" alt="Image 9">
                </div>
                
                <!-- Image Modal (Accordion style) -->
                <div id="imageAccordion" style="display: none;">
                    <span class="close">X</span>
                    <img id="expandedImg" src="" alt="Enlarged image">
                    <button id="prevBtn">←</button>
                    <button id="nextBtn">→</button>
                </div>
                
            </section>

            <section id="reviews" class="reviews-section">
        <h2><?php echo $lang['reviews_title']; ?></h2>
        
        <div class="reviews-container">
            <!-- Review 1 -->
            <div class="review-card">
                <img src="https://media.istockphoto.com/id/494711330/es/foto/hombre-joven-latina-en-un-estudio.jpg?s=612x612&w=0&k=20&c=Ye9u097upgUhpcVttIB5i39Dj8XOPheoL4HB6SHC9iA=" alt="<?php echo $lang['review_1_author']; ?>" class="review-img">
                <div class="review-content">
                    <p class="review-text"><?php echo $lang['review_1_text']; ?></p>
                    <p class="review-author"><?php echo $lang['review_1_author']; ?></p>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="review-card">
                <img src="https://media.istockphoto.com/id/1311957094/es/foto/guapo-joven-sonriente-con-retrato-de-brazos-cruzados.jpg?s=612x612&w=0&k=20&c=x5LVA3-Y4WCfJmz6FzGTjXYv4tB1HPVkLuhLqcj8g6Q=" alt="<?php echo $lang['review_2_author']; ?>" class="review-img">
                <div class="review-content">
                    <p class="review-text"><?php echo $lang['review_2_text']; ?></p>
                    <p class="review-author"><?php echo $lang['review_2_author']; ?></p>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="review-card">
                <img src="https://img.freepik.com/fotos-premium/joven-alegre-brasil-camiseta-casual-vaqueros_941493-3125.jpg?w=360" alt="<?php echo $lang['review_3_author']; ?>" class="review-img">
                <div class="review-content">
                    <p class="review-text"><?php echo $lang['review_3_text']; ?></p>
                    <p class="review-author"><?php echo $lang['review_3_author']; ?></p>
                </div>
            </div>

            <!-- Review 4 -->
            <div class="review-card">
                <img src="https://media.gettyimages.com/id/1404193313/es/foto/man-walking.jpg?s=612x612&w=gi&k=20&c=3sSFQgcAb6wAtAGwI8SEG786gEP4ZSaMuJ-3H_J9rKQ=" alt="<?php echo $lang['review_4_author']; ?>" class="review-img">
                <div class="review-content">
                    <p class="review-text"><?php echo $lang['review_4_text']; ?></p>
                    <p class="review-author"><?php echo $lang['review_4_author']; ?></p>
                </div>
            </div>
        </div>
    </section>

        

        <!--  Sección "Reservation" -->

        <section id="reservation">
        <h2><?php echo $lang['reservation_title']; ?></h2>
        <p><?php echo $lang['reservation_description']; ?></p>
            <form class="reservation-form">
                <label for="reservation-date"><?php echo $lang['reservation_date_label']; ?></label>
                <input type="date" id="reservation-date" name="reservation-date" required>

                <label for="reservation-time"><?php echo $lang['reservation_time_label']; ?></label>
                <input type="time" id="reservation-time" name="reservation-time" required>

                <button type="submit"><?php echo $lang['reservation_button']; ?></button>
            </form>
        </section>
        
        <!--  Sección "Contact" -->

        <section id="contact">
        <h2><?php echo $lang['contact_title']; ?></h2>
        <p><?php echo $lang['contact_description']; ?></p>

        <form class="contact-form">
            <label for="name"><?php echo $lang['contact_name_label']; ?></label>
            <input type="text" id="name" name="name" required>

            <label for="email"><?php echo $lang['contact_email_label']; ?></label>
            <input type="email" id="email" name="email" required>

            <label for="message"><?php echo $lang['contact_message_label']; ?></label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit"><?php echo $lang['contact_button']; ?></button>
        </form>
        </section>
        
        <footer class="footer">
    <div class="container">
        <!-- Enlaces a secciones -->
        <div class="footer-links">
            <a href="#" class="active"><?php echo $lang['home']; ?></a>
            <a href="#amenities"><?php echo $lang['amenities']; ?></a>
            <a href="#contact"><?php echo $lang['contact']; ?></a>
            <a href="#reviews"><?php echo $lang['reviews']; ?></a>
            <a href="#reservation"><?php echo $lang['reservation']; ?></a>
            <a href="#gallery"><?php echo $lang['gallery']; ?></a>
            <a href="./build/signup.html"><?php echo $lang['account']; ?></a>
        </div>

        <!-- Redes sociales -->
        <div class="social-media">
            <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://www.whatsapp.com" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>

        <!-- Derechos de autor -->
        <div class="copyright">
            &copy; 2024 <a href="https://rayitodesol.es"><?php echo $lang['site_name']; ?></a>. <?php echo $lang['rights']; ?>
        </div>
    </div>
</footer>
        

    </div>

</body>
</html>