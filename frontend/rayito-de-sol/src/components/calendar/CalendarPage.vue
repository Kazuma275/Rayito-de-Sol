<template>
  <div>
    <div class="section-header">
      <h2 class="section-title">Calendario de Disponibilidad</h2>
      <div class="calendar-property-select">
        <label>Propiedad:</label>
        <select v-model="calendarPropertyId" class="property-select">
          <option v-for="property in properties" :key="property.id" :value="property.id">
            {{ property.name }}
          </option>
        </select>
      </div>
    </div>
    
    <CalendarView 
      :property-id="calendarPropertyId" 
    />
    
    <BulkActions />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import CalendarView from './CalendarView.vue';
import BulkActions from './BulkActions.vue';

const props = defineProps({
  properties: {
    type: Array,
    required: true
  }
});

const calendarPropertyId = ref(props.properties.length > 0 ? props.properties[0].id : null);
</script>

<style scoped>
.calendar-property-select {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.property-select {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.9rem;
}
</style>