<template>
  <transition name="calendar-fade" mode="out-in">
    <div :key="currentMonthKey" class="calendar-container">
      <div class="weekdays-header">
        <div v-for="day in weekDays" :key="day" class="weekday">{{ day }}</div>
      </div>
      <div class="calendar-grid">
        <div 
          v-for="(day, index) in calendarDays" 
          :key="index" 
          :class="[
            'calendar-day', 
            { 
              'empty': !day.date, 
              'available': day.available && day.date,
              'unavailable': !day.available && day.date,
              'booked': day.booked && day.date,
              'today': day.isToday,
              'in-selection': isInSelection(day)
            }
          ]"
          @click="day.date && $emit('day-click', day)"
          @mouseenter="day.date && $emit('day-hover', day)"
        >
          <span v-if="day.date" class="day-number">{{ day.date }}</span>
          <transition name="status-fade">
            <div v-if="day.date" class="day-status">
              <span v-if="day.booked" class="status booked">Reservado</span>
              <span v-else-if="!day.available" class="status unavailable">No disponible</span>
              <span v-else class="status available">Disponible</span>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  weekDays: Array,
  calendarDays: Array,
  currentMonthKey: String,
  isInSelection: Function
})
defineEmits(['day-click', 'day-hover'])
</script>