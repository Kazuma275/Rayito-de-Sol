<template>
  <div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <button
      class="mb-5 text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center gap-1"
      @click="goBack"
    >
      <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Volver
    </button>
    <div v-if="property">
      <h1 class="text-3xl font-bold mb-4">{{ property.name }}</h1>
      <div class="flex flex-col md:flex-row gap-6">
        <div class="md:w-1/2">
          <img
            v-if="property.image"
            :src="property.image"
            :alt="property.name"
            class="rounded-lg w-full h-64 object-cover border"
          />
          <div
            v-else
            class="flex items-center justify-center bg-gray-100 border rounded-lg w-full h-64 text-gray-400 text-xl"
          >
            Sin imagen
          </div>
        </div>
        <div class="md:w-1/2 flex flex-col gap-3">
          <div class="flex gap-3">
            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
              {{ property.location }}
            </span>
            <span
              class="px-3 py-1 rounded-full"
              :class="property.status === 'active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
            >
              {{ property.status === 'active' ? 'Disponible' : 'No disponible' }}
            </span>
          </div>
          <div class="flex flex-wrap gap-4 mt-2 text-gray-700">
            <span><strong>Dormitorios:</strong> {{ property.bedrooms }}</span>
            <span><strong>Capacidad:</strong> {{ property.capacity }}</span>
            <span><strong>Precio:</strong> ${{ property.price }}</span>
          </div>
          <div class="mt-4">
            <h2 class="text-lg font-semibold mb-1">Amenidades</h2>
            <ul v-if="property.amenities && property.amenities.length > 0" class="flex flex-wrap gap-2">
              <li
                v-for="amenity in property.amenities"
                :key="amenity"
                class="flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-sm"
              >
                <svg
                  class="w-4 h-4 text-blue-400"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 12l2 2 4-4" />
                </svg>
                {{ amenity }}
              </li>
            </ul>
            <div v-else class="text-gray-400 italic text-sm">Sin amenidades registradas</div>
          </div>
        </div>
      </div>
      <div class="mt-6">
        <h2 class="text-lg font-semibold mb-2">Descripción</h2>
        <p class="text-gray-700 leading-relaxed">{{ property.description }}</p>
      </div>
    </div>
    <div v-else class="text-center py-16">
      <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
      </svg>
      <p class="text-gray-400">Cargando propiedad...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const property = ref(null)

const goBack = () => {
  router.back()
}

onMounted(async () => {
  console.log('Detalle montado');
  console.log('route.params.id:', route.params.id);
  try {
    const res = await axios.get(`/api/properties/${route.params.id}`)
    property.value = res.data
    console.log('Propiedad cargada:', res.data)
  } catch (error) {
    property.value = null
    console.error('Error fetch propiedad:', error)
  }
})
</script>