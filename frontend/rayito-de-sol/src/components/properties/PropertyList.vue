<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { HomeIcon, ChevronLeftIcon, ChevronRightIcon } from "lucide-vue-next";
import RendersPropertyCard from "./RendersPropertyCard.vue";
import { useRouter } from "vue-router";
import { apiHeaders } from "@/../utils/api";
import { getItem } from "@/helpers/storage";

const router = useRouter();

const props = defineProps({
  title: { type: String, default: "Propiedades" },
  showFilters: { type: Boolean, default: true },
  showPagination: { type: Boolean, default: true },
  itemsPerPage: { type: Number, default: 6 },
});

const emit = defineEmits(["toggleFavorite", "viewProperty", "changePage"]);

const properties = ref([]);
const favorites = ref([]);

const sortBy = ref("price_asc");
const activeFilters = ref([]);
const currentPage = ref(1);

const filterOptions = [
  { id: "pool", name: "Piscina" },
  { id: "beach", name: "Playa" },
  { id: "wifi", name: "Wi-Fi" },
  { id: "parking", name: "Parking" },
];
const amenities = [
  { id: "wifi", name: "Wi-Fi" },
  { id: "pool", name: "Piscina" },
  { id: "ac", name: "Aire acondicionado" },
  { id: "kitchen", name: "Cocina equipada" },
  { id: "washer", name: "Lavadora" },
  { id: "tv", name: "TV" },
  { id: "terrace", name: "Terraza" },
  { id: "parking", name: "Parking" },
];

const API_BASE_URL =
  import.meta.env.VITE_API_URL || "http://localhost:8000/api";

async function fetchProperties() {
  try {
    const propRes = await axios.get(`${API_BASE_URL}/properties`, apiHeaders());
    properties.value = propRes.data;
  } catch (e) {
    console.error("Error al cargar propiedades:", e);
    properties.value = [];
  }

  // Solo carga favoritos si hay token
  const token = getItem("auth_token", true) || getItem("auth_token");
  if (token) {
    try {
      const favRes = await axios.get(
        `${API_BASE_URL}/user/favorites`,
        apiHeaders()
      );
      favorites.value = favRes.data;
    } catch (e) {
      console.error("Error al cargar favoritos:", e);
      favorites.value = [];
    }
  } else {
    favorites.value = [];
  }
}

onMounted(fetchProperties);

function propertyHasAmenity(property, filterId) {
  if (!property.amenities || property.amenities.length === 0) return false;
  if (typeof property.amenities[0] === "string") {
    return property.amenities.includes(filterId);
  }
  if (typeof property.amenities[0] === "object") {
    return property.amenities.some((a) => a.id === filterId);
  }
  return false;
}

const filteredProperties = computed(() => {
  let result = [...properties.value];
  if (activeFilters.value.length > 0) {
    result = result.filter((property) =>
      activeFilters.value.some(
        (filter) => property.amenities && property.amenities.includes(filter)
      )
    );
  }
  result.sort((a, b) => {
    switch (sortBy.value) {
      case "price_asc":
        return a.price - b.price;
      case "price_desc":
        return b.price - a.price;
      case "rating":
        return (b.rating || 0) - (a.rating || 0);
      case "newest":
        return new Date(b.createdAt || 0) - new Date(a.createdAt || 0);
      default:
        return 0;
    }
  });
  const startIndex = (currentPage.value - 1) * props.itemsPerPage;
  const endIndex = startIndex + props.itemsPerPage;
  return result.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  let result = [...properties.value];
  if (activeFilters.value.length > 0) {
    result = result.filter((property) =>
      activeFilters.value.every((filter) =>
        propertyHasAmenity(property, filter)
      )
    );
  }
  return Math.max(1, Math.ceil(result.length / props.itemsPerPage));
});

const paginationPages = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;
  if (totalPages.value <= maxVisiblePages) {
    for (let i = 1; i <= totalPages.value; i++) pages.push(i);
  } else {
    let startPage = Math.max(
      1,
      currentPage.value - Math.floor(maxVisiblePages / 2)
    );
    let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1);
    if (endPage === totalPages.value) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    for (let i = startPage; i <= endPage; i++) pages.push(i);
  }
  return pages;
});

const toggleFilter = (filterId) => {
  const index = activeFilters.value.indexOf(filterId);
  if (index === -1) {
    activeFilters.value.push(filterId);
  } else {
    activeFilters.value.splice(index, 1);
  }
  currentPage.value = 1;
};

const toggleFavorite = async (propertyId) => {
  const token = localStorage.getItem("auth_token");
  if (!token) return; // No permitir si no está logueado
  try {
    await axios.post(
      `${API_BASE_URL}/user/toggle-favorite`,
      { propertyId },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    await fetchProperties();
    emit("toggleFavorite", propertyId);
  } catch (e) {
    console.error("Error al cambiar favorito:", e);
  }
};

const viewProperty = (propertyId) => {
  emit("viewProperty", propertyId);
  router.push(`/renters/property/${propertyId}`);
};

const changePage = (page) => {
  currentPage.value = page;
  emit("changePage", page);
};
</script>

<template>
  <div class="property-list-container">
    <div class="list-header">
      <h2 class="list-title">{{ title }}</h2>
      <div class="list-filters" v-if="showFilters">
        <div class="filter-group">
          <label>Ordenar por:</label>
          <select v-model="sortBy" class="filter-select">
            <option value="price_asc">Precio: menor a mayor</option>
            <option value="price_desc">Precio: mayor a menor</option>
            <option value="rating">Mejor valorados</option>
            <option value="newest">Más recientes</option>
          </select>
        </div>
        <!--         <div class="filter-group">
          <label>Filtrar por:</label>
          <div class="filter-buttons">
            <button 
              v-for="filter in filterOptions" 
              :key="filter.id" 
              class="filter-button" 
              :class="{ active: activeFilters.includes(filter.id) }"
              @click="toggleFilter(filter.id)"
            >
              {{ filter.name }}
            </button>
          </div>
        </div> -->
      </div>
    </div>
    <div v-if="filteredProperties.length > 0" class="properties-grid">
      <RendersPropertyCard
        v-for="property in filteredProperties"
        :key="property.id"
        :property="property"
        :properties="properties"
        :favorite="favorites.includes(property.id)"
        @view="() => viewProperty(property.id)"
        @toggleFavorite="() => toggleFavorite(property.id)"
      />
    </div>
    <div v-else class="empty-state">
      <div class="empty-illustration">
        <HomeIcon class="empty-icon" />
        <div class="empty-waves"></div>
      </div>
      <h3>No se encontraron propiedades</h3>
      <p>Intenta cambiar los filtros o busca en otra ubicación</p>
    </div>
    <div v-if="showPagination && totalPages > 1" class="pagination">
      <button
        class="pagination-button"
        :disabled="currentPage === 1"
        @click="changePage(currentPage - 1)"
      >
        <ChevronLeftIcon class="pagination-icon" />
      </button>
      <div class="pagination-pages">
        <button
          v-for="page in paginationPages"
          :key="page"
          class="page-button"
          :class="{ active: currentPage === page }"
          @click="changePage(page)"
        >
          {{ page }}
        </button>
      </div>
      <button
        class="pagination-button"
        :disabled="currentPage === totalPages"
        @click="changePage(currentPage + 1)"
      >
        <ChevronRightIcon class="pagination-icon" />
      </button>
    </div>
  </div>
</template>

<style scoped>
.property-list-container {
  margin-bottom: 3rem;
}
.list-header {
  margin-bottom: 2rem;
}
.list-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 1.5rem;
  position: relative;
}
.list-title::after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}
.list-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: center;
}
.filter-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.filter-group label {
  font-weight: 600;
  color: #003580;
}
.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  background-color: white;
  color: #1e293b;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
}
.filter-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}
.filter-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.filter-button {
  padding: 0.5rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 20px;
  background-color: white;
  color: #64748b;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s;
}
.filter-button:hover {
  background-color: #f0f7ff;
  color: #0071c2;
}
.filter-button.active {
  background-color: #e6f0ff;
  color: #0071c2;
  border-color: #0071c2;
}
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}
.empty-state {
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
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}
@keyframes wave {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}
.empty-state h3 {
  font-size: 1.4rem;
  margin-bottom: 0.5rem;
  color: #003580;
}
.empty-state p {
  color: #64748b;
  margin-bottom: 0;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
}
.pagination-button {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  background-color: white;
  color: #0071c2;
  cursor: pointer;
  transition: all 0.3s;
}
.pagination-button:hover:not(:disabled) {
  background-color: #f0f7ff;
}
.pagination-button:disabled {
  color: #94a3b8;
  cursor: not-allowed;
}
.pagination-icon {
  width: 18px;
  height: 18px;
}
.pagination-pages {
  display: flex;
  gap: 0.5rem;
}
.page-button {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  background-color: white;
  color: #1e293b;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}
.page-button:hover {
  background-color: #f0f7ff;
}
.page-button.active {
  background-color: #0071c2;
  color: white;
  border-color: #0071c2;
}
@media (max-width: 768px) {
  .list-filters {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  .filter-group {
    width: 100%;
  }
  .filter-select {
    width: 100%;
  }
  .properties-grid {
    grid-template-columns: 1fr;
  }
}
</style>