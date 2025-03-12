<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section">
        <div class="footer-logo">
          <SunIcon class="logo-icon" />
          <span class="logo-text">Rayito de Sol</span>
        </div>
        <p class="footer-description">
          Plataforma de gestión de propiedades para anfitriones.
        </p>
      </div>
      
      <div class="footer-section">
        <h4 class="footer-title">Enlaces</h4>
        <ul class="footer-links">
          <li><a href="#" @click.prevent="changeTab('dashboard')">Inicio</a></li>
          <li><a href="#" @click.prevent="changeTab('properties')">Propiedades</a></li>
          <li><a href="#" @click.prevent="changeTab('bookings')">Reservas</a></li>
          <li><a href="#" @click.prevent="changeTab('calendar')">Calendario</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4 class="footer-title">Soporte</h4>
        <ul class="footer-links">
          <li><a href="#" @click.prevent="changeTab('help')">Centro de ayuda</a></li>
          <li><a href="#" @click.prevent="changeTab('help')">Contacto</a></li>
          <li><a href="#" @click.prevent="changeTab('settings')">Términos y condiciones</a></li>
          <li><a href="#" @click.prevent="changeTab('settings')">Política de privacidad</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4 class="footer-title">Contacto</h4>
        <ul class="footer-contact">
          <li>
            <MapPinIcon class="contact-icon" />
            <span>Calle Principal 123, Madrid</span>
          </li>
          <li>
            <PhoneIcon class="contact-icon" />
            <span>+34 912 345 678</span>
          </li>
          <li>
            <MailIcon class="contact-icon" />
            <span>info@rayitodesol.com</span>
          </li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      <div class="footer-container">
        <p class="copyright">© 2023 Rayito de Sol. Todos los derechos reservados.</p>
        <div class="social-links">
          <a href="#" class="social-link">
            <FacebookIcon class="social-icon" />
          </a>
          <a href="#" class="social-link">
            <TwitterIcon class="social-icon" />
          </a>
          <a href="#" class="social-link">
            <InstagramIcon class="social-icon" />
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { SunIcon, MapPinIcon, PhoneIcon, MailIcon, FacebookIcon, TwitterIcon, InstagramIcon } from 'lucide-vue-next';

const emit = defineEmits(['changeTab']);

const changeTab = (tab) => {
  emit('changeTab', tab);
};
</script>

<style scoped>
.footer {
  background-color: var(--primary-color);
  color: white;
  padding-top: 3rem;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  max-width: 1280px; /* Increased from previous value */
  margin: 0 auto;
  padding: 0 2rem;
}

.footer-section {
  flex: 1;
  min-width: 250px;
  margin-bottom: 2rem;
}

.footer-logo {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.logo-icon {
  width: 24px;
  height: 24px;
  color: var(--secondary-color);
  margin-right: 0.5rem;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
}

.footer-description {
  color: rgba(255, 255, 255, 0.7);
  max-width: 300px;
}

.footer-title {
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}

.footer-links, .footer-contact {
  list-style: none;
}

.footer-links li, .footer-contact li {
  margin-bottom: 0.75rem;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: white;
}

.footer-contact li {
  display: flex;
  align-items: center;
}

.contact-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.75rem;
  color: var(--secondary-color);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 1.5rem 0;
}

.footer-bottom .footer-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.copyright {
  color: rgba(255, 255, 255, 0.7);
}

.social-links {
  display: flex;
  gap: 1rem;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.1);
  transition: background-color 0.3s;
}

.social-link:hover {
  background-color: var(--secondary-color);
}

.social-icon {
  width: 18px;
  height: 18px;
  color: white;
}

@media (max-width: 768px) {
  .footer-section {
    flex: 0 0 50%;
  }
  
  .footer-bottom .footer-container {
    flex-direction: column;
    gap: 1rem;
  }
}

@media (max-width: 576px) {
  .footer-section {
    flex: 0 0 100%;
  }
  
  .footer-container {
    padding: 0 1rem;
  }
}
</style>