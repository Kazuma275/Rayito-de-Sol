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
            <span v-if="validationErrors.name" class="form-error">{{ validationErrors.name[0] }}</span>
          </div>

          <div class="form-group">
            <label>Ubicación</label>
            <div class="location-input">
              <MapPinIcon class="input-icon" />
              <input v-model="localProperty.location" type="text" required class="form-input with-icon" placeholder="Ciudad, Provincia" />
            </div>
            <span v-if="validationErrors.location" class="form-error">{{ validationErrors.location[0] }}</span>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Dormitorios</label>
              <div class="number-input">
                <BedIcon class="input-icon" />
                <input v-model.number="localProperty.bedrooms" type="number" min="1" required class="form-input with-icon" />
              </div>
              <span v-if="validationErrors.bedrooms" class="form-error">{{ validationErrors.bedrooms[0] }}</span>
            </div>
            <div class="form-group">
              <label>Capacidad</label>
              <div class="number-input">
                <UsersIcon class="input-icon" />
                <input v-model.number="localProperty.capacity" type="number" min="1" required class="form-input with-icon" />
              </div>
              <span v-if="validationErrors.capacity" class="form-error">{{ validationErrors.capacity[0] }}</span>
            </div>
          </div>

          <div class="form-group">
            <label>Precio por noche (€)</label>
            <div class="price-input">
              <EuroIcon class="input-icon" />
              <input v-model.number="localProperty.price" type="number" min="1" required class="form-input with-icon" />
            </div>
            <span v-if="validationErrors.price" class="form-error">{{ validationErrors.price[0] }}</span>
          </div>

          <div class="form-group">
            <label>Descripción</label>
            <textarea v-model="localProperty.description" class="form-textarea" rows="4" required></textarea>
            <span v-if="validationErrors.description" class="form-error">{{ validationErrors.description[0] }}</span>
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
            <span v-if="validationErrors.image" class="form-error">{{ validationErrors.image[0] }}</span>
          </div>

          <!-- NUEVA SECCIÓN DE AMENITIES MEJORADA -->
          <div class="form-group">
            <label>Comodidades</label>
            <div class="amenities-container">
              <!-- Amenities predefinidos -->
              <div class="amenities-grid">
                <div 
                  v-for="amenity in predefinedAmenities" 
                  :key="amenity.id"
                  class="amenity-card"
                  :class="{ selected: selectedAmenities.includes(amenity.name) }"
                  @click="toggleAmenity(amenity.name)"
                >
                  <div class="amenity-icon">
                    <component :is="amenity.icon" />
                  </div>
                  <span class="amenity-name">{{ amenity.name }}</span>
                  <div class="amenity-check" v-if="selectedAmenities.includes(amenity.name)">
                    <CheckIcon />
                  </div>
                </div>
              </div>

              <!-- Amenities personalizados -->
              <div class="custom-amenities-section">
                <h4 class="custom-title">Amenidades personalizadas</h4>
                <div class="custom-amenities-list">
                  <div 
                    v-for="(customAmenity, index) in customAmenities" 
                    :key="`custom-${index}`"
                    class="custom-amenity-item"
                  >
                    <input
                      v-model="customAmenities[index]"
                      type="text"
                      class="custom-amenity-input"
                      placeholder="Ej: Terraza privada, Jacuzzi..."
                      @input="updateAmenities"
                    />
                    <button
                      v-if="customAmenities.length > 1"
                      type="button"
                      @click="removeCustomAmenity(index)"
                      class="remove-custom-button"
                      aria-label="Eliminar amenidad personalizada"
                    >
                      <XIcon />
                    </button>
                  </div>
                </div>
                <button 
                  type="button" 
                  @click="addCustomAmenity" 
                  class="add-custom-button"
                >
                  <PlusIcon />
                  Añadir amenidad personalizada
                </button>
              </div>

              <!-- Resumen de amenities seleccionados -->
              <div v-if="allSelectedAmenities.length > 0" class="selected-summary">
                <h4 class="summary-title">Amenidades seleccionadas ({{ allSelectedAmenities.length }})</h4>
                <div class="selected-tags">
                  <span 
                    v-for="amenity in allSelectedAmenities" 
                    :key="amenity"
                    class="selected-tag"
                  >
                    {{ amenity }}
                    <button 
                      @click="removeAmenity(amenity)"
                      class="remove-tag-button"
                      aria-label="Quitar amenidad"
                    >
                      <XIcon />
                    </button>
                  </span>
                </div>
              </div>
            </div>
            <span v-if="validationErrors.amenities" class="form-error">{{ validationErrors.amenities[0] }}</span>
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
            <span v-if="validationErrors.status" class="form-error">{{ validationErrors.status[0] }}</span>
          </div>

          <div v-if="formError" class="form-error form-error-global">{{ formError }}</div>

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
import { ref, watch, computed } from 'vue'
import { 
  XIcon, 
  MapPinIcon, 
  BedIcon, 
  UsersIcon, 
  EuroIcon, 
  ImageIcon, 
  CheckIcon, 
  XCircleIcon,
  PlusIcon,
  WifiIcon, 
  CarIcon, 
  UtensilsIcon, 
  TvIcon, 
  AirVentIcon, 
  WashingMachineIcon,
  DumbbellIcon,
  WavesIcon,
  TreesIcon,
  CoffeeIcon,
  ShieldCheckIcon,
  PawPrintIcon,
  BathIcon,
  MicrowaveIcon,
  RefrigeratorIcon,
  SnowflakeIcon,
  ThermometerIcon
} from 'lucide-vue-next'
import axios from 'axios'

// Usa variable de entorno si tienes VITE_API_URL
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const props = defineProps({
  visible: Boolean,
  property: Object,
  isEditMode: Boolean
})

const emit = defineEmits(['submit', 'close', 'error'])

// Amenities predefinidos con iconos
const predefinedAmenities = ref([
  { id: 1, name: 'WiFi gratuito', icon: WifiIcon },
  { id: 2, name: 'Aparcamiento gratuito', icon: CarIcon },
  { id: 3, name: 'Cocina equipada', icon: UtensilsIcon },
  { id: 4, name: 'TV', icon: TvIcon },
  { id: 5, name: 'Aire acondicionado', icon: AirVentIcon },
  { id: 6, name: 'Lavadora', icon: WashingMachineIcon },
  { id: 7, name: 'Gimnasio', icon: DumbbellIcon },
  { id: 8, name: 'Piscina', icon: WavesIcon },
  { id: 9, name: 'Jardín', icon: TreesIcon },
  { id: 10, name: 'Cafetera', icon: CoffeeIcon },
  { id: 11, name: 'Caja fuerte', icon: ShieldCheckIcon },
  { id: 12, name: 'Se admiten mascotas', icon: PawPrintIcon },
  { id: 13, name: 'Bañera', icon: BathIcon },
  { id: 14, name: 'Microondas', icon: MicrowaveIcon },
  { id: 15, name: 'Nevera', icon: RefrigeratorIcon },
  { id: 16, name: 'Calefacción', icon: ThermometerIcon },
  { id: 17, name: 'Congelador', icon: SnowflakeIcon }
])

const localProperty = ref({})
const formError = ref('')
const validationErrors = ref({})

// Estados para amenities
const selectedAmenities = ref([])
const customAmenities = ref([''])

// Computed para obtener todas las amenidades seleccionadas
const allSelectedAmenities = computed(() => {
  const predefined = selectedAmenities.value
  const custom = customAmenities.value.filter(amenity => amenity.trim() !== '')
  return [...predefined, ...custom]
})

// Watch para inicializar localProperty correctamente
watch(
  () => props.property,
  (newVal) => {
    localProperty.value = { 
      name: '',
      location: '',
      bedrooms: 1,
      capacity: 1,
      price: 1,
      description: '',
      image: '',
      status: 'active',
      statusText: 'Activo',
      amenities: [],
      ...newVal
    }
    
    // Inicializar amenities
    if (Array.isArray(localProperty.value.amenities)) {
      const predefinedNames = predefinedAmenities.value.map(a => a.name)
      selectedAmenities.value = localProperty.value.amenities.filter(amenity => predefinedNames.includes(amenity))
      const customValues = localProperty.value.amenities.filter(amenity => !predefinedNames.includes(amenity))
      customAmenities.value = customValues.length > 0 ? customValues : ['']
    } else {
      selectedAmenities.value = []
      customAmenities.value = ['']
    }
    
    formError.value = ''
    validationErrors.value = {}
  },
  { immediate: true }
)

// Watch para actualizar localProperty.amenities cuando cambien las amenities
watch(allSelectedAmenities, (newAmenities) => {
  localProperty.value.amenities = newAmenities
}, { deep: true })

// Métodos para amenities
const toggleAmenity = (amenityName) => {
  const index = selectedAmenities.value.indexOf(amenityName)
  if (index > -1) {
    selectedAmenities.value.splice(index, 1)
  } else {
    selectedAmenities.value.push(amenityName)
  }
}

const addCustomAmenity = () => {
  customAmenities.value.push('')
}

const removeCustomAmenity = (index) => {
  customAmenities.value.splice(index, 1)
  if (customAmenities.value.length === 0) {
    customAmenities.value.push('')
  }
}

const removeAmenity = (amenityName) => {
  // Remover de predefinidos
  const predefinedIndex = selectedAmenities.value.indexOf(amenityName)
  if (predefinedIndex > -1) {
    selectedAmenities.value.splice(predefinedIndex, 1)
    return
  }
  
  // Remover de personalizados
  const customIndex = customAmenities.value.indexOf(amenityName)
  if (customIndex > -1) {
    customAmenities.value.splice(customIndex, 1)
    if (customAmenities.value.length === 0) {
      customAmenities.value.push('')
    }
  }
}

const updateAmenities = () => {
  // Método para forzar actualización cuando se escriben amenities personalizados
  localProperty.value.amenities = allSelectedAmenities.value
}

// Manejo de imagen (base64)
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

// Enviar formulario
const handleSubmit = async () => {
  formError.value = ''
  validationErrors.value = {}
  
  // Clona el objeto y elimina statusText y campos vacíos innecesarios
  const payload = { ...localProperty.value }
  delete payload.statusText
  if (!payload.image) delete payload.image
  if (!payload.description) delete payload.description
  
  // Asegurar que amenities esté actualizado y limpio
  payload.amenities = allSelectedAmenities.value
  
  try {
    if (props.isEditMode) {
      await axios.put(`${API_BASE_URL}/properties/${localProperty.value.id}`, payload)
    } else {
      await axios.post(`${API_BASE_URL}/properties`, payload)
    }
    emit('submit', { ...localProperty.value })
  } catch (error) {
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors || {}
      formError.value = 'Hay errores en el formulario'
    } else if (error.response && error.response.data && error.response.data.message) {
      formError.value = error.response.data.message
    } else {
      formError.value = 'Error al crear la propiedad'
    }
    emit('error', formError.value)
  }
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
  max-width: 800px;
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

/* ESTILOS PARA AMENITIES */
.amenities-container {
  background: #f8fafc;
  border: 1px solid #e6f0ff;
  border-radius: 12px;
  padding: 1.5rem;
  margin-top: 0.5rem;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.amenity-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border: 2px solid #e6f0ff;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  transition: all 0.3s ease;
  user-select: none;
}

.amenity-card:hover {
  border-color: #0071c2;
  background: #f0f7ff;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 113, 194, 0.1);
}

.amenity-card.selected {
  border-color: #0071c2;
  background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
  box-shadow: 0 2px 8px rgba(0, 113, 194, 0.15);
}

.amenity-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  color: #0071c2;
}

.amenity-name {
  flex: 1;
  font-weight: 500;
  color: #1e293b;
  font-size: 0.85rem;
}

.amenity-check {
  position: absolute;
  top: 0.25rem;
  right: 0.25rem;
  width: 16px;
  height: 16px;
  background: #0071c2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.amenity-check svg {
  width: 10px;
  height: 10px;
}

.custom-amenities-section {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e6f0ff;
}

.custom-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #003580;
  margin: 0 0 1rem 0;
}

.custom-amenities-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.custom-amenity-item {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.custom-amenity-input {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border: 1px solid #cce0ff;
  border-radius: 6px;
  font-size: 0.85rem;
  background: white;
  transition: all 0.3s ease;
}

.custom-amenity-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

.remove-custom-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: #fee2e2;
  border: 1px solid #fecaca;
  border-radius: 4px;
  color: #dc2626;
  cursor: pointer;
  transition: all 0.3s ease;
}

.remove-custom-button:hover {
  background: #fecaca;
}

.remove-custom-button svg {
  width: 12px;
  height: 12px;
}

.add-custom-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: white;
  border: 1px dashed #0071c2;
  border-radius: 6px;
  color: #0071c2;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.85rem;
}

.add-custom-button:hover {
  background: #f0f7ff;
}

.add-custom-button svg {
  width: 14px;
  height: 14px;
}

.selected-summary {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e6f0ff;
}

.summary-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #003580;
  margin: 0 0 0.75rem 0;
}

.selected-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.selected-tag {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.625rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border-radius: 16px;
  font-size: 0.8rem;
  font-weight: 500;
}

.remove-tag-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 14px;
  height: 14px;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  transition: background 0.3s ease;
}

.remove-tag-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.remove-tag-button svg {
  width: 8px;
  height: 8px;
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

.form-error {
  color: #e41c00;
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

.form-error-global {
  background: #fff2f0;
  border: 1px solid #fecaca;
  border-radius: 6px;
  padding: 0.75rem;
  text-align: center;
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
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.5rem;
  }
  
  .amenity-card {
    padding: 0.5rem;
  }
  
  .amenity-name {
    font-size: 0.8rem;
  }
  
  .custom-amenity-item {
    flex-direction: column;
    align-items: stretch;
  }
  
  .remove-custom-button {
    align-self: flex-end;
  }
}

@media (max-width: 480px) {
  .amenities-grid {
    grid-template-columns: 1fr;
  }
  
  .selected-tags {
    flex-direction: column;
  }
  
  .selected-tag {
    justify-content: space-between;
  }
}
</style>