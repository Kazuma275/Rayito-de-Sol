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
            <input v-model="localProperty.location" type="text" required class="form-input" />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Dormitorios</label>
              <input v-model.number="localProperty.bedrooms" type="number" min="1" required class="form-input" />
            </div>
            <div class="form-group">
              <label>Capacidad</label>
              <input v-model.number="localProperty.capacity" type="number" min="1" required class="form-input" />
            </div>
          </div>

          <div class="form-group">
            <label>Precio por noche (€)</label>
            <input v-model.number="localProperty.price" type="number" min="1" required class="form-input" />
          </div>

          <div class="form-group">
            <label>Descripción</label>
            <textarea v-model="localProperty.description" class="form-textarea" rows="4" required></textarea>
          </div>

          <div class="form-group">
            <label>Foto</label>
            <div class="photo-upload" @click="$refs.imageInput.click()">
              <div class="upload-placeholder" v-if="!localProperty.image">
                <UploadIcon class="upload-icon" />
                <span>Subir foto</span>
              </div>
              <img v-else :src="localProperty.image" class="uploaded-image-preview" />
              <input ref="imageInput" type="file" accept="image/*" @change="handleImageUpload" style="display: none" />
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
import { UploadIcon, XIcon } from 'lucide-vue-next'

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

.form-group {
  display: flex;
  flex-direction: column;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.form-group label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.form-input, .form-textarea {
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
}

.form-textarea {
  resize: vertical;
}

.photo-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  margin-top: 0.5rem;
}

.upload-placeholder {
  width: 150px;
  height: 150px;
  border: 2px dashed #aaa;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: #777;
  background-color: #f9f9f9;
  transition: border-color 0.3s, background-color 0.3s;
}

.upload-placeholder:hover {
  border-color: #0071c2;
  background-color: #f0f8ff;
}

.upload-icon {
  width: 32px;
  height: 32px;
  color: #0071c2;
  margin-bottom: 0.5rem;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.75rem;
}

.amenity-checkbox {
  display: flex;
  align-items: center;
}

.amenity-checkbox input {
  margin-right: 0.5rem;
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

.uploaded-image-preview {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.submit-button,
.cancel-button {
  padding: 0.5rem 1rem;
  border-radius: 6px;
}

.submit-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}

.cancel-button {
  background-color: #f44336;
  color: white;
  border: none;
  cursor: pointer;
}

.submit-button:hover {
  background-color: #45a049;
}

.cancel-button:hover {
  background-color: #e53935;
}
</style>
