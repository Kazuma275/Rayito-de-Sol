<template>
  <div class="calendar-container">
    <div class="calendar-controls">
      <button class="calendar-nav" @click="previousMonth">
        <ChevronLeftIcon />
      </button>
      <h3 class="calendar-month">{{ currentMonthName }} {{ currentYear }}</h3>
      <button class="calendar-nav" @click="nextMonth">
        <ChevronRightIcon />
      </button>
    </div>
    
    <div class="calendar-grid">
      <div v-for="day in weekDays" :key="day" class="calendar-day header">{{ day }}</div>
      <div v-for="(day, index) in calendarDays" :key="index" 
           :class="['calendar-day', { 
             'empty': !day.date, 
             'available': day.available && day.date,
             'unavailable': !day.available && day.date,
             'booked': day.booked && day.date,
             'today': day.isToday
           }]"
           @click="day.date && toggleAvailability(day)">
        <span v-if="day.date" class="day-number">{{ day.date }}</span>
        <div v-if="day.date && day.booked" class="day-info">Reservado</div>
        <div v-else-if="day.date && !day.available" class="day-info">No disponible</div>
        <div v-else-if="day.date" class="day-info">Disponible</div>
      </div>
    </div>
    
    <div class="calendar-legend">
      <div class="legend-item">
        <div class="legend-color available"></div>
        <span>Disponible</span>
      </div>
      <div class="legend-item">
        <div class="legend-color unavailable"></div>
        <span>No disponible</span>
      </div>
      <div class="legend-item">
        <div class="legend-color booked"></div>
        <span>Reservado</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';

defineProps({
  propertyId: {
    type: Number,
    required: true
  }
});

const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());

const weekDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

const currentMonthName = computed(() => {
  const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
  return months[currentMonth.value];
});

const calendarDays = computed(() => {
  const days = [];
  const firstDay = new Date(currentYear.value, currentMonth.value, 1);
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);

  // Get the day of the week for the first day (0 = Sunday, 1 = Monday, etc.)
  let firstDayOfWeek = firstDay.getDay() - 1;
  if (firstDayOfWeek < 0) firstDayOfWeek = 6; // Adjust for Monday as first day

  // Add empty days for the beginning of the month
  for (let i = 0; i < firstDayOfWeek; i++) {
    days.push({ date: null });
  }

  // Add days of the month
  const today = new Date();
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const isToday = 
      today.getDate() === i && 
      today.getMonth() === currentMonth.value && 
      today.getFullYear() === currentYear.value;
    
    // For demo purposes, randomly determine availability and bookings
    const random = Math.random();
    const available = random > 0.3;
    const booked = random < 0.2;
    
    days.push({ 
      date: i, 
      available, 
      booked,
      isToday 
    });
  }

  return days;
});

const previousMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
};

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
};

const toggleAvailability = (day) => {
  if (day.booked) return; // Can't change availability for booked days
  day.available = !day.available;
};
</script>

<style scoped>
.calendar-container {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 1.5rem;
}

.calendar-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.calendar-month {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.calendar-nav {
  background: none;
  border: none;
  color: #0071c2;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s;
}

.calendar-nav:hover {
  background-color: #f5f5f5;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.calendar-day {
  text-align: center;
  padding: 0.75rem;
  border-radius: 4px;
  position: relative;
}

.calendar-day.header {
  font-weight: 600;
  color: #003580;
}

.calendar-day.empty {
  background: none;
}

.day-number {
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.day-info {
  font-size: 0.7rem;
}

.calendar-day.available {
  background-color: #e6f7ee;
  color: #00703c;
  cursor: pointer;
}

.calendar-day.unavailable {
  background-color: #fff2f0;
  color: #e41c00;
  cursor: pointer;
}

.calendar-day.booked {
  background-color: #e6f0ff;
  color: #0071c2;
}

.calendar-day.today {
  border: 2px solid #0071c2;
  font-weight: 600;
}

.calendar-legend {
  display: flex;
  gap: 1.5rem;
  margin-top: 1.5rem;
  justify-content: center;
}

.legend-item {
  display: flex;
  align-items: center;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
  margin-right: 0.5rem;
}

.legend-color.available {
  background-color: #e6f7ee;
}

.legend-color.unavailable {
  background-color: #fff2f0;
}

.legend-color.booked {
  background-color: #e6f0ff;
}
</style>