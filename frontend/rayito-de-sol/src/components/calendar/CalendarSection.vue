<template>
  <section class="calendar-section">
    <div class="section-header">
      <h2 class="section-title">Calendario de Disponibilidad</h2>
      <div class="property-selector">
        <select :value="selectedPropertyId" class="property-select" @change="onChangeProperty">
          <option v-for="property in properties" :key="property.id" :value="property.id">
            {{ property.name }}
          </option>
        </select>
        <ChevronDownIcon class="select-icon" />
      </div>
    </div>
    
    <CalendarNavigation
      :current-month-name="currentMonthName"
      :current-year="currentYear"
      @prev="onPrevMonth"
      @next="onNextMonth"
    />
    
    <CalendarGrid
      :week-days="weekDays"
      :calendar-days="calendarDays"
      :current-month-key="currentMonthKey"
      :is-in-selection="isInSelection"
      @day-click="onDayClick"
      @day-hover="onDayHover"
    />
    
    <CalendarLegend />

    <CalendarBulkActions
      :is-selecting="isSelecting"
      :selection-start="selectionStart"
      :selection-end="selectionEnd"
      :format-date-range="formatDateRange"
      @cancel="onCancelSelection"
      @available="() => onBulkAction(true)"
      @unavailable="() => onBulkAction(false)"
    />
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ChevronDownIcon } from 'lucide-vue-next'
import CalendarNavigation from './CalendarNavigation.vue'
import CalendarGrid from './CalendarGrid.vue'
import CalendarLegend from './CalendarLegend.vue'
import CalendarBulkActions from './CalendarBulkActions.vue'

// Props
defineProps({
  properties: Array,
  selectedPropertyId: [String, Number],
  currentMonthName: String,
  currentYear: Number,
  weekDays: Array,
  calendarDays: Array,
  currentMonthKey: String,
  isInSelection: Function,
  isSelecting: Boolean,
  selectionStart: [String, Date],
  selectionEnd: [String, Date],
  formatDateRange: Function
})

// Emits
const emit = defineEmits([
  'change-property',
  'prev-month',
  'next-month',
  'day-click',
  'day-hover',
  'cancel-selection',
  'bulk-action'
])

function onChangeProperty(event) { emit('change-property', event.target.value) }
function onPrevMonth() { emit('prev-month') }
function onNextMonth() { emit('next-month') }
function onDayClick(day) { emit('day-click', day) }
function onDayHover(day) { emit('day-hover', day) }
function onCancelSelection() { emit('cancel-selection') }
function onBulkAction(available) { emit('bulk-action', available) }
</script>