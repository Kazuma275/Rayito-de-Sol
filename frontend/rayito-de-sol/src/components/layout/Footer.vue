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
          <li>
            <a href="#" @click.prevent="changeTab('dashboard')">Inicio</a>
          </li>
          <li>
            <a href="#" @click.prevent="changeTab('properties')">Propiedades</a>
          </li>
          <li>
            <a href="#" @click.prevent="changeTab('bookings')">Reservas</a>
          </li>
          <li>
            <a href="#" @click.prevent="changeTab('calendar')">Calendario</a>
          </li>
        </ul>
      </div>

      <div class="footer-section">
        <h4 class="footer-title">Soporte</h4>
        <ul class="footer-links">
          <li>
            <router-link to="/manage/help" @click.prevent="changeTab('help')">
              Centro de Ayuda
            </router-link>
          </li>
          <li>
            <a href="#" @click.prevent="changeTab('help')"> Contacto </a>
          </li>
          <li>
            <router-link to="/manage/help" @click.prevent="changeTab('help')">
              Términos y condiciones
            </router-link>
          </li>
          <li>
            <a href="#" @click.prevent="changeTab('help')">
              Política de privacidad
            </a>
          </li>
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
        <p class="copyright">
          © 2023 Rayito de Sol. Todos los derechos reservados.
        </p>
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
import {
  SunIcon,
  MapPinIcon,
  PhoneIcon,
  MailIcon,
  FacebookIcon,
  TwitterIcon,
  InstagramIcon,
} from "lucide-vue-next";

const emit = defineEmits(["changeTab"]);

const changeTab = (tab) => {
  emit("changeTab", tab);
};
</script>

<style scoped>
.footer {
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  padding-top: 3rem;
  position: relative;
  overflow: hidden;
}

.footer::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(
      circle at 20% 150%,
      rgba(0, 113, 194, 0.4) 0%,
      transparent 50%
    ),
    radial-gradient(
      circle at 80% -50%,
      rgba(0, 53, 128, 0.3) 0%,
      transparent 60%
    );
  z-index: 0;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  position: relative;
  z-index: 1;
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
  width: 28px;
  height: 28px;
  color: #feba02;
  filter: drop-shadow(0 0 8px rgba(254, 186, 2, 0.6));
  margin-right: 0.75rem;
  animation: pulse 3s infinite ease-in-out;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.footer-description {
  color: rgba(255, 255, 255, 0.7);
  max-width: 300px;
  line-height: 1.6;
}

.footer-title {
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
  color: white;
  position: relative;
  padding-bottom: 0.5rem;
}

.footer-title::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 40px;
  height: 2px;
  background-color: #feba02;
}

.footer-links,
.footer-contact {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li,
.footer-contact li {
  margin-bottom: 0.75rem;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: all 0.3s ease;
  position: relative;
  padding-left: 0;
}

.footer-links a::before {
  content: "›";
  position: absolute;
  left: -12px;
  top: 0;
  opacity: 0;
  transition: all 0.3s ease;
}

.footer-links a:hover {
  color: white;
  padding-left: 12px;
}

.footer-links a:hover::before {
  opacity: 1;
  left: 0;
}

.footer-contact li {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.7);
  transition: color 0.3s ease;
}

.footer-contact li:hover {
  color: white;
}

.contact-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.75rem;
  color: #feba02;
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 1.5rem 0;
  position: relative;
  z-index: 1;
}

.footer-bottom .footer-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.copyright {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
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
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.social-link::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    135deg,
    rgba(254, 186, 2, 0.8) 0%,
    rgba(254, 186, 2, 0.4) 100%
  );
  opacity: 0;
  transition: opacity 0.3s ease;
}

.social-link:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.social-link:hover::before {
  opacity: 1;
}

.social-icon {
  width: 18px;
  height: 18px;
  color: white;
  position: relative;
  z-index: 1;
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