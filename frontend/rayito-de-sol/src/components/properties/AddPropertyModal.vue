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
            <div class="location-input">
              <MapPinIcon class="input-icon" />
              <input id="property-location" v-model="newProperty.location" type="text" class="form-input with-icon" required placeholder="Ciudad, Provincia" />
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="property-bedrooms">Dormitorios</label>
              <div class="number-input">
                <BedIcon class="input-icon" />
                <input id="property-bedrooms" v-model="newProperty.bedrooms" type="number" min="1" class="form-input with-icon" required />
              </div>
            </div>
            
            <div class="form-group">
              <label for="property-capacity">Capacidad (huéspedes)</label>
              <div class="number-input">
                <UsersIcon class="input-icon" />
                <input id="property-capacity" v-model="newProperty.capacity" type="number" min="1" class="form-input with-icon" required />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="property-price">Precio por noche (€)</label>
            <div class="price-input">
              <EuroIcon class="input-icon" />
              <input id="property-price" v-model="newProperty.price" type="number" min="1" class="form-input with-icon" required />
            </div>
          </div>
          
          <div class="form-group">
            <label for="property-description">Descripción</label>
            <textarea 
              id="property-description" 
              v-model="newProperty.description" 
              class="form-textarea" 
              rows="4" 
              required
              placeholder="Describe tu propiedad, destacando sus características principales y lo que la hace especial..."
            ></textarea>
          </div>
          
          <div class="form-group">
            <label>Imagen de la propiedad</label>
            <div class="image-upload" @click="triggerFileInput">
              <input 
                type="file" 
                ref="fileInput" 
                accept="image/*" 
                @change="handleImageUpload" 
                style="display: none" 
              />
              <div v-if="!imagePreview" class="upload-placeholder">
                <ImageIcon class="upload-icon" />
                <span>Haz clic para subir una imagen</span>
              </div>
              <img v-else :src="imagePreview" class="image-preview" />
            </div>
          </div>
          
          <div class="form-group">
            <label>Servicios disponibles</label>
            <div class="amenities-grid">
              <label v-for="(amenity, index) in amenities" :key="index" class="amenity-checkbox">
                <input 
                  type="checkbox" 
                  :value="amenity.id" 
                  v-model="newProperty.amenities" 
                />
                <span class="checkbox-label">{{ amenity.name }}</span>
              </label>
            </div>
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
import { XIcon, MapPinIcon, BedIcon, UsersIcon, EuroIcon, ImageIcon } from 'lucide-vue-next';

const emit = defineEmits(['close', 'add-property']);

const newProperty = ref({
  name: '',
  location: '',
  bedrooms: 2,
  capacity: 4,
  price: 100,
  description: '',
  amenities: [],
  image: null,
  status: 'active',
  statusText: 'Activo'
});

const imagePreview = ref(null);
const fileInput = ref(null);

const amenities = [
  { id: 1, name: 'Wi-Fi' },
  { id: 2, name: 'Piscina' },
  { id: 3, name: 'Aire acondicionado' },
  { id: 4, name: 'Cocina equipada' },
  { id: 5, name: 'Lavadora' },
  { id: 6, name: 'TV' },
  { id: 7, name: 'Terraza' },
  { id: 8, name: 'Parking' },
  { id: 9, name: 'Vista al mar' },
  { id: 10, name: 'Acceso a playa' },
];

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
      newProperty.value.image = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const submitForm = () => {
  // Si no hay imagen, usar una imagen de placeholder
  if (!newProperty.value.image) {
    newProperty.value.image = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80';
  }
  
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

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
}

.amenity-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.amenity-checkbox input {
  width: 18px;
  height: 18px;
  accent-color: #0071c2;
}

.checkbox-label {
  color: #1e293b;
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
  
  .amenities-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}
</style>