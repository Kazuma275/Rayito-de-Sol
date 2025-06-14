import { ref, watch } from "vue"

const STORAGE_KEY = "favorites"
const SUMMARY_KEY = "favorite-summaries"

// Inicializa favoriteIds desde localStorage o vacío
const favoriteIds = ref(JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]"))

// Inicializa favoriteSummaries desde localStorage o vacío (array de objetos con datos básicos)
const favoriteSummaries = ref(JSON.parse(localStorage.getItem(SUMMARY_KEY) || "[]"))

// Sincroniza localStorage cuando favoriteIds cambie
watch(
  favoriteIds,
  (newVal) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(newVal))
    console.log("Favoritos guardados en localStorage:", newVal)
  },
  { deep: true },
)

// Sincroniza localStorage cuando favoriteSummaries cambie
watch(
  favoriteSummaries,
  (newVal) => {
    localStorage.setItem(SUMMARY_KEY, JSON.stringify(newVal))
    console.log("Resumen de favoritos guardado en localStorage:", newVal)
  },
  { deep: true },
)

export function useFavorites() {
  // Comprueba si un id está en favoritos
  const isFavorite = (id) => {
    const result = favoriteIds.value.includes(id)
    console.log(`¿Es favorito ${id}?`, result)
    return result
  }

  // Añade o quita el id de favoritos
  const toggleFavorite = (id) => {
    console.log("Toggling favorite para ID:", id)
    console.log("Estado actual de favoriteIds:", favoriteIds.value)

    const index = favoriteIds.value.indexOf(id)
    if (index === -1) {
      favoriteIds.value.push(id)
      console.log(`Añadido ${id} a favoritos. Nuevo estado:`, favoriteIds.value)
    } else {
      favoriteIds.value.splice(index, 1)
      // También elimina el resumen si existe
      removeFavoriteSummary(id)
      console.log(`Eliminado ${id} de favoritos. Nuevo estado:`, favoriteIds.value)
    }
  }

  // Opción para limpiar todos los favoritos
  const clearFavorites = () => {
    console.log("Limpiando todos los favoritos")
    favoriteIds.value = []
    favoriteSummaries.value = []
    localStorage.removeItem(STORAGE_KEY)
    localStorage.removeItem(SUMMARY_KEY)
  }

  // Recarga los favoritos desde localStorage
  const reloadFavorites = () => {
    console.log("Recargando favoritos manualmente")
    favoriteIds.value = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]")
    favoriteSummaries.value = JSON.parse(localStorage.getItem(SUMMARY_KEY) || "[]")
  }

  // Función para cargar favoritos (para compatibilidad)
  const loadFavorites = () => {
    console.log("Cargando favoritos desde localStorage")
    favoriteIds.value = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]")
    favoriteSummaries.value = JSON.parse(localStorage.getItem(SUMMARY_KEY) || "[]")
  }

  // Función para guardar favoritos (para compatibilidad)
  const saveFavorites = () => {
    console.log("Guardando favoritos en localStorage")
    localStorage.setItem(STORAGE_KEY, JSON.stringify(favoriteIds.value))
    localStorage.setItem(SUMMARY_KEY, JSON.stringify(favoriteSummaries.value))
  }

  // ----- NUEVO: Métodos para guardar/gestionar resúmenes -----

  // Guarda la información básica de una propiedad favorita (si su id está en favoritos y no existe ya)
  const saveFavoriteSummary = (property) => {
    if (!favoriteIds.value.includes(property.id)) return
    if (favoriteSummaries.value.some(p => p.id == property.id)) return
    favoriteSummaries.value.push({
      id: property.id,
      name: property.name,
      location: property.location,
      price: property.price,
      image: property.image,
    })
    console.log(`Resumen guardado para propiedad ${property.id}`)
  }

  // Elimina el resumen de una propiedad favorita
  const removeFavoriteSummary = (propertyId) => {
    favoriteSummaries.value = favoriteSummaries.value.filter(p => p.id != propertyId)
    console.log(`Resumen eliminado para propiedad ${propertyId}`)
  }

  return {
    favoriteIds,
    isFavorite,
    toggleFavorite,
    clearFavorites,
    reloadFavorites,
    loadFavorites,
    saveFavorites,
    // nuevos para gestión de resúmenes
    favoriteSummaries,
    saveFavoriteSummary,
    removeFavoriteSummary,
  }
}