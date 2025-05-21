<template>
  <div class="search-container">
    <h1 class="search-title">Buscar Alojamiento</h1>
    
    <div class="search-form">
      <div class="search-input-group">
        <label for="location">Destino</label>
        <div class="input-with-icon">
          <MapPinIcon class="input-icon" />
          <input type="text" id="location" placeholder="¿A dónde vas?" class="search-input" />
        </div>
      </div>
      
      <div class="search-input-group">
        <label for="dates">Fechas</label>
        <div class="input-with-icon">
          <CalendarIcon class="input-icon" />
          <input type="text" id="dates" placeholder="Entrada - Salida" class="search-input" />
        </div>
      </div>
      
      <div class="search-input-group">
        <label for="guests">Huéspedes</label>
        <div class="input-with-icon">
          <UsersIcon class="input-icon" />
          <input type="number" id="guests" placeholder="Número de huéspedes" class="search-input" min="1" />
        </div>
      </div>
      
      <button class="search-button">
        <SearchIcon class="button-icon" />
        Buscar
      </button>
    </div>
    
    <div class="search-results">
      <p class="results-info">Mostrando resultados para tu búsqueda...</p>
      
      <div class="results-grid">
        <!-- Aquí irían los resultados de la búsqueda -->
        <div class="result-placeholder" v-for="i in 6" :key="i">
          <div class="result-image"></div>
          <div class="result-details">
            <div class="result-name"></div>
            <div class="result-location"></div>
            <div class="result-price"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { MapPinIcon, CalendarIcon, UsersIcon, SearchIcon } from 'lucide-vue-next';
</script>

<style scoped>
.search-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.search-title {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin-bottom: 2rem;
}

.search-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  background-color: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.search-input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.search-input-group label {
  font-weight: 500;
  color: #333;
}

.input-with-icon {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  color: #666;
}

.search-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color, #003580);
}

.search-button {
  background-color: var(--primary-color, #003580);
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  align-self: flex-end;
}

.search-button:hover {
  background-color: var(--primary-light, #0071c2);
}

.button-icon {
  width: 18px;
  height: 18px;
}

.search-results {
  margin-top: 2rem;
}

.results-info {
  margin-bottom: 1.5rem;
  color: #666;
}

.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.result-placeholder {
  background-color: #f5f5f5;
  border-radius: 8px;
  overflow: hidden;
  height: 300px;
  animation: pulse 1.5s infinite alternate;
}

.result-image {
  height: 180px;
  background-color: #eee;
}

.result-details {
  padding: 1rem;
}

.result-name {
  height: 24px;
  background-color: #eee;
  margin-bottom: 0.75rem;
  width: 80%;
  border-radius: 4px;
}

.result-location {
  height: 16px;
  background-color: #eee;
  margin-bottom: 0.75rem;
  width: 60%;
  border-radius: 4px;
}

.result-price {
  height: 24px;
  background-color: #eee;
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
  .search-container {
    padding: 1rem;
  }
  
  .search-form {
    grid-template-columns: 1fr;
  }
}
</style>
