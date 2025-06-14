<template>
  <div class="bookings-filter">
    <div class="filter-tabs">
      <button 
        v-for="filter in filters" 
        :key="filter.id" 
        class="filter-tab" 
        :class="{ active: activeFilter === filter.id }"
        @click="$emit('change-filter', filter.id)"
      >
        {{ filter.name }}
      </button>
    </div>
    <div class="filter-property">
      <label>Filtrar por propiedad:</label>
      <select
        v-model="localProperty"
        class="property-select"
        @change="$emit('change-property', localProperty)"
      >
        <option value="all">Todas las propiedades</option>
        <option v-for="property in properties" :key="property.id" :value="property.id">
          {{ property.name }}
        </option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
const props = defineProps({
  filters: Array,
  activeFilter: String,
  properties: Array,
  selectedProperty: String
})
const localProperty = ref(props.selectedProperty)
watch(
  () => props.selectedProperty,
  (val) => { localProperty.value = val }
)
</script>