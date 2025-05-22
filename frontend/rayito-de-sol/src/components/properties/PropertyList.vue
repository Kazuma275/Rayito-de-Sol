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
        
        <div class="filter-group">
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
        </div>
      </div>
    </div>
    
    <div v-if="filteredProperties.length > 0" class="properties-grid">
      <RendersPropertyCard 
        v-for="property in filteredProperties" 
        :key="property.id" 
        :property="property" 
        :favorites="favorites"
        :amenities="amenities"
        @toggle-favorite="toggleFavorite"
        @view-property="viewProperty"
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

<script setup>
import { ref, computed } from 'vue';
import { HomeIcon, ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';
import RendersPropertyCard from './RendersPropertyCard.vue';

const props = defineProps({
  properties: {
    type: Array,
    required: true
  },
  favorites: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: 'Propiedades'
  },
  showFilters: {
    type: Boolean,
    default: true
  },
  showPagination: {
    type: Boolean,
    default: true
  },
  itemsPerPage: {
    type: Number,
    default: 6
  }
});

const emit = defineEmits(['toggleFavorite', 'viewProperty', 'changePage']);

// Filter and sort options
const sortBy = ref('price_asc');
const activeFilters = ref([]);
const currentPage = ref(1);

// Sample filter options
const filterOptions = [
  { id: 'pool', name: 'Piscina' },
  { id: 'beach', name: 'Playa' },
  { id: 'wifi', name: 'Wi-Fi' },
  { id: 'parking', name: 'Parking' }
];

// Sample amenities for the property cards
const amenities = [
  { id: 1, name: 'Wi-Fi' },
  { id: 2, name: 'Piscina' },
  { id: 3, name: 'Aire acondicionado' },
  { id: 4, name: 'Cocina equipada' },
  { id: 5, name: 'Lavadora' },
  { id: 6, name: 'TV' },
  { id: 7, name: 'Terraza' },
  { id: 8, name: 'Parking' }
];

// Computed properties
const filteredProperties = computed(() => {
  let result = [...props.properties];
  
  // Apply filters (in a real app, this would be more sophisticated)
  if (activeFilters.value.length > 0) {
    // This is just a placeholder. In a real app, you'd filter based on property amenities
    result = result.filter(property => 
      activeFilters.value.some(filter => 
        property.amenities && property.amenities.includes(filter)
      )
    );
  }
  
  // Apply sorting
  result.sort((a, b) => {
    switch (sortBy.value) {
      case 'price_asc':
        return a.price - b.price;
      case 'price_desc':
        return b.price - a.price;
      case 'rating':
        return (b.rating || 0) - (a.rating || 0);
      case 'newest':
        return new Date(b.createdAt || 0) - new Date(a.createdAt || 0);
      default:
        return 0;
    }
  });
  
  // Apply pagination
  const startIndex = (currentPage.value - 1) * props.itemsPerPage;
  const endIndex = startIndex + props.itemsPerPage;
  
  return result.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(props.properties.length / props.itemsPerPage);
});

const paginationPages = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;
  
  if (totalPages.value <= maxVisiblePages) {
    // Show all pages if there are few
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i);
    }
  } else {
    // Show a subset of pages with current page in the middle
    let startPage = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1);
    
    // Adjust if we're near the end
    if (endPage === totalPages.value) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
  }
  
  return pages;
});

// Methods
const toggleFilter = (filterId) => {
  const index = activeFilters.value.indexOf(filterId);
  if (index === -1) {
    activeFilters.value.push(filterId);
  } else {
    activeFilters.value.splice(index, 1);
  }
  
  // Reset to first page when filters change
  currentPage.value = 1;
};

const toggleFavorite = (propertyId) => {
  emit('toggleFavorite', propertyId);
};

const viewProperty = (propertyId) => {
  emit('viewProperty', propertyId);
};

const changePage = (page) => {
  currentPage.value = page;
  emit('changePage', page);
};
</script>

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
  content: '';
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
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes wave {
  0%, 100% {
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