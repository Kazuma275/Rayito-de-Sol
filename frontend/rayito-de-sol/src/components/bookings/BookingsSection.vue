<template>
  <section class="bookings-section">
    <h2 class="section-title">Reservas</h2>
    
    <BookingsFilter
      :filters="bookingFilters"
      :active-filter="activeBookingFilter"
      :properties="properties"
      :selected-property="bookingPropertyFilter"
      @change-filter="onChangeFilter"
      @change-property="onChangeProperty"
    />

    <BookingsList
      v-if="filteredBookings.length > 0"
      :bookings="filteredBookings"
      :get-property-by-id="getPropertyById"
      @view-details="onViewDetails"
      @accept="onAccept"
      @reject="onReject"
      @message="onMessage"
    />

    <div v-else class="empty-bookings">
      <CalendarOffIcon class="empty-icon" />
      <h3>No hay reservas {{ activeBookingFilter === 'all' ? '' : 'en este estado' }}</h3>
      <p v-if="activeBookingFilter !== 'all'">Prueba a seleccionar otro filtro</p>
    </div>
  </section>
</template>

<script setup>
import { CalendarOffIcon } from 'lucide-vue-next'
import BookingsFilter from './BookingsFilter.vue'
import BookingsList from './BookingsList.vue'

// Props
defineProps({
  bookingFilters: Array,
  activeBookingFilter: String,
  bookingPropertyFilter: String,
  properties: Array,
  filteredBookings: Array,
  getPropertyById: Function
})

// Emits
const emit = defineEmits([
  'change-filter',
  'change-property',
  'view-details',
  'accept',
  'reject',
  'message'
])

// Métodos para emitir eventos hacia el padre
function onChangeFilter(filterId) { emit('change-filter', filterId) }
function onChangeProperty(propertyId) { emit('change-property', propertyId) }
function onViewDetails(booking) { emit('view-details', booking) }
function onAccept(booking) { emit('accept', booking) }
function onReject(booking) { emit('reject', booking) }
function onMessage(booking) { emit('message', booking) }
</script>