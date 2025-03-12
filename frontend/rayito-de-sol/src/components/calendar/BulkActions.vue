<template>
  <div class="bulk-actions">
    <h4>Acciones en bloque</h4>
    <div class="date-range">
      <div class="date-input">
        <label>Desde</label>
        <input type="date" v-model="bulkStartDate" class="form-input" />
      </div>
      <div class="date-input">
        <label>Hasta</label>
        <input type="date" v-model="bulkEndDate" class="form-input" />
      </div>
    </div>
    <div class="action-buttons">
      <button class="bulk-button available" @click="setBulkAvailability(true)">Marcar como disponible</button>
      <button class="bulk-button unavailable" @click="setBulkAvailability(false)">Marcar como no disponible</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const bulkStartDate = ref('');
const bulkEndDate = ref('');

const setBulkAvailability = (available) => {
  if (!bulkStartDate.value || !bulkEndDate.value) {
    alert('Por favor, selecciona un rango de fechas');
    return;
  }
  
  console.log(`Set availability to ${available} from ${bulkStartDate.value} to ${bulkEndDate.value}`);
  // This would update the availability for the selected date range
};
</script>

<style scoped>
.bulk-actions {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.bulk-actions h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.date-range {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.date-input {
  flex: 1;
}

.date-input label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.bulk-button {
  flex: 1;
  padding: 0.75rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
  border: none;
}

.bulk-button.available {
  background-color: #e6f7ee;
  color: #00703c;
}

.bulk-button.available:hover {
  background-color: #d1f0e0;
}

.bulk-button.unavailable {
  background-color: #fff2f0;
  color: #e41c00;
}

.bulk-button.unavailable:hover {
  background-color: #ffe5e2;
}

@media (max-width: 576px) {
  .date-range {
    flex-direction: column;
    gap: 1rem;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>