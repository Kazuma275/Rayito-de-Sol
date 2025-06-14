<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content" @click.stop>
      <h2>{{ isEditMode ? 'Editar' : 'Añadir' }} Propiedad</h2>
      <form @submit.prevent="onSubmit">
        <div class="form-group">
          <label for="property-name">Nombre de la propiedad</label>
          <input id="property-name" v-model="localProperty.name" type="text" class="form-input" required />
        </div>
        <div class="form-group">
          <label for="property-location">Ubicación</label>
          <input id="property-location" v-model="localProperty.location" type="text" class="form-input" required />
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="property-bedrooms">Dormitorios</label>
            <input id="property-bedrooms" v-model="localProperty.bedrooms" type="number" min="1" class="form-input" required />
          </div>
          <div class="form-group">
            <label for="property-capacity">Capacidad (huéspedes)</label>
            <input id="property-capacity" v-model="localProperty.capacity" type="number" min="1" class="form-input" required />
          </div>
        </div>
        <div class="form-group">
          <label for="property-price">Precio por noche (€)</label>
          <input id="property-price" v-model="localProperty.price" type="number" min="1" class="form-input" required />
        </div>
        <div class="form-group">
          <label for="property-description">Descripción</label>
          <textarea id="property-description" v-model="localProperty.description" class="form-textarea" rows="4" required></textarea>
        </div>
        <div class="form-group">
          <label>Fotos</label>
          <div class="photo-upload" @click="triggerImageInput">
            <div class="upload-placeholder" v-if="!localProperty.image">
              <UploadIcon class="upload-icon" />
              <span>Subir foto</span>
            </div>
            <img v-else :src="localProperty.image" class="uploaded-image-preview" />
            <input
              ref="imageInput"
              type="file"
              accept="image/*"
              @change="handleImageUpload"
              style="display: none"
            />
          </div>
        </div>
        <div class="form-group">
          <label>Servicios</label>
          <div class="amenities-grid">
            <div v-for="amenity in amenities" :key="amenity.id" class="amenity-checkbox">
              <input :id="`amenity-${amenity.id}`" type="checkbox" v-model="localProperty.amenities" :value="amenity.id" />
              <label :for="`amenity-${amenity.id}`">{{ amenity.name }}</label>
            </div>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" class="cancel-button" @click="$emit('close')">Cancelar</button>
          <button type="submit" class="submit-button">{{ isEditMode ? 'Guardar Cambios' : 'Guardar Propiedad' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { UploadIcon } from 'lucide-vue-next'

const props = defineProps({
  visible: Boolean,
  isEditMode: Boolean,
  property: Object,
  amenities: Array
})

const emit = defineEmits(['save', 'close'])
const imageInput = ref(null)
const localProperty = ref({
  id: null,
  name: '',
  location: '',
  bedrooms: 1,
  capacity: 1,
  price: 0,
  image: null,
  description: '',
  amenities: [],
  status: 'active',
  statusText: 'Activo'
})

watch(
  () => props.property,
  (val) => {
    if (val) localProperty.value = { ...val }
    else localProperty.value = {
      id: null, name: '', location: '', bedrooms: 1, capacity: 1, price: 0, image: null, description: '', amenities: [], status: 'active', statusText: 'Activo'
    }
  },
  { immediate: true }
)

function triggerImageInput() {
  imageInput.value && imageInput.value.click()
}

function handleImageUpload(e) {
  const file = e.target.files[0]
  if (file && file.type.startsWith('image/')) {
    localProperty.value.image = URL.createObjectURL(file)
    // Si necesitas guardar el archivo original, puedes añadirlo también
  }
}

function onSubmit() {
  emit('save', { ...localProperty.value })
}
</script>