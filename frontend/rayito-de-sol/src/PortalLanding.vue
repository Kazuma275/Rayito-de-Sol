<template>
  <div class="portal-container">
    <div class="portal-content">
      <div class="logo-container">
        <SunIcon class="logo-icon" />
        <h1 class="logo-text">Rayito de Sol</h1>
      </div>
      
      <div class="portal-options">
        <div class="portal-option website" @click="goToWebsite">
          <HomeIcon class="option-icon" />
          <h2>Sitio Web</h2>
          <p>Visita nuestro sitio web para conocer nuestras propiedades y servicios</p>
          <button class="option-button">Visitar Sitio Web</button>
        </div>
        
        <div class="portal-option portal" @click="goToPortal">
          <BuildingIcon class="option-icon" />
          <h2>Portal de Propietarios</h2>
          <p>Accede al portal para gestionar tus propiedades y reservas</p>
          <button class="option-button">Acceder al Portal</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { 
  SunIcon, 
  HomeIcon, 
  BuildingIcon
} from 'lucide-vue-next';

const router = useRouter();

// Navigation functions
const goToWebsite = () => {
  window.location.href = 'https://rayitodesol.es';
};

const goToPortal = () => {
  // Navigate directly to the main page
  router.push('/login');
};
</script>

<style scoped>
.portal-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
}

.portal-content {
  flex: 1;
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 4rem 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 3rem;
}

.logo-icon {
  width: 48px;
  height: 48px;
  color: var(--secondary-color, #feba02);
  margin-right: 1rem;
}

.logo-text {
  font-size: 3rem;
  font-weight: 700;
  color: var(--primary-color, #003580);
  margin: 0;
}

.portal-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  width: 100%;
}

.portal-option {
  background-color: white;
  border-radius: 12px;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.portal-option:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.option-icon {
  width: 64px;
  height: 64px;
  color: var(--primary-color, #003580);
  margin-bottom: 1.5rem;
}

.portal-option h2 {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin: 0 0 1rem;
}

.portal-option p {
  color: #666;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.option-button {
  background-color: var(--primary-color, #003580);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.option-button:hover {
  background-color: var(--primary-light, #0071c2);
}

@media (max-width: 768px) {
  .logo-text {
    font-size: 2rem;
  }
  
  .portal-content {
    padding: 2rem 1rem;
  }
}

@media (max-width: 576px) {
  .logo-container {
    margin-bottom: 2rem;
  }
  
  .logo-icon {
    width: 36px;
    height: 36px;
  }
  
  .logo-text {
    font-size: 1.5rem;
  }
  
  .portal-option {
    padding: 1.5rem;
  }
  
  .option-icon {
    width: 48px;
    height: 48px;
    margin-bottom: 1rem;
  }
  
  .portal-option h2 {
    font-size: 1.5rem;
  }
}
</style>
