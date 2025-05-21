<template>
  <div class="calendar-container">
    <div class="calendar-header">
      <button class="month-nav prev" @click="prevMonth">
        <ChevronLeftIcon class="nav-icon" />
      </button>
      <h3 class="current-month">{{ currentMonthName }} {{ currentYear }}</h3>
      <button class="month-nav next" @click="nextMonth">
        <ChevronRightIcon class="nav-icon" />
      </button>
    </div>
    
    <div class="calendar-grid">
      <div class="weekday-header" v-for="day in weekdays" :key="day">{{ day }}</div>
      
      <div 
        v-for="(day, index) in calendarDays" 
        :key="index"
        class="calendar-day"
        :class="{
          'other-month': !day.isCurrentMonth,
          'today': day.isToday,
          'selected': isSelected(day),
          'in-range': isInRange(day),
          'has-booking': hasBooking(day),
          'unavailable': !isAvailable(day),
          'disabled': !day.isSelectable
        }"
        @click="selectDate(day)"
      >
        <span class="day-number">{{ day.day }}</span>
        <div v-if="hasBooking(day)" class="booking-indicator"></div>
        <div v-if="day.isCurrentMonth && isAvailable(day)" class="price-indicator">
          {{ formatPrice(getDatePrice(day)) }}
        </div>
      </div>
    </div>
    
    <div v-if="selectionMode === 'range' && startDate && endDate" class="selection-summary">
      <div class="date-range">
        {{ formatDate(startDate) }} - {{ formatDate(endDate) }}
      </div>
      <div class="nights-count">
        {{ calculateNights() }} noches
      </div>
      <div class="price-total">
        Total: {{ formatPrice(calculateTotal()) }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';

const props = defineProps({
  // Available dates with prices
  availableDates: {
    type: Array,
    default: () => []
  },
  // Existing bookings
  bookings: {
    type: Array,
    default: () => []
  },
  // Selection mode: 'single' or 'range'
  selectionMode: {
    type: String,
    default: 'range'
  },
  // Minimum stay in nights (for range selection)
  minStay: {
    type: Number,
    default: 1
  },
  // Maximum stay in nights (for range selection)
  maxStay: {
    type: Number,
    default: 30
  },
  // Initial selected date (for single selection)
  initialDate: {
    type: Date,
    default: null
  },
  // Initial date range (for range selection)
  initialDateRange: {
    type: Object,
    default: () => ({ start: null, end: null })
  }
});

const emit = defineEmits(['dateSelected', 'rangeSelected']);

// State
const currentDate = ref(new Date());
const selectedDate = ref(props.initialDate);
const startDate = ref(props.initialDateRange.start);
const endDate = ref(props.initialDateRange.end);

// Weekdays
const weekdays = ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do'];

// Current month and year
const currentMonthName = computed(() => {
  const months = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ];
  return months[currentDate.value.getMonth()];
});

const currentYear = computed(() => {
  return currentDate.value.getFullYear();
});

// Calendar days
const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  
  // First day of the month
  const firstDay = new Date(year, month, 1);
  // Last day of the month
  const lastDay = new Date(year, month + 1, 0);
  
  // Get the day of the week for the first day (0 = Sunday, 1 = Monday, etc.)
  let firstDayOfWeek = firstDay.getDay();
  // Adjust for Monday as first day of week
  firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;
  
  const daysInMonth = lastDay.getDate();
  const daysInPrevMonth = new Date(year, month, 0).getDate();
  
  const days = [];
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  // Previous month days
  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const day = daysInPrevMonth - i;
    const date = new Date(year, month - 1, day);
    days.push({
      day,
      date,
      isCurrentMonth: false,
      isToday: isSameDay(date, today),
      isSelectable: isDateSelectable(date)
    });
  }
  
  // Current month days
  for (let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month, day);
    days.push({
      day,
      date,
      isCurrentMonth: true,
      isToday: isSameDay(date, today),
      isSelectable: isDateSelectable(date)
    });
  }
  
  // Next month days
  const remainingDays = 42 - days.length; // 6 rows * 7 days = 42
  for (let day = 1; day <= remainingDays; day++) {
    const date = new Date(year, month + 1, day);
    days.push({
      day,
      date,
      isCurrentMonth: false,
      isToday: isSameDay(date, today),
      isSelectable: isDateSelectable(date)
    });
  }
  
  return days;
});

// Navigation
const prevMonth = () => {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() - 1,
    1
  );
};

const nextMonth = () => {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() + 1,
    1
  );
};

// Date selection
const selectDate = (day) => {
  if (!day.isSelectable) return;
  
  if (props.selectionMode === 'single') {
    selectedDate.value = day.date;
    emit('dateSelected', day.date);
  } else {
    // Range selection
    if (!startDate.value || (startDate.value && endDate.value)) {
      // Start new selection
      startDate.value = day.date;
      endDate.value = null;
    } else {
      // Complete selection
      if (day.date < startDate.value) {
        // If selecting a date before start date, swap them
        endDate.value = startDate.value;
        startDate.value = day.date;
      } else {
        endDate.value = day.date;
      }
      
      // Check if range is valid
      const nights = calculateNights();
      if (nights < props.minStay) {
        alert(`La estancia mínima es de ${props.minStay} noches.`);
        endDate.value = null;
        return;
      }
      
      if (nights > props.maxStay) {
        alert(`La estancia máxima es de ${props.maxStay} noches.`);
        endDate.value = null;
        return;
      }
      
      // Check if all dates in range are available
      if (!isRangeAvailable()) {
        alert('Algunas fechas en el rango seleccionado no están disponibles.');
        endDate.value = null;
        return;
      }
      
      // Emit range selected event
      emit('rangeSelected', { start: startDate.value, end: endDate.value });
    }
  }
};

// Helper functions
const isSameDay = (date1, date2) => {
  if (!date1 || !date2) return false;
  return (
    date1.getFullYear() === date2.getFullYear() &&
    date1.getMonth() === date2.getMonth() &&
    date1.getDate() === date2.getDate()
  );
};

const isSelected = (day) => {
  if (props.selectionMode === 'single') {
    return selectedDate.value && isSameDay(day.date, selectedDate.value);
  } else {
    return startDate.value && isSameDay(day.date, startDate.value) || 
           endDate.value && isSameDay(day.date, endDate.value);
  }
};

const isInRange = (day) => {
  if (props.selectionMode !== 'range' || !startDate.value || !endDate.value) return false;
  
  return day.date > startDate.value && day.date < endDate.value;
};

const isDateSelectable = (date) => {
  // Can't select dates in the past
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  if (date < today) return false;
  
  // Check if date is available (not booked)
  return isAvailable({ date });
};

const hasBooking = (day) => {
  return props.bookings.some(booking => {
    const bookingStart = new Date(booking.checkIn);
    const bookingEnd = new Date(booking.checkOut);
    
    return day.date >= bookingStart && day.date < bookingEnd;
  });
};

const isAvailable = (day) => {
  // Check if date is in available dates
  if (props.availableDates.length === 0) {
    // If no available dates provided, check if it's not booked
    return !hasBooking(day);
  }
  
  return props.availableDates.some(availableDate => {
    const date = new Date(availableDate.date);
    return isSameDay(date, day.date);
  });
};

const isRangeAvailable = () => {
  if (!startDate.value || !endDate.value) return true;
  
  const start = new Date(startDate.value);
  const end = new Date(endDate.value);
  
  // Check each date in the range
  for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
    if (!isAvailable({ date: new Date(d) })) {
      return false;
    }
  }
  
  return true;
};

const getDatePrice = (day) => {
  if (!day.isCurrentMonth) return null;
  
  const availableDate = props.availableDates.find(d => {
    const date = new Date(d.date);
    return isSameDay(date, day.date);
  });
  
  return availableDate ? availableDate.price : null;
};

const calculateNights = () => {
  if (!startDate.value || !endDate.value) return 0;
  
  const start = new Date(startDate.value);
  const end = new Date(endDate.value);
  const diffTime = end.getTime() - start.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays;
};

const calculateTotal = () => {
  if (!startDate.value || !endDate.value) return 0;
  
  let total = 0;
  const start = new Date(startDate.value);
  const end = new Date(endDate.value);
  
  for (let d = new Date(start); d < end; d.setDate(d.getDate() + 1)) {
    const price = getDatePrice({ date: new Date(d) });
    if (price) {
      total += price;
    }
  }
  
  return total;
};

// Formatting
const formatDate = (date) => {
  if (!date) return '';
  
  const options = { day: 'numeric', month: 'short', year: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

const formatPrice = (price) => {
  if (price === null) return '';
  return `€${price}`;
};

// Watch for prop changes
watch(() => props.initialDate, (newValue) => {
  if (newValue) {
    selectedDate.value = newValue;
  }
});

watch(() => props.initialDateRange, (newValue) => {
  if (newValue.start) {
    startDate.value = newValue.start;
  }
  if (newValue.end) {
    endDate.value = newValue.end;
  }
});

// Initialize
onMounted(() => {
  // If initial dates are provided, navigate to that month
  if (props.selectionMode === 'single' && props.initialDate) {
    currentDate.value = new Date(props.initialDate);
  } else if (props.selectionMode === 'range' && props.initialDateRange.start) {
    currentDate.value = new Date(props.initialDateRange.start);
  }
});
</script>

<style scoped>
.calendar-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.calendar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.current-month {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--primary-color, #003580);
  margin: 0;
}

.month-nav {
  background: none;
  border: 1px solid #eee;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.month-nav:hover {
  background-color: #f5f5f5;
}

.nav-icon {
  width: 18px;
  height: 18px;
  color: #666;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.weekday-header {
  text-align: center;
  font-weight: 600;
  font-size: 0.8rem;
  color: #666;
  padding: 0.5rem 0;
}

.calendar-day {
  position: relative;
  height: 60px;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.calendar-day:hover:not(.disabled):not(.unavailable) {
  background-color: #f5f5f5;
}

.day-number {
  font-size: 1rem;
  font-weight: 500;
}

.other-month {
  opacity: 0.4;
}

.today {
  background-color: #f5f9ff;
}

.today .day-number {
  color: var(--primary-color, #003580);
  font-weight: 700;
}

.selected {
  background-color: var(--primary-color, #003580) !important;
  color: white;
}

.in-range {
  background-color: #e6f0ff;
}

.has-booking {
  background-color: #fff2f0;
  cursor: not-allowed;
}

.unavailable {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.booking-indicator {
  position: absolute;
  top: 5px;
  right: 5px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #e41c00;
}

.price-indicator {
  font-size: 0.7rem;
  color: #666;
  margin-top: 0.25rem;
}

.selection-summary {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #eee;
}

.date-range {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.nights-count {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.price-total {
  font-weight: 700;
  font-size: 1.1rem;
  color: var(--primary-color, #003580);
}

@media (max-width: 576px) {
  .calendar-container {
    padding: 1rem;
  }
  
  .calendar-day {
    height: 50px;
  }
  
  .day-number {
    font-size: 0.9rem;
  }
  
  .price-indicator {
    font-size: 0.6rem;
  }
}
</style>
