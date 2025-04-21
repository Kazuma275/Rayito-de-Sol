<template>
  <div class="dashboard-card">
    <div class="card-header">
      <h3>Próximas Reservas</h3>
      <button class="view-all-button">Ver todas</button>
    </div>
    <div v-if="bookings.length > 0" class="upcoming-bookings">
      <div v-for="(booking, index) in bookings" :key="index" class="booking-item">
        <div class="booking-property">
          <img :src="getPropertyById(booking.propertyId).image" alt="Property" class="booking-image" />
          <div>
            <h4>{{ getPropertyById(booking.propertyId).name }}</h4>
            <p class="booking-dates">{{ booking.checkIn }} - {{ booking.checkOut }}</p>
          </div>
        </div>
        <div class="booking-guest">
          <UserIcon class="guest-icon" />
          <div>
            <p class="guest-name">{{ booking.guestName }}</p>
            <p class="guest-info">{{ booking.guests }} huéspedes</p>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="empty-state">
      <CalendarOffIcon class="empty-icon" />
      <p>No hay reservas próximas</p>
    </div>
  </div>
</template>

<script setup>
import { UserIcon, CalendarOffIcon } from 'lucide-vue-next';

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

const getPropertyById = (id) => {
  return props.properties.find(property => property.id === id) || { 
    name: 'Propiedad no encontrada', 
    image: '/placeholder.svg?height=100&width=100' 
  };
};
</script>

<style scoped>
.dashboard-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.card-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.view-all-button {
  background: none;
  border: none;
  color: #0071c2;
  font-size: 0.9rem;
  cursor: pointer;
}

.upcoming-bookings {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.booking-item {
  display: flex;
  justify-content: space-between;
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.booking-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.booking-property {
  display: flex;
  align-items: center;
}

.booking-image {
  width: 50px;
  height: 50px;
  border-radius: 4px;
  object-fit: cover;
  margin-right: 0.75rem;
}

.booking-property h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.booking-dates {
  font-size: 0.8rem;
  color: #666;
}

.booking-guest {
  display: flex;
  align-items: center;
}

.guest-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.guest-name {
  font-size: 0.9rem;
  font-weight: 500;
  margin: 0 0 0.25rem;
}

.guest-info {
  font-size: 0.8rem;
  color: #666;
  margin: 0;
}

.empty-state {
  text-align: center;
  padding: 2rem 0;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #ccc;
  margin-bottom: 1rem;
}
</style>