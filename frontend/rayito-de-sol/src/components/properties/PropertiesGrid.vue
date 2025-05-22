<template>
  <div class="properties-grid">
    <PropertyCard
      v-for="property in properties"
      :key="property.id"
      :property="property"
      @edit="() => $emit('edit', property)"
      @calendar="() => $emit('calendar', property.id)"
    />
  </div>
</template>

<script setup>
import PropertyCard from './PropertyCard.vue'
defineProps({ properties: Array })
defineEmits(['edit', 'calendar'])
</script>