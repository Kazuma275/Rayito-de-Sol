<template>
  <div>
    <h2 class="section-title">Reservas</h2>
    
    <div class="bookings-filter">
      <div class="filter-tabs">
        <button 
          v-for="filter in bookingFilters" 
          :key="filter.id" 
          class="filter-tab" 
          :class="{ active: activeBookingFilter === filter.id }"
          @click="activeBookingFilter = filter.id"
        >
          {{ filter.name }}
        </button>
      </div>
      
      <div class="filter-property">
        <label>Filtrar por propiedad:</label>
        <select v-model="bookingPropertyFilter" class="property-select">
          <option value="all">Todas las propiedades</option>
          <option v-for="property in properties" :key="property.id" :value="property.id">
            {{ property.name }}
          </option>
        </select>
      </div>
    </div>
    
    <div v-if="filteredBookings.length > 0" class="bookings-list">
      <BookingCard 
        v-for="booking in filteredBookings" 
        :key="booking.id" 
        :booking="booking" 
        :property="getPropertyById(booking.propertyId)" 
      />
    </div>
    
    <div v-else class="empty-bookings">
      <CalendarOffIcon class="empty-icon" />
      <h3>No hay reservas {{ activeBookingFilter === 'all' ? '' : 'en este estado' }}</h3>
      <p v-if="activeBookingFilter !== 'all'">Prueba a seleccionar otro filtro</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { CalendarOffIcon } from 'lucide-vue-next';
import BookingCard from './BookingCard.vue';

const props = defineProps({
  bookings: {
    type: Array,
    required: true
  },
  properties: {
    type: Array,
    required: true
  }
});

const bookingFilters = [
  { id: 'all', name: 'Todas' },
  { id: 'pending', name: 'Pendientes' },
  { id: 'confirmed', name: 'Confirmadas' },
  { id: 'completed', name: 'Completadas' },
  { id: 'cancelled', name: 'Canceladas' }
];

const activeBookingFilter = ref('all');
const bookingPropertyFilter = ref('all');

const filteredBookings = computed(() => {
  let filtered = props.bookings;
  
  if (activeBookingFilter.value !== 'all') {
    filtered = filtered.filter(booking => booking.status === activeBookingFilter.value);
  }
  
  if (bookingPropertyFilter.value !== 'all') {
    filtered = filtered.filter(booking => booking.propertyId === parseInt(bookingPropertyFilter.value));
  }
  
  return filtered;
});

const getPropertyById = (id) => {
  return props.properties.find(property => property.id === id) || { 
    name: 'Propiedad no encontrada', 
    image: '/placeholder.svg?height=100&width=100' 
  };
};
</script>

<style scoped>
.bookings-filter {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.filter-tabs {
  display: flex;
  border: 1px solid #ccc;
  border-radius: 4px;
  overflow: hidden;
}

.filter-tab {
  background: none;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
  font-size: 0.9rem;
}

.filter-tab:not(:last-child) {
  border-right: 1px solid #ccc;
}

.filter-tab.active {
  background-color: #0071c2;
  color: white;
}

.filter-property {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.property-select {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.9rem;
}

.bookings-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.empty-bookings {
  text-align: center;
  padding: 3rem 0;
}

.empty-bookings .empty-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1rem;
}

.empty-bookings h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.empty-bookings p {
  color: #666;
}

@media (max-width: 768px) {
  .bookings-filter {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .filter-tabs {
    width: 100%;
    overflow-x: auto;
  }
}
</style>