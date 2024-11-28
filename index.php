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
                <p>The little bit of Sun you need.</p>
            </div>
        </section>
        

        <section id="amenities" class="intro-section">
            <div class="container">
                <h2>Welcome to Our Apartment</h2>
                <p>Enjoy an unforgettable stay in our cozy apartment with sea views. Perfect for a relaxing vacation with all the comforts you need.</p>
                
                <div class="services-container">
                    <!-- Main services of the apartment -->
                    <div class="service-card">
                        <h3>Bedroom</h3>
                        <p>Double bed with two nightstands, sea views, and a spacious wardrobe for your comfort.</p>
                    </div>
        
                    <div class="service-card">
                        <h3>Bathroom</h3>
                        <p>Private bathroom with shower and toilet, designed for your comfort and relaxation.</p>
                    </div>
        
                    <div class="service-card">
                        <h3>Lounge and Kitchen</h3>
                        <p>Spacious lounge with sofa bed, table, and a fully equipped kitchen with countertop, refrigerator, freezer, and pantry.</p>
                    </div>
        
                    <div class="service-card">
                        <h3>Sea Views</h3>
                        <p>Enjoy stunning sea views from the lounge and bedroom, perfect for relaxing.</p>
                    </div>
        
                    <div class="service-card">
                        <h3>Washing Machine and Dryer</h3>
                        <p>Equipped with a washing machine and dryer to keep your clothes fresh and clean during your stay.</p>
                    </div>
        
                    <div class="service-card">
                        <h3>Kitchen Extras</h3>
                        <p>Coffee maker with free coffee and chocolate, drawers with utensils, and a pantry for your shopping.</p>
                    </div>
        
                    <div class="service-card">
                        <h3>Electronic Lock</h3>
                        <p>Door with an electronic lock for secure and hassle-free entry.</p>
                    </div>
        
                    <div class="service-card">
                        <h3>Other Amenities</h3>
                        <p>Vacuum cleaner for quick and easy cleaning, and other essential accessories.</p>
                    </div>

                    <div class="service-card">
                        <h3>Public Pool</h3>
                        <p>Access to a nearby public pool, perfect for a refreshing swim on sunny days.</p>
                    </div>
                    
                    <div class="service-card">
                        <h3>Free Wi-Fi</h3>
                        <p>Free Wi-Fi connection in the apartment so you can stay connected at all times.</p>
                    </div>
                    
                    <div class="service-card">
                        <h3>Restaurant</h3>
                        <p>Just 50 meters from the apartment, you'll find an excellent seafood restaurant, perfect for tasting local delights.</p>
                    </div>
                    
                    <div class="service-card">
                        <h3>Beach at Your Feet</h3>
                        <p>Enjoy the beach right at the doorstep of the apartment, with easy access for your moments of relaxation.</p>
                    </div>
                    
                </div>
            </div>
        </section>
        

        <!-- Gallery Section -->
        <section id="gallery">
            <h2>Gallery</h2>
            <!-- Thumbnail image gallery -->
            <div class="gallery-large">
                <img class="gallery-thumbnail" src="img/img-1.jpg" alt="Image 1">
                <img class="gallery-thumbnail" src="img/img-2.jpg" alt="Image 2">
                <img class="gallery-thumbnail" src="img/img-3.jpg" alt="Image 3">
                <img class="gallery-thumbnail" src="img/img-5.jpg" alt="Image 4">
                <img class="gallery-thumbnail" src="img/img-6.jpg" alt="Image 5">
                <img class="gallery-thumbnail" src="img/img-7.jpg" alt="Image 6">
                <img class="gallery-thumbnail" src="img/img-9.jpg" alt="Image 7">
                <img class="gallery-thumbnail" src="img/img-10.jpg" alt="Image 8">
                <img class="gallery-thumbnail" src="img/img-12.jpg" alt="Image 9">
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
            <h2>Customer Reviews</h2>
            
            <div class="reviews-container">
                <!-- Review 1 -->
                <div class="review-card">
                    <img src="https://media.istockphoto.com/id/494711330/es/foto/hombre-joven-latina-en-un-estudio.jpg?s=612x612&w=0&k=20&c=Ye9u097upgUhpcVttIB5i39Dj8XOPheoL4HB6SHC9iA=" alt="Photo of Laura" class="review-img">
                    <div class="review-content">
                        <p class="review-text">"The apartment is cozy and has everything you need. We enjoyed the sea views so much. We will definitely come back!"</p>
                        <p class="review-author">- Laura G.</p>
                    </div>
                </div>
    
                <!-- Review 2 -->
                <div class="review-card">
                    <img src="https://media.istockphoto.com/id/1311957094/es/foto/guapo-joven-sonriente-con-retrato-de-brazos-cruzados.jpg?s=612x612&w=0&k=20&c=x5LVA3-Y4WCfJmz6FzGTjXYv4tB1HPVkLuhLqcj8g6Q=" alt="Photo of Carlos" class="review-img">
                    <div class="review-content">
                        <p class="review-text">"Excellent location, just steps from the beach. The apartment was spotless and decorated with great taste. I highly recommend it."</p>
                        <p class="review-author">- Carlos M.</p>
                    </div>
                </div>
    
                <!-- Review 3 -->
                <div class="review-card">
                    <img src="https://img.freepik.com/fotos-premium/joven-alegre-brasil-camiseta-casual-vaqueros_941493-3125.jpg?w=360" alt="Photo of Ana" class="review-img">
                    <div class="review-content">
                        <p class="review-text">"Perfect for a relaxing vacation. The terrace has spectacular sunset views, and the place is very quiet. Highly recommended."</p>
                        <p class="review-author">- Ana P.</p>
                    </div>
                </div>
    
                <!-- Review 4 -->
                <div class="review-card">
                    <img src="https://media.gettyimages.com/id/1404193313/es/foto/man-walking.jpg?s=612x612&w=gi&k=20&c=3sSFQgcAb6wAtAGwI8SEG786gEP4ZSaMuJ-3H_J9rKQ=" alt="Photo of Javier" class="review-img">
                    <div class="review-content">
                        <p class="review-text">"The apartment was in perfect condition and very well equipped. The proximity to the beach and shops is ideal. I would definitely come back."</p>
                        <p class="review-author">- Javier R.</p>
                    </div>
                </div>
            </div>
        </div>
        </section>
        

        <!--  Sección "Reservation" -->

        <section id="reservation">
            <h2>Booking</h2>
            <p>To make a reservation, fill out the form below or contact us directly by email or phone.</p>
            
            <form class="reservation-form">
                <label for="reservation-date">Reservation Date</label>
                <input type="date" id="reservation-date" name="reservation-date" required>
        
                <label for="reservation-time">Reservation Time:</label>
                <input type="time" id="reservation-time" name="reservation-time" required>
        
                <button type="submit">Reserve</button>
            </form>
        </section>
        
        <!--  Sección "Contact" -->

        <section id="contact">
            <h2>Contact</h2>
            <p>For any questions or more information, please reach out to us at</p>
        
            <form class="contact-form">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
        
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
        
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
        
                <button type="submit">Send mensaje</button>
            </form>
        </section>
        
        <footer class="footer">
            <div class="container">
                <!-- Sección de enlaces a secciones -->
                <div class="footer-links">
                    <a href="#" class="active">Home</a>
                    <a href="#amenities">Amenities</a>
                    <a href="#contact">Contact</a>
                    <a href="#reviews">Reviews</a>
                    <a href="#reservation">Reserve</a>
                    <a href="#gallery">Gallery</a>
                    <a href="./build/signup.html">Account</a>
                </div>
                
                <!-- Sección de redes sociales -->
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.whatsapp.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-whatsapp"></i></a>
                </div>
                
                <!-- Derechos de autor -->
                <div class="copyright">
                    &copy; 2024 <a href="https://rayitodesol.es">Rayito de Sol</a>. All the<a href="https://terminosycondiciones.es">rights reserved</a>.
                </div>
            </div>
        </footer>
        

    </div>

</body>
</html>