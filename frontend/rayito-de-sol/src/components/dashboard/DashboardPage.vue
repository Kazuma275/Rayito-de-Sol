<template>
  <div>
    <h2 class="section-title">Dashboard</h2>
    
    <div class="dashboard-welcome">
      <div class="welcome-text">
        <h3>Bienvenido, {{ user.name }}</h3>
        <p>Aquí tienes un resumen de tus propiedades y reservas.</p>
      </div>
      <div class="welcome-actions">
        <button class="btn btn-primary">
          <PlusIcon class="btn-icon" />
          Añadir propiedad
        </button>
      </div>
    </div>
    
    <div class="dashboard-stats">
      <div class="stat-card">
        <HomeIcon class="stat-icon" />
        <div class="stat-info">
          <h4>Propiedades</h4>
          <p class="stat-value">{{ properties.length }}</p>
        </div>
      </div>
      
      <div class="stat-card">
        <CalendarIcon class="stat-icon" />
        <div class="stat-info">
          <h4>Reservas</h4>
          <p class="stat-value">{{ bookings.length }}</p>
        </div>
      </div>
      
      <div class="stat-card">
        <TrendingUpIcon class="stat-icon" />
        <div class="stat-info">
          <h4>Ocupación</h4>
          <p class="stat-value">{{ averageOccupancy }}%</p>
        </div>
      </div>
      
      <div class="stat-card">
        <DollarSignIcon class="stat-icon" />
        <div class="stat-info">
          <h4>Ingresos</h4>
          <p class="stat-value">{{ totalRevenue }}€</p>
        </div>
      </div>
    </div>
    
    <div class="dashboard-grid">
      <div class="dashboard-column">
        <UpcomingBookings :bookings="bookings" :properties="properties" />
      </div>
      
      <div class="dashboard-column">
        <RecentMessages :properties="properties" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { HomeIcon, CalendarIcon, TrendingUpIcon, DollarSignIcon, PlusIcon } from 'lucide-vue-next';
import UpcomingBookings from './UpcomingBookings.vue';
import RecentMessages from './RecentMessages.vue';

const props = defineProps({
  properties: {
    type: Array,
    required: true
  },
  bookings: {
    type: Array,
    required: true
  }
});

// Sample user data
const user = {
  name: 'Carlos Rodríguez',
  email: 'carlos@example.com'
};

// Calculate average occupancy
const averageOccupancy = computed(() => {
  if (props.properties.length === 0) return 0;
  
  const totalOccupancy = props.properties.reduce((sum, property) => {
    return sum + (property.occupancyRate || 0);
  }, 0);
  
  return Math.round(totalOccupancy / props.properties.length);
});

// Calculate total revenue
const totalRevenue = computed(() => {
  return props.bookings.reduce((sum, booking) => {
    return sum + (booking.totalAmount || 0);
  }, 0);
});
</script>

<style scoped>
.dashboard-welcome {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.welcome-text h3 {
  font-size: 1.5rem;
  color: #003580;
  margin-bottom: 0.5rem;
}

.welcome-text p {
  color: #666;
}

.btn-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 40px;
  height: 40px;
  color: #0071c2;
  margin-right: 1rem;
}

.stat-info h4 {
  font-size: 1rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: #003580;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

@media (max-width: 992px) {
  .dashboard-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .dashboard-welcome {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 576px) {
  .dashboard-stats {
    grid-template-columns: 1fr;
  }
}
</style>