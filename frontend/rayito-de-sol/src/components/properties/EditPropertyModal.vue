<template>
  <div v-if="visible" class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>{{ isEditMode ? 'Editar Propiedad' : 'Añadir Nueva Propiedad' }}</h3>
        <button class="close-button" @click="$emit('close')">
          <XIcon />
        </button>
      </div>

      <div class="modal-body">
        <form @submit.prevent="handleSubmit" class="property-form">
          <div class="form-group">
            <label>Nombre</label>
            <input v-model="localProperty.name" type="text" required class="form-input" />
          </div>

          <div class="form-group">
            <label>Ubicación</label>
            <div class="location-input">
              <MapPinIcon class="input-icon" />
              <input v-model="localProperty.location" type="text" required class="form-input with-icon" placeholder="Ciudad, Provincia" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Dormitorios</label>
              <div class="number-input">
                <BedIcon class="input-icon" />
                <input v-model.number="localProperty.bedrooms" type="number" min="1" required class="form-input with-icon" />
              </div>
            </div>
            <div class="form-group">
              <label>Capacidad</label>
              <div class="number-input">
                <UsersIcon class="input-icon" />
                <input v-model.number="localProperty.capacity" type="number" min="1" required class="form-input with-icon" />
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Precio por noche (€)</label>
            <div class="price-input">
              <EuroIcon class="input-icon" />
              <input v-model.number="localProperty.price" type="number" min="1" required class="form-input with-icon" />
            </div>
          </div>

          <div class="form-group">
            <label>Descripción</label>
            <textarea v-model="localProperty.description" class="form-textarea" rows="4" required></textarea>
          </div>

          <div class="form-group">
            <label>Foto</label>
            <div class="image-upload" @click="$refs.imageInput.click()">
              <input ref="imageInput" type="file" accept="image/*" @change="handleImageUpload" style="display: none" />
              <div v-if="!localProperty.image" class="upload-placeholder">
                <ImageIcon class="upload-icon" />
                <span>Haz clic para subir una imagen</span>
              </div>
              <img v-else :src="localProperty.image" class="image-preview" />
            </div>
          </div>
          
          <div class="form-group">
            <label>Estado de la propiedad</label>
            <div class="status-toggle">
              <button 
                type="button" 
                class="status-button" 
                :class="{ active: localProperty.status === 'active' }"
                @click="localProperty.status = 'active'; localProperty.statusText = 'Activo'"
              >
                <CheckIcon class="status-icon" />
                Activo
              </button>
              <button 
                type="button" 
                class="status-button" 
                :class="{ active: localProperty.status === 'inactive' }"
                @click="localProperty.status = 'inactive'; localProperty.statusText = 'Inactivo'"
              >
                <XCircleIcon class="status-icon" />
                Inactivo
              </button>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="cancel-button" @click="$emit('close')">Cancelar</button>
            <button type="submit" class="submit-button">
              {{ isEditMode ? 'Guardar Cambios' : 'Añadir Propiedad' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { XIcon, MapPinIcon, BedIcon, UsersIcon, EuroIcon, ImageIcon, CheckIcon, XCircleIcon } from 'lucide-vue-next'

const props = defineProps({
  visible: Boolean,
  property: Object,
  isEditMode: Boolean
})

const emit = defineEmits(['submit', 'close'])

const localProperty = ref({})

watch(
  () => props.property,
  (newVal) => {
    localProperty.value = { ...newVal }
  },
  { immediate: true }
)

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file && file.type.startsWith('image/')) {
    const reader = new FileReader()
    reader.onload = (e) => {
      localProperty.value.image = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleSubmit = () => {
  emit('submit', { ...localProperty.value })
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 53, 128, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background-color: white;
  border-radius: 12px;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e6f0ff;
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  border-radius: 12px 12px 0 0;
}

.modal-header h3 {
  font-size: 1.4rem;
  margin: 0;
  font-weight: 600;
}

.close-button {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.close-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.modal-body {
  padding: 2rem;
}

.property-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.form-group label {
  font-weight: 600;
  color: #003580;
  font-size: 0.95rem;
}

.form-input, .form-textarea {
  padding: 0.875rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  color: #1e293b;
  background-color: #f8fafc;
}

.form-input.with-icon {
  padding-left: 2.5rem;
}

.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
  background-color: white;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.location-input, .number-input, .price-input {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #0071c2;
  width: 18px;
  height: 18px;
}

.image-upload {
  border: 2px dashed #cce0ff;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: #f8fafc;
}

.image-upload:hover {
  border-color: #0071c2;
  background-color: #f0f7ff;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  color: #64748b;
}

.upload-icon {
  width: 48px;
  height: 48px;
  color: #0071c2;
}

.image-preview {
  max-width: 100%;
  max-height: 200px;
  border-radius: 8px;
}

.status-toggle {
  display: flex;
  gap: 1rem;
}

.status-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  background-color: #f8fafc;
  color: #64748b;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.status-button.active {
  background-color: #f0f7ff;
  border-color: #0071c2;
  color: #0071c2;
}

.status-button:first-child.active {
  background-color: #e6f7ee;
  border-color: #00703c;
  color: #00703c;
}

.status-button:last-child.active {
  background-color: #fff2f0;
  border-color: #e41c00;
  color: #e41c00;
}

.status-icon {
  width: 18px;
  height: 18px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #cce0ff;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover {
  background-color: #f8fafc;
  color: #1e293b;
}

.submit-button {
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

.submit-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
  }
  
  .modal-header {
    padding: 1.25rem 1.5rem;
  }
  
  .modal-body {
    padding: 1.5rem;
  }
  
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>