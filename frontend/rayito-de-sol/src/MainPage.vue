<template>
  <div class="mt-4">

    <Welcome/>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { CalendarIcon, HomeIcon } from 'lucide-vue-next';
import Welcome from './components/views/Welcome.vue';

// Get router instance
const router = useRouter();

// State for hover effects
const hoverRental = ref(false);
const hoverMain = ref(false);

// Background image from the provided URL
const backgroundImage = 'https://sjc.microlink.io/-p-fwojbjDvRwpePN2Z_l13ojz0jiDJ0JuUgMOruCd9nTT0ju4uWBG4DHcKkOsBswI98iuf-0pgLjtPXh49xnQ.jpeg';

// Navigation functions
const goToRentalManagement = () => {
  router.push('/manage');
};

const goToMainSite = () => {
  window.location.href = 'https://rayitodesol.es';
};
</script>

<style scoped>
.landing-page {
  position: relative;
  height: 100vh;
  width: 100%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.bg-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  filter: brightness(0.7);
  z-index: -1;
}

.content-container {
  width: 100%;
  max-width: 1200px;
  padding: 2rem;
  z-index: 1;
  text-align: center;
}

.logo-container {
  margin-bottom: 4rem;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.site-title {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.site-subtitle {
  font-size: 1.5rem;
  font-weight: 300;
}

.options-container {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.option-card {
  background-color: rgba(255, 255, 255, 0.95);
  border-radius: 8px;
  padding: 2rem;
  width: 300px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.option-hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
}

.option-icon {
  background-color: #0071c2;
  width: 70px;
  height: 70px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.icon {
  color: white;
  width: 32px;
  height: 32px;
}

.option-card h2 {
  font-size: 1.5rem;
  margin-bottom: 0.75rem;
  color: #0071c2;
}

.option-card p {
  color: #333;
  margin-bottom: 1.5rem;
}

.option-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.option-button:hover {
  background-color: #005999;
}

@media (max-width: 768px) {
  .options-container {
    flex-direction: column;
    align-items: center;
  }
  
  .option-card {
    width: 100%;
    max-width: 300px;
  }
}
</style>

