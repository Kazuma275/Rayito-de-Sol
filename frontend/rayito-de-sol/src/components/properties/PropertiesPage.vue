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
      <div class="empty-illustration">
        <HomeIcon class="empty-icon" />
        <div class="empty-waves"></div>
      </div>
      <h3>No tienes propiedades registradas</h3>
      <p>Añade tu primera propiedad para empezar a recibir reservas</p>
      <button class="add-property-button" @click="showAddPropertyModal = true">
        <PlusIcon class="button-icon" />
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
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.add-button {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.add-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.add-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.empty-properties {
  text-align: center;
  padding: 4rem 2rem;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.empty-illustration {
  position: relative;
  width: 120px;
  height: 120px;
  margin: 0 auto 2rem;
}

.empty-icon {
  width: 80px;
  height: 80px;
  color: #0071c2;
  position: relative;
  z-index: 2;
  animation: float 3s ease-in-out infinite;
}

.empty-waves {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 40px;
  background: linear-gradient(90deg, #e6f0ff, #cce0ff, #e6f0ff);
  border-radius: 50%;
  z-index: 1;
  opacity: 0.7;
  animation: wave 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes wave {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

.empty-properties h3 {
  font-size: 1.4rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.empty-properties p {
  color: #64748b;
  margin-bottom: 2rem;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

.add-property-button {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.add-property-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .add-button {
    width: 100%;
    justify-content: center;
  }
  
  .properties-grid {
    grid-template-columns: 1fr;
  }
}
</style>