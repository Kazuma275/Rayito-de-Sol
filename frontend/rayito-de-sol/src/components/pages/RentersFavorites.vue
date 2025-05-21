<template>
  <div class="favorites-container">
    <h1 class="favorites-title">Mis Favoritos</h1>
    
    <div class="favorites-content">
      <div class="empty-state" v-if="!hasFavorites">
        <HeartIcon class="empty-icon" />
        <p>No tienes propiedades guardadas como favoritas</p>
        <a href="/renters/search" class="action-button">Buscar alojamiento</a>
      </div>
      
      <div v-else class="favorites-grid">
        <!-- Aquí irían las propiedades favoritas -->
        <div class="favorite-placeholder" v-for="i in 6" :key="i">
          <div class="favorite-image"></div>
          <div class="favorite-details">
            <div class="favorite-name"></div>
            <div class="favorite-location"></div>
            <div class="favorite-price"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { HeartIcon } from 'lucide-vue-next';

const hasFavorites = ref(false); // Cambiar a true cuando haya favoritos
</script>

<style scoped>
.favorites-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.favorites-title {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin-bottom: 2rem;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  text-align: center;
  color: #666;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #ccc;
  margin-bottom: 1rem;
}

.action-button {
  margin-top: 1rem;
  background-color: var(--primary-color, #003580);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.3s;
}

.action-button:hover {
  background-color: var(--primary-light, #0071c2);
}

.favorites-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.favorite-placeholder {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  height: 300px;
  animation: pulse 1.5s infinite alternate;
}

.favorite-image {
  height: 180px;
  background-color: #f5f5f5;
}

.favorite-details {
  padding: 1rem;
}

.favorite-name {
  height: 24px;
  background-color: #f5f5f5;
  margin-bottom: 0.75rem;
  width: 80%;
  border-radius: 4px;
}

.favorite-location {
  height: 16px;
  background-color: #f5f5f5;
  margin-bottom: 0.75rem;
  width: 60%;
  border-radius: 4px;
}

.favorite-price {
  height: 24px;
  background-color: #f5f5f5;
  width: 40%;
  border-radius: 4px;
}

@keyframes pulse {
  0% {
    opacity: 0.6;
  }
  100% {
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .favorites-container {
    padding: 1rem;
  }
}
</style>
