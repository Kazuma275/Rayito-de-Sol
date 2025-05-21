<template>
  <div class="bookings-container">
    <h1 class="bookings-title">Mis Reservas</h1>
    
    <div class="bookings-tabs">
      <button 
        v-for="tab in tabs" 
        :key="tab.id" 
        class="tab-button" 
        :class="{ active: activeTab === tab.id }"
        @click="activeTab = tab.id"
      >
        {{ tab.name }}
      </button>
    </div>
    
    <div class="bookings-list">
      <div class="empty-state" v-if="!hasBookings">
        <CalendarIcon class="empty-icon" />
        <p>No tienes reservas {{ activeTab === 'upcoming' ? 'próximas' : activeTab === 'past' ? 'pasadas' : '' }}</p>
        <a href="/renters/search" class="action-button">Buscar alojamiento</a>
      </div>
      
      <div v-else class="booking-cards">
        <!-- Aquí irían las tarjetas de reservas -->
        <div class="booking-placeholder" v-for="i in 3" :key="i">
          <div class="booking-header">
            <div class="booking-property"></div>
            <div class="booking-status"></div>
          </div>
          <div class="booking-details">
            <div class="booking-dates"></div>
            <div class="booking-guests"></div>
            <div class="booking-price"></div>
          </div>
          <div class="booking-actions"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { CalendarIcon } from 'lucide-vue-next';

const tabs = [
  { id: 'upcoming', name: 'Próximas' },
  { id: 'past', name: 'Pasadas' },
  { id: 'cancelled', name: 'Canceladas' }
];

const activeTab = ref('upcoming');
const hasBookings = ref(false); // Cambiar a true cuando haya reservas
</script>

<style scoped>
.bookings-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.bookings-title {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin-bottom: 2rem;
}

.bookings-tabs {
  display: flex;
  border-bottom: 1px solid #eee;
  margin-bottom: 2rem;
}

.tab-button {
  padding: 0.75rem 1.5rem;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  transition: all 0.3s;
}

.tab-button.active {
  color: var(--primary-color, #003580);
  border-bottom-color: var(--primary-color, #003580);
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

.booking-cards {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.booking-placeholder {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  animation: pulse 1.5s infinite alternate;
}

.booking-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.booking-property {
  height: 60px;
  width: 70%;
  background-color: #f5f5f5;
  border-radius: 4px;
}

.booking-status {
  height: 30px;
  width: 20%;
  background-color: #f5f5f5;
  border-radius: 20px;
}

.booking-details {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.booking-dates, .booking-guests, .booking-price {
  height: 50px;
  flex: 1;
  min-width: 150px;
  background-color: #f5f5f5;
  border-radius: 4px;
}

.booking-actions {
  height: 40px;
  background-color: #f5f5f5;
  border-radius: 4px;
  width: 100%;
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
  .bookings-container {
    padding: 1rem;
  }
  
  .bookings-tabs {
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 0.5rem;
  }
  
  .tab-button {
    padding: 0.75rem 1rem;
  }
}
</style>
