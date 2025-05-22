<template>
  <div class="bulk-actions">
    <div class="bulk-header">
      <h4>Acciones en bloque</h4>
      <button v-if="isSelecting" class="cancel-selection" @click="$emit('cancel')">
        <XIcon class="icon" />
        Cancelar selección
      </button>
    </div>
    <div v-if="!isSelecting" class="selection-prompt">
      <p>Haz clic en una fecha para comenzar a seleccionar un rango</p>
    </div>
    <div v-else class="selection-info">
      <div class="date-range-display">
        <CalendarIcon class="icon" />
        <span>{{ formatDateRange(selectionStart, selectionEnd) }}</span>
      </div>
      <div class="action-buttons">
        <button class="action-button available" @click="$emit('available')">
          <CheckIcon class="icon" />
          Marcar como disponible
        </button>
        <button class="action-button unavailable" @click="$emit('unavailable')">
          <XIcon class="icon" />
          Marcar como no disponible
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { XIcon, CalendarIcon, CheckIcon } from 'lucide-vue-next'
defineProps({
  isSelecting: Boolean,
  selectionStart: [String, Date],
  selectionEnd: [String, Date],
  formatDateRange: Function
})
defineEmits(['cancel', 'available', 'unavailable'])
</script>