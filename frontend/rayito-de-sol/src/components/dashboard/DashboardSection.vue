<template>
  <section class="dashboard-section">
    <h2 class="section-title">Panel de Control</h2>
    
    <!-- Grid de estadísticas -->
    <StatGrid
      :properties-count="properties.length"
      :active-bookings="activeBookings"
      :occupancy-rate="occupancyRate"
      :monthly-revenue="monthlyRevenue"
    />

    <div class="dashboard-grid">
      <!-- Próximas Reservas -->
      <UpcomingBookings
        :bookings="upcomingBookings"
        :get-property-by-id="getPropertyById"
        :format-date="formatDate"
      />
      <!-- Mensajes Recientes -->
      <RecentMessages
        :messages="recentMessages"
        :get-property-by-id="getPropertyById"
        :format-date-time="formatDateTime"
      />
    </div>
  </section>
</template>

<script setup>
import StatGrid from './StatGrid.vue'
import UpcomingBookings from './UpcomingBookings.vue'
import RecentMessages from './RecentMessages.vue'

// Props esperadas (pueden adaptarse a tu store/padre)
defineProps({
  properties: { type: Array, required: true },
  activeBookings: { type: Number, required: true },
  occupancyRate: { type: Number, required: true },
  monthlyRevenue: { type: Number, required: true },
  upcomingBookings: { type: Array, required: true },
  recentMessages: { type: Array, required: true },
  getPropertyById: { type: Function, required: true },
  formatDate: { type: Function, required: true },
  formatDateTime: { type: Function, required: true }
})
</script>