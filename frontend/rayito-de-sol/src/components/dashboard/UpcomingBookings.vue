<template>
  <div class="dashboard-card">
    <div class="card-header">
      <h3>Próximas Reservas</h3>
      <router-link to="/manage/bookings" class="view-all-button">Ver todas</router-link>
    </div>
    <div v-if="bookings.length > 0" class="upcoming-bookings">
      <div v-for="booking in bookings" :key="booking.id">
        <div class="booking-property">
          <img :src="getPropertyById(booking.property_id).image" alt="Property" class="booking-image" />
          <div>
            <h4>{{ getPropertyById(booking.property_id).name }}</h4>
            <p class="booking-dates">
              {{ formatDate(booking.check_in) }} - {{ formatDate(booking.check_out) }}
            </p>
          </div>
        </div>
        <div class="booking-guest">
          <UserIcon class="guest-icon" />
          <div>
            <p class="guest-name">{{ booking.guest_name }}</p>
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
import { UserIcon, CalendarOffIcon } from 'lucide-vue-next'

defineProps({
  bookings: Array,
  getPropertyById: Function,
  formatDate: Function
})
</script>