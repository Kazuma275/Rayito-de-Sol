<template>
  <section class="properties-section">
    <div class="section-header">
      <h2 class="section-title">Mis Propiedades</h2>
      <button class="add-button" @click="onAdd">
        <PlusIcon class="add-icon" />
        Añadir Propiedad
      </button>
    </div>

    <!-- Mensaje de carga -->
    <div v-if="isLoading" class="loading-properties">
      <svg class="spinner" viewBox="0 0 50 50">
        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"/>
      </svg>
      <span class="loading-text">Esperando... Cargando propiedades</span>
    </div>

    <!-- Grid de propiedades -->
    <PropertiesGrid
      v-else-if="properties.length > 0"
      :properties="properties"
      @edit="onEdit"
      @calendar="onCalendar"
    />

    <!-- Estado vacío -->
    <div v-else class="empty-properties">
      <HomeIcon class="empty-icon" />
      <h3>No tienes propiedades registradas</h3>
      <p>Añade tu primera propiedad para empezar a recibir reservas</p>
      <button class="add-property-button" @click="onAdd">
        Añadir Propiedad
      </button>
    </div>
    
    <!-- Modal crear/editar propiedad -->
    <PropertyModal
      v-if="showModal"
      :visible="showModal"
      :is-edit-mode="isEditMode"
      :property="editProperty"
      :amenities="amenities"
      @save="onSave"
      @close="onCloseModal"
    />
  </section>
</template>

<script setup>
import { ref, watch } from 'vue'
import { PlusIcon, HomeIcon } from 'lucide-vue-next'
import PropertiesGrid from './PropertiesGrid.vue'
import PropertyModal from './PropertyModal.vue'

// Props
defineProps({
  properties: { type: Array, required: true },
  isLoading: { type: Boolean, default: false },
  amenities: { type: Array, default: () => [] }
})

// Emits para comunicar con el padre
const emit = defineEmits(['add', 'edit', 'calendar', 'save', 'close-modal'])

// Estado local para el modal y edición
const showModal = ref(false)
const isEditMode = ref(false)
const editProperty = ref(null)

// Métodos para abrir modal de añadir o editar
function onAdd() {
  isEditMode.value = false
  editProperty.value = null
  showModal.value = true
  emit('add')
}

function onEdit(property) {
  isEditMode.value = true
  editProperty.value = property
  showModal.value = true
  emit('edit', property)
}

function onCalendar(propertyId) {
  emit('calendar', propertyId)
}

function onSave(propertyData) {
  emit('save', propertyData)
  showModal.value = false
}

function onCloseModal() {
  showModal.value = false
  emit('close-modal')
}
</script>