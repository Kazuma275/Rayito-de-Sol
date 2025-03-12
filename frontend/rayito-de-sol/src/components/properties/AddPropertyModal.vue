<template>
  <div class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>Añadir Nueva Propiedad</h3>
        <button class="close-button" @click="$emit('close')">
          <XIcon />
        </button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="submitForm" class="property-form">
          <div class="form-group">
            <label for="property-name">Nombre de la propiedad</label>
            <input id="property-name" v-model="newProperty.name" type="text" class="form-input" required />
          </div>
          
          <div class="form-group">
            <label for="property-location">Ubicación</label>
            <input id="property-location" v-model="newProperty.location" type="text" class="form-input" required />
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="property-bedrooms">Dormitorios</label>
              <input id="property-bedrooms" v-model="newProperty.bedrooms" type="number" min="1" class="form-input" required />
            </div>
            
            <div class="form-group">
              <label for="property-capacity">Capacidad (huéspedes)</label>
              <input id="property-capacity" v-model="newProperty.capacity" type="number" min="1" class="form-input" required />
            </div>
          </div>
          
          <div class="form-group">
            <label for="property-price">Precio por noche (€)</label>
            <input id="property-price" v-model="newProperty.price" type="number" min="1" class="form-input" required />
          </div>
          
          <div class="form-group">
            <label for="property-description">Descripción</label>
            <textarea id="property-description" v-model="newProperty.description" class="form-textarea" rows="4" required></textarea>
          </div>
          
          <div class="form-actions">
            <button type="button" class="cancel-button" @click="$emit('close')">Cancelar</button>
            <button type="submit" class="submit-button">Guardar Propiedad</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { XIcon } from 'lucide-vue-next';

const emit = defineEmits(['close', 'add-property']);

const newProperty = ref({
  name: '',
  location: '',
  bedrooms: 2,
  capacity: 4,
  price: 100,
  description: '',
  amenities: []
});

const submitForm = () => {
  emit('add-property', { ...newProperty.value });
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.3s;
}

.close-button:hover {
  background-color: #f5f5f5;
}

.modal-body {
  padding: 1.5rem;
}

.property-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-button {
  background-color: white;
  color: #666;
  border: 1px solid #ccc;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cancel-button:hover {
  background-color: #f5f5f5;
}

.submit-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #005999;
}

@media (max-width: 576px) {
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>