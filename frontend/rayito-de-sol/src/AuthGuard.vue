<template>
  <div v-if="authState.loading" class="auth-loading">
    <div class="auth-loading-content">
      <div class="logo-container">
        <div class="logo-icon-wrapper">
          <SunIcon class="logo-icon" />
          <div class="logo-icon-glow"></div>
        </div>
      </div>
      
      <div class="loader-container">
        <div class="loader-ring"></div>
        <div class="loader-ring"></div>
        <div class="loader-ring"></div>
      </div>
      
      <h2 class="loading-title">Verificando sesión</h2>
      <p class="loading-message">Estamos validando tu acceso</p>
      
      <div class="loading-progress">
        <div class="progress-bar">
          <div class="progress-fill"></div>
        </div>
      </div>
    </div>
    
    <div class="background-elements">
      <div class="bg-circle circle-1"></div>
      <div class="bg-circle circle-2"></div>
      <div class="bg-circle circle-3"></div>
      <div class="bg-line line-1"></div>
      <div class="bg-line line-2"></div>
      <div class="bg-line line-3"></div>
    </div>
  </div>
  <slot v-else-if="authState.isAuthenticated"></slot>
  <!-- Si no está autenticado, no renderiza nada porque el router ya redirigirá -->
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth, authState } from './router/auth-guard';
import axios from 'axios';
import { getItem } from "@/helpers/storage";
import { apiHeaders } from "@/../utils/api";
import { SunIcon } from 'lucide-vue-next';

const router = useRouter();
const { requireAuth } = useAuth();

onMounted(async () => {
  authState.loading = true;
  
  try {
    // Verifica si hay token
    if (!requireAuth()) {
      return; // El requireAuth ya redirige si es necesario
    }
    
    // Opcional: Verificar validez del token con el servidor
    const token = getItem("auth_token", true) || getItem("auth_token");
    
    if (token) {
      try {
        // Intenta obtener datos del usuario para verificar que el token es válido
        const { data } = await axios.get("http://localhost:8000/api/user", apiHeaders());
        authState.user = data;
        authState.isAuthenticated = true;
      } catch (error) {
        console.error("Error al verificar token:", error);
        // Si hay error, el token probablemente expiró
        authState.isAuthenticated = false;
        router.push('/login');
      }
    } else {
      authState.isAuthenticated = false;
      router.push('/login');
    }
  } finally {
    // Añadir un pequeño retraso para que se vea la animación
    setTimeout(() => {
      authState.loading = false;
    }, 1200);
  }
});
</script>

<style scoped>
.auth-loading {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0a2463 0%, #1e3a8a 100%);
  z-index: 9999;
  overflow: hidden;
}

.auth-loading-content {
  position: relative;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.8s ease-out forwards, float 6s ease-in-out infinite;
  max-width: 90%;
  width: 400px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.logo-container {
  margin-bottom: 2rem;
}

.logo-icon-wrapper {
  position: relative;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-icon {
  width: 60px;
  height: 60px;
  color: #facc15;
  filter: drop-shadow(0 0 15px rgba(250, 204, 21, 0.7));
  position: relative;
  z-index: 1;
  animation: pulse 3s infinite ease-in-out;
}

.logo-icon-glow {
  position: absolute;
  width: 80px;
  height: 80px;
  top: 0;
  left: 0;
  background: radial-gradient(circle, rgba(250, 204, 21, 0.6) 0%, rgba(250, 204, 21, 0) 70%);
  border-radius: 50%;
  animation: glow 3s infinite ease-in-out;
  z-index: 0;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

@keyframes glow {
  0%, 100% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.8);
    opacity: 0.8;
  }
}

.loader-container {
  position: relative;
  width: 80px;
  height: 80px;
  margin-bottom: 2rem;
}

.loader-ring {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 3px solid transparent;
  border-top-color: #3b82f6;
  animation: spin 1.5s linear infinite;
}

.loader-ring:nth-child(2) {
  width: 70%;
  height: 70%;
  top: 15%;
  left: 15%;
  border-top-color: #facc15;
  animation-duration: 2s;
  animation-direction: reverse;
}

.loader-ring:nth-child(3) {
  width: 40%;
  height: 40%;
  top: 30%;
  left: 30%;
  border-top-color: #ffffff;
  animation-duration: 1s;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.loading-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: white;
  margin: 0 0 0.5rem;
  text-align: center;
  background: linear-gradient(to right, #ffffff, #facc15);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% {
    background-position: -200% center;
  }
  100% {
    background-position: 200% center;
  }
}

.loading-message {
  color: rgba(255, 255, 255, 0.8);
  text-align: center;
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.loading-progress {
  width: 100%;
  margin-top: 1rem;
}

.progress-bar {
  height: 6px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
  overflow: hidden;
  position: relative;
}

.progress-fill {
  position: absolute;
  height: 100%;
  background: linear-gradient(to right, #3b82f6, #facc15);
  border-radius: 3px;
  width: 0;
  animation: fillProgress 2s ease-in-out infinite;
}

@keyframes fillProgress {
  0% {
    width: 0;
    left: 0;
  }
  50% {
    width: 100%;
    left: 0;
  }
  100% {
    width: 0;
    left: 100%;
  }
}

/* Elementos de fondo */
.background-elements {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
  z-index: 1;
}

.bg-circle {
  position: absolute;
  border-radius: 50%;
  opacity: 0.1;
}

.circle-1 {
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, #facc15 0%, transparent 70%);
  top: -100px;
  right: -100px;
  animation: floatCircle 15s infinite ease-in-out;
}

.circle-2 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
  bottom: -200px;
  left: -200px;
  animation: floatCircle 20s infinite ease-in-out reverse;
}

.circle-3 {
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, #ffffff 0%, transparent 70%);
  top: 40%;
  right: 10%;
  animation: floatCircle 12s infinite ease-in-out 1s;
}

@keyframes floatCircle {
  0%, 100% {
    transform: translate(0, 0);
  }
  25% {
    transform: translate(50px, 20px);
  }
  50% {
    transform: translate(0, 50px);
  }
  75% {
    transform: translate(-30px, 10px);
  }
}

.bg-line {
  position: absolute;
  height: 2px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  width: 100%;
  transform-origin: left;
}

.line-1 {
  top: 20%;
  transform: rotate(5deg);
  animation: moveLine 8s infinite ease-in-out;
}

.line-2 {
  top: 50%;
  transform: rotate(-3deg);
  animation: moveLine 12s infinite ease-in-out reverse;
}

.line-3 {
  top: 80%;
  transform: rotate(2deg);
  animation: moveLine 10s infinite ease-in-out 2s;
}

@keyframes moveLine {
  0%, 100% {
    opacity: 0.1;
    width: 90%;
  }
  50% {
    opacity: 0.3;
    width: 100%;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .auth-loading-content {
    padding: 2rem;
  }
  
  .logo-icon-wrapper {
    width: 60px;
    height: 60px;
  }
  
  .logo-icon {
    width: 40px;
    height: 40px;
  }
  
  .loader-container {
    width: 60px;
    height: 60px;
  }
  
  .loading-title {
    font-size: 1.5rem;
  }
  
  .loading-message {
    font-size: 1rem;
  }
}
</style>