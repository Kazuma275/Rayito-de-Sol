<template>
  <div class="booking-card">
    <div class="booking-header">
      <div class="booking-property-info">
        <img :src="getPropertyById(booking.propertyId).image" alt="Property" class="booking-property-image" />
        <div>
          <h3>{{ getPropertyById(booking.propertyId).name }}</h3>
          <div class="booking-id">Reserva #{{ booking.id }}</div>
        </div>
      </div>
      <div class="booking-status" :class="booking.status">
        {{ booking.statusText }}
      </div>
    </div>
    <div class="booking-details">
      <div class="booking-dates">
        <div class="date-item">
          <CalendarIcon class="date-icon" />
          <div>
            <span class="date-label">Llegada</span>
            <span class="date-value">{{ booking.checkIn }}</span>
          </div>
        </div>
        <div class="date-item">
          <CalendarIcon class="date-icon" />
          <div>
            <span class="date-label">Salida</span>
            <span class="date-value">{{ booking.checkOut }}</span>
          </div>
        </div>
      </div>
      <div class="booking-guest-info">
        <UserIcon class="guest-icon" />
        <div>
          <span class="guest-label">Huésped</span>
          <span class="guest-value">{{ booking.guestName }}</span>
        </div>
      </div>
      <div class="booking-guests">
        <UsersIcon class="guests-icon" />
        <div>
          <span class="guests-label">Huéspedes</span>
          <span class="guests-value">{{ booking.guests }}</span>
        </div>
      </div>
      <div class="booking-price">
        <EuroIcon class="price-icon" />
        <div>
          <span class="price-label">Total</span>
          <span class="price-value">€{{ booking.total }}</span>
        </div>
      </div>
    </div>
    <div class="booking-actions">
      <button class="action-button view" @click="$emit('view-details')">Ver detalles</button>
      <button v-if="booking.status === 'pending'" class="action-button accept" @click="$emit('accept')">Aceptar</button>
      <button v-if="booking.status === 'pending'" class="action-button reject" @click="$emit('reject')">Rechazar</button>
      <button v-if="booking.status === 'confirmed'" class="action-button message" @click="$emit('message')">
        <MessageSquareIcon class="action-icon" />
        Mensaje
      </button>
    </div>
  </div>
</template>

<script setup>
import { CalendarIcon, UserIcon, UsersIcon, EuroIcon, MessageSquareIcon } from 'lucide-vue-next'
defineProps({
  booking: Object,
  getPropertyById: Function
})
defineEmits(['view-details', 'accept', 'reject', 'message'])
</script>