<template>
  <div class="portal-container">
    <div class="portal-inner">
      <!-- Logo -->
      <div class="logo">
        <div class="logo-icon-wrapper">
          <SunIcon class="logo-icon" />
          <div class="logo-icon-glow"></div>
        </div>
        <h1 class="logo-text">Rayito de Sol</h1>
      </div>

      <!-- Opciones -->
      <div class="options">
        <!-- Propietarios -->
        <div class="card property-card" @click="goToPortal" ref="propertyCard">
          <div class="card-content">
            <div class="icon-wrapper blue">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="svg-icon">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
              </svg>
              <div class="icon-glow blue-glow"></div>
            </div>
            <div class="card-text">
              <h2>Portal de Propietarios</h2>
              <p>Accede al portal para gestionar tus propiedades y reservas</p>
            </div>
            <button class="blue-button">
              <span>Acceder al Portal</span>
              <div class="button-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="arrow-icon">
                  <path d="m9 18 6-6-6-6"/>
                </svg>
              </div>
            </button>
          </div>
        </div>

        <!-- Inquilinos -->
        <div class="card renter-card" @click="goToRentersPortal" ref="renterCard">
          <div class="card-content">
            <div class="icon-wrapper yellow">
              <KeyIcon class="svg-icon" />
              <div class="icon-glow yellow-glow"></div>
            </div>
            <div class="card-text">
              <h2>Portal de Inquilinos</h2>
              <p>Busca y reserva apartamentos, gestiona tus reservas y pagos</p>
            </div>
            <button class="yellow-button">
              <span>Acceder como Inquilino</span>
              <div class="button-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="arrow-icon">
                  <path d="m9 18 6-6-6-6"/>
                </svg>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Sitio Web -->
      <div class="website" @click="goToWebsite" ref="websiteSection">
        <div class="website-content">
          <HomeIcon class="website-icon" />
          <h3>Visita nuestro Sitio Web</h3>
          <p>Descubre propiedades, servicios y más información</p>
          <button class="text-link">
            <span>Ir al Sitio Web</span>
            <div class="link-arrow">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="arrow-icon">
                <line x1="7" y1="17" x2="17" y2="7"></line>
                <polyline points="7 7 17 7 17 17"></polyline>
              </svg>
            </div>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        <div class="footer-content">
          <div class="footer-logo">
            <SunIcon class="footer-icon" />
          </div>
          <p class="footer-text">© {{ currentYear }} Rayito de Sol</p>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  SunIcon,
  HomeIcon,
  KeyIcon
} from 'lucide-vue-next'

const router = useRouter()
const propertyCard = ref<HTMLElement | null>(null)
const renterCard = ref<HTMLElement | null>(null)
const websiteSection = ref<HTMLElement | null>(null)
const currentYear = new Date().getFullYear()

const goToWebsite = () => {
  window.location.href = 'https://rayitodesol.es'
}

const goToPortal = () => {
  router.push('/login')
}

const goToRentersPortal = () => {
  router.push('/renters/login')
}

onMounted(() => {
  if (propertyCard.value && renterCard.value && websiteSection.value) {
    setTimeout(() => {
      if (propertyCard.value) propertyCard.value.classList.add('animate-in')
    }, 100)
    
    setTimeout(() => {
      if (renterCard.value) renterCard.value.classList.add('animate-in')
    }, 250)
    
    setTimeout(() => {
      if (websiteSection.value) websiteSection.value.classList.add('animate-in')
    }, 400)
  }
})
</script>

<style scoped>
/* Container styling */
.portal-container {
  min-height: 100vh;
  padding: 4rem 1rem;
  background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  position: relative;
  overflow: hidden;
}

.portal-container::before {
  content: '';
  position: absolute;
  width: 200%;
  height: 200%;
  top: -50%;
  left: -50%;
  background: radial-gradient(circle at center, rgba(59, 130, 246, 0.1) 0%, transparent 70%),
              radial-gradient(circle at 30% 70%, rgba(251, 191, 36, 0.1) 0%, transparent 60%);
  animation: rotate 30s linear infinite;
  z-index: 0;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.portal-inner {
  max-width: 1000px;
  margin: 0 auto;
  width: 100%;
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 3rem;
  position: relative;
  z-index: 1;
}

/* Logo styling */
.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.logo-icon-wrapper {
  position: relative;
  margin-right: 1rem;
}

.logo-icon {
  width: 48px;
  height: 48px;
  color: #facc15;
  filter: drop-shadow(0 0 8px rgba(250, 204, 21, 0.5));
  position: relative;
  z-index: 1;
  animation: pulse 3s infinite ease-in-out;
}

.logo-icon-glow {
  position: absolute;
  width: 48px;
  height: 48px;
  top: 0;
  left: 0;
  background: radial-gradient(circle, rgba(250, 204, 21, 0.3) 0%, rgba(250, 204, 21, 0) 70%);
  border-radius: 50%;
  animation: glow 3s infinite ease-in-out;
  z-index: 0;
}

.logo-text {
  font-size: 2.5rem;
  color: #1e3a8a;
  font-weight: 700;
  letter-spacing: -0.5px;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  position: relative;
}

.logo-text::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 100%;
  height: 2px;
  background: linear-gradient(90deg, transparent, #3b82f6, transparent);
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}

/* Card options styling */
.options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.card {
  height: 100%;
  border-radius: 16px;
  cursor: pointer;
  position: relative;
  transform: translateY(20px);
  opacity: 0;
  transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1);
  overflow: hidden;
}

.card.animate-in {
  transform: translateY(0);
  opacity: 1;
}

.card-content {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 16px;
  padding: 2.5rem 2rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.03),
    0 1px 3px rgba(0, 0, 0, 0.02);
  z-index: 1;
  border: 1px solid rgba(255, 255, 255, 0.6);
  position: relative;
  overflow: hidden;
}

.card-content::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
  transform: translateY(100%);
  transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

.card:hover .card-content::before {
  transform: translateY(0);
}

.card:hover .card-content {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 
    0 20px 25px rgba(0, 0, 0, 0.07),
    0 10px 10px rgba(0, 0, 0, 0.03);
}

.icon-wrapper {
  position: relative;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.card:hover .icon-wrapper {
  transform: scale(1.1) rotate(5deg);
}

.blue {
  background: linear-gradient(135deg, rgba(29, 78, 216, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
}

.yellow {
  background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(250, 204, 21, 0.1) 100%);
}

.icon-glow {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  z-index: -1;
  opacity: 0;
  transition: all 0.5s ease;
}

.blue-glow {
  background: radial-gradient(circle, rgba(29, 78, 216, 0.3) 0%, rgba(29, 78, 216, 0) 70%);
}

.yellow-glow {
  background: radial-gradient(circle, rgba(251, 191, 36, 0.3) 0%, rgba(251, 191, 36, 0) 70%);
}

.card:hover .icon-glow {
  opacity: 1;
  transform: scale(1.8);
  animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
  0%, 100% {
    transform: scale(1.5);
  }
  50% {
    transform: scale(1.8);
  }
}

.svg-icon {
  width: 56px;
  height: 56px;
  color: #1d4ed8;
  transition: all 0.3s ease;
}

.card:hover .svg-icon {
  transform: scale(1.1);
  filter: drop-shadow(0 0 8px rgba(29, 78, 216, 0.3));
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) scale(1.1);
  }
  50% {
    transform: translateY(-5px) scale(1.15);
  }
}

.yellow .svg-icon {
  color: #f59e0b;
}

.card:hover .yellow .svg-icon {
  filter: drop-shadow(0 0 8px rgba(251, 191, 36, 0.4));
}

.card-text {
  flex: 1;
  margin-bottom: 1.5rem;
  position: relative;
}

.card h2 {
  font-size: 1.5rem;
  color: #1e3a8a;
  font-weight: 600;
  margin-bottom: 1rem;
  letter-spacing: -0.5px;
  transition: transform 0.3s ease;
}

.card:hover h2 {
  transform: scale(1.05);
}

.card p {
  font-size: 1rem;
  line-height: 1.6;
  color: #64748b;
  margin-bottom: 0;
  transition: all 0.3s ease;
}

.card:hover p {
  color: #475569;
}

button {
  width: 100%;
  border: none;
  border-radius: 8px;
  padding: 0.875rem 1.5rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.blue-button {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
}

.yellow-button {
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
}

.blue-button::before,
.yellow-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transform: translateX(-100%);
  transition: transform 0s;
}

.blue-button:hover::before,
.yellow-button:hover::before {
  transform: translateX(100%);
  transition: transform 0.6s ease;
}

.blue-button:hover {
  background: linear-gradient(135deg, #1e40af 0%, #60a5fa 100%);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  transform: translateY(-2px);
}

.yellow-button:hover {
  background: linear-gradient(135deg, #f59e0b 0%, #fcd34d 100%);
  box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
  transform: translateY(-2px);
}

.button-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 8px;
  opacity: 0;
  width: 0;
  transition: all 0.3s ease;
}

button:hover .button-arrow {
  opacity: 1;
  width: 24px;
  animation: bounce 1s infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateX(0);
  }
  50% {
    transform: translateX(3px);
  }
}

.arrow-icon {
  width: 16px;
  height: 16px;
}

/* Website section styling */
.website {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1);
  cursor: pointer;
  margin-top: 0.5rem;
}

.website.animate-in {
  opacity: 0.85;
  transform: translateY(0);
}

.website-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.5rem;
  border-radius: 12px;
  transition: all 0.3s ease;
  position: relative;
}

.website:hover .website-content {
  background-color: rgba(255, 255, 255, 0.6);
  transform: translateY(-4px);
}

.website-icon {
  width: 40px;
  height: 40px;
  color: #94a3b8;
  margin-bottom: 0.75rem;
  transition: all 0.3s ease;
}

.website:hover .website-icon {
  color: #64748b;
  transform: scale(1.1) rotate(5deg);
  animation: float 2s ease-in-out infinite;
}

.website h3 {
  font-size: 1.2rem;
  color: #334155;
  font-weight: 600;
  margin-bottom: 0.5rem;
  transition: all 0.3s ease;
}

.website:hover h3 {
  color: #1e293b;
  transform: scale(1.05);
}

.website p {
  font-size: 0.95rem;
  color: #64748b;
  margin-bottom: 0.75rem;
  max-width: 320px;
  transition: color 0.3s ease;
}

.website:hover p {
  color: #475569;
}

.text-link {
  background: transparent;
  color: #3b82f6;
  font-size: 0.95rem;
  padding: 0.5rem 0.75rem;
  position: relative;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.text-link:hover {
  color: #1d4ed8;
  transform: translateY(-2px);
}

.link-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 4px;
  opacity: 0;
  width: 0;
  transition: all 0.3s ease;
}

.text-link:hover .link-arrow {
  opacity: 1;
  width: 16px;
  animation: diagonal-bounce 1s infinite;
}

@keyframes diagonal-bounce {
  0%, 100% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(2px, -2px);
  }
}

/* Footer styling */
.footer {
  background: transparent;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid rgba(148, 163, 184, 0.1);
  opacity: 0.7;
  transition: opacity 0.3s ease;
}

.footer:hover {
  opacity: 1;
}

.footer-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.footer-icon {
  width: 20px;
  height: 20px;
  color: #facc15;
  opacity: 0.7;
  transition: all 0.3s ease;
}

.footer:hover .footer-icon {
  opacity: 1;
  transform: rotate(180deg);
  filter: drop-shadow(0 0 4px rgba(250, 204, 21, 0.5));
}

.footer-text {
  font-size: 0.875rem;
  color: #64748b;
  transition: color 0.3s ease;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  opacity: 0;
  transform: translateY(10px);
  animation: fadeInUp 0.5s ease forwards 0.8s;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Animations */
@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

@keyframes glow {
  0%, 100% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.5);
    opacity: 0.8;
  }
}

/* Responsive design */
@media (max-width: 768px) {
  .portal-container {
    padding: 3rem 1rem;
  }
  
  .portal-inner {
    gap: 2rem;
  }
  
  .options {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .card-content {
    padding: 2rem 1.5rem;
  }
  
  .icon-wrapper {
    width: 72px;
    height: 72px;
    margin-bottom: 1.25rem;
  }
  
  .svg-icon {
    width: 48px;
    height: 48px;
  }
  
  .card h2 {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
  }
  
  .card p {
    font-size: 0.9rem;
  }
  
  .logo-icon {
    width: 40px;
    height: 40px;
  }
  
  .logo-text {
    font-size: 2rem;
  }

  .footer {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
  }

  .footer-text {
    font-size: 0.8rem;
  }
}
</style>