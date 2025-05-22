<template>
  <div class="stats-grid">
    <div class="stat-card">
      <HomeIcon class="stat-icon" />
      <div class="stat-content">
        <h3 class="stat-title">Propiedades</h3>
        <p class="stat-value">{{ propertiesCount }}</p>
      </div>
    </div>
    <div class="stat-card">
      <CalendarIcon class="stat-icon" />
      <div class="stat-content">
        <h3 class="stat-title">Reservas Activas</h3>
        <p class="stat-value">{{ activeBookings }}</p>
      </div>
    </div>
    <div class="stat-card">
      <TrendingUpIcon class="stat-icon" />
      <div class="stat-content">
        <h3 class="stat-title">Tasa de Ocupación</h3>
        <p class="stat-value">{{ occupancyRate }}%</p>
      </div>
    </div>
    <div class="stat-card">
      <EuroIcon class="stat-icon" />
      <div class="stat-content">
        <h3 class="stat-title">Ingresos (30 días)</h3>
        <p class="stat-value">€{{ monthlyRevenue }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { HomeIcon, CalendarIcon, TrendingUpIcon, EuroIcon } from 'lucide-vue-next'

defineProps({
  propertiesCount: Number,
  activeBookings: Number,
  occupancyRate: Number,
  monthlyRevenue: Number
})
</script>