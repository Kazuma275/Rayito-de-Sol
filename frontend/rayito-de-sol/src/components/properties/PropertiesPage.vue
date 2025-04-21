<template>
  <div>
    <div class="section-header">
      <h2 class="section-title">Mis Propiedades</h2>
      <button class="add-button" @click="showAddPropertyModal = true">
        <PlusIcon class="add-icon" />
        Añadir Propiedad
      </button>
    </div>
    
    <div v-if="properties.length > 0" class="properties-grid">
      <PropertyCard 
        v-for="property in properties" 
        :key="property.id" 
        :property="property" 
      />
    </div>
    
    <div v-else class="empty-properties">
      <HomeIcon class="empty-icon" />
      <h3>No tienes propiedades registradas</h3>
      <p>Añade tu primera propiedad para empezar a recibir reservas</p>
      <button class="add-property-button" @click="showAddPropertyModal = true">
        Añadir Propiedad
      </button>
    </div>
    
    <AddPropertyModal 
      v-if="showAddPropertyModal" 
      @close="showAddPropertyModal = false"
      @add-property="addProperty"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { PlusIcon, HomeIcon } from 'lucide-vue-next';
import PropertyCard from './PropertyCard.vue';
import AddPropertyModal from './AddPropertyModal.vue';

const props = defineProps({
  properties: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['add-property']);

const showAddPropertyModal = ref(false);

const addProperty = (newProperty) => {
  emit('add-property', newProperty);
  showAddPropertyModal.value = false;
};
</script>

<style scoped>
.add-button {
  display: flex;
  align-items: center;
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-button:hover {
  background-color: #005999;
}

.add-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.empty-properties {
  text-align: center;
  padding: 3rem 0;
}

.empty-properties .empty-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1rem;
}

.empty-properties h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.empty-properties p {
  color: #666;
  margin-bottom: 1.5rem;
}

.add-property-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-property-button:hover {
  background-color: #005999;
}

@media (max-width: 576px) {
  .properties-grid {
    grid-template-columns: 1fr;
  }
}
</style>