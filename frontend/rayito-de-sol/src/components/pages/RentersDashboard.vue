<template>
  <div class="dashboard-container">
    <h1 class="dashboard-title">Bienvenido al Portal de Inquilinos</h1>
    
    <div class="dashboard-stats">
      <div class="stat-card">
        <CalendarIcon class="stat-icon" />
        <div class="stat-content">
          <h3 class="stat-title">Reservas Activas</h3>
          <p class="stat-value">2</p>
        </div>
      </div>
      
      <div class="stat-card">
        <HeartIcon class="stat-icon" />
        <div class="stat-content">
          <h3 class="stat-title">Favoritos</h3>
          <p class="stat-value">5</p>
        </div>
      </div>
      
      <div class="stat-card">
        <MessageSquareIcon class="stat-icon" />
        <div class="stat-content">
          <h3 class="stat-title">Mensajes</h3>
          <p class="stat-value">3</p>
        </div>
      </div>
    </div>
    
    <div class="dashboard-sections">
      <div class="dashboard-section">
        <div class="section-header">
          <h2 class="section-title">Próximas Reservas</h2>
          <a href="/renters/bookings" class="section-link">Ver todas</a>
        </div>
        
        <div class="section-content">
          <div class="empty-state">
            <CalendarIcon class="empty-icon" />
            <p>No tienes reservas próximas</p>
            <a href="/renters/search" class="action-button">Buscar alojamiento</a>
          </div>
        </div>
      </div>
      
      <div class="dashboard-section">
        <div class="section-header">
          <h2 class="section-title">Propiedades Recomendadas</h2>
          <a href="/renters/search" class="section-link">Ver más</a>
        </div>
        
        <div class="section-content">
          <div class="properties-grid">
            <!-- Aquí irían las propiedades recomendadas -->
            <div class="property-placeholder">
              <div class="property-image"></div>
              <div class="property-details">
                <div class="property-name"></div>
                <div class="property-location"></div>
                <div class="property-price"></div>
              </div>
            </div>
            
            <div class="property-placeholder">
              <div class="property-image"></div>
              <div class="property-details">
                <div class="property-name"></div>
                <div class="property-location"></div>
                <div class="property-price"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { CalendarIcon, HeartIcon, MessageSquareIcon } from 'lucide-vue-next';
</script>

<style scoped>
.dashboard-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.dashboard-title {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin-bottom: 2rem;
}

.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.stat-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
}

.stat-icon {
  width: 40px;
  height: 40px;
  color: var(--primary-color, #003580);
  margin-right: 1rem;
}

.stat-content {
  flex: 1;
}

.stat-title {
  font-size: 1rem;
  color: #666;
  margin: 0 0 0.5rem;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--primary-color, #003580);
  margin: 0;
}

.dashboard-sections {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

.dashboard-section {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.section-title {
  font-size: 1.2rem;
  color: var(--primary-color, #003580);
  margin: 0;
}

.section-link {
  color: var(--primary-color, #003580);
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
}

.section-link:hover {
  text-decoration: underline;
}

.section-content {
  padding: 1.5rem;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
  color: #666;
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

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.property-placeholder {
  background-color: #f5f5f5;
  border-radius: 8px;
  overflow: hidden;
  height: 300px;
  animation: pulse 1.5s infinite alternate;
}

.property-image {
  height: 180px;
  background-color: #eee;
}

.property-details {
  padding: 1rem;
}

.property-name {
  height: 24px;
  background-color: #eee;
  margin-bottom: 0.75rem;
  width: 80%;
  border-radius: 4px;
}

.property-location {
  height: 16px;
  background-color: #eee;
  margin-bottom: 0.75rem;
  width: 60%;
  border-radius: 4px;
}

.property-price {
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
  .dashboard-container {
    padding: 1rem;
  }
  
  .dashboard-stats {
    grid-template-columns: 1fr;
  }
}
</style>
