<template>
  <div class="property-search">
    <div class="search-container">
      <div class="search-header">
        <h2 class="search-title">Encuentra tu alojamiento perfecto</h2>
        <p class="search-subtitle">Explora nuestras propiedades y encuentra el lugar ideal para tus vacaciones</p>
      </div>
      
      <div class="search-form">
        <div class="search-input-group">
          <label>Destino</label>
          <div class="input-wrapper">
            <MapPinIcon class="input-icon" />
            <input 
              type="text" 
              v-model="searchParams.location" 
              placeholder="¿A dónde quieres ir?" 
              class="search-input"
            />
          </div>
        </div>
        
        <div class="search-input-group">
          <label>Fechas</label>
          <div class="dates-wrapper">
            <div class="input-wrapper date">
              <CalendarIcon class="input-icon" />
              <input 
                type="date" 
                v-model="searchParams.checkIn" 
                class="search-input"
              />
            </div>
            <div class="date-separator">-</div>
            <div class="input-wrapper date">
              <CalendarIcon class="input-icon" />
              <input 
                type="date" 
                v-model="searchParams.checkOut" 
                class="search-input"
              />
            </div>
          </div>
        </div>
        
        <div class="search-input-group guests-group">
          <label>Huéspedes</label>
          <div class="input-wrapper">
            <UsersIcon class="input-icon" />
            <select v-model="searchParams.guests" class="search-input">
              <option v-for="n in 10" :key="n" :value="n">{{ n }} {{ n === 1 ? 'huésped' : 'huéspedes' }}</option>
            </select>
          </div>
        </div>
        
        <button class="search-button" @click="search">
          <SearchIcon class="button-icon" />
          Buscar
        </button>
      </div>
    </div>
    
    <div class="search-filters" v-if="showAdvancedFilters">
      <div class="filter-section">
        <h3 class="filter-title">Filtros</h3>
        
        <div class="filter-group">
          <h4>Precio por noche</h4>
          <div class="price-range">
            <div class="price-input">
              <span class="currency">€</span>
              <input 
                type="number" 
                v-model="filters.minPrice" 
                placeholder="Mín" 
                class="range-input"
              />
            </div>
            <div class="range-separator">-</div>
            <div class="price-input">
              <span class="currency">€</span>
              <input 
                type="number" 
                v-model="filters.maxPrice" 
                placeholder="Máx" 
                class="range-input"
              />
            </div>
          </div>
        </div>
        
        <div class="filter-group">
          <h4>Tipo de alojamiento</h4>
          <div class="checkbox-group">
            <label class="checkbox-label" v-for="type in accommodationTypes" :key="type.id">
              <input 
                type="checkbox" 
                :value="type.id" 
                v-model="filters.types"
              />
              <span>{{ type.name }}</span>
            </label>
          </div>
        </div>
        
        <div class="filter-group">
          <h4>Servicios</h4>
          <div class="checkbox-group amenities">
            <label class="checkbox-label" v-for="amenity in amenities" :key="amenity.id">
              <input 
                type="checkbox" 
                :value="amenity.id" 
                v-model="filters.amenities"
              />
              <span>{{ amenity.name }}</span>
            </label>
          </div>
        </div>
        
        <div class="filter-actions">
          <button class="clear-button" @click="clearFilters">Limpiar filtros</button>
          <button class="apply-button" @click="applyFilters">Aplicar filtros</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { MapPinIcon, CalendarIcon, UsersIcon, SearchIcon } from 'lucide-vue-next';

const props = defineProps({
  showAdvancedFilters: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['search', 'applyFilters']);

// Search parameters
const searchParams = ref({
  location: '',
  checkIn: '',
  checkOut: '',
  guests: 2
});

// Advanced filters
const filters = ref({
  minPrice: null,
  maxPrice: null,
  types: [],
  amenities: []
});

// Sample data for filters
const accommodationTypes = [
  { id: 'apartment', name: 'Apartamento' },
  { id: 'house', name: 'Casa' },
  { id: 'villa', name: 'Villa' },
  { id: 'studio', name: 'Estudio' }
];

const amenities = [
  { id: 'wifi', name: 'Wi-Fi' },
  { id: 'pool', name: 'Piscina' },
  { id: 'ac', name: 'Aire acondicionado' },
  { id: 'kitchen', name: 'Cocina equipada' },
  { id: 'washer', name: 'Lavadora' },
  { id: 'tv', name: 'TV' },
  { id: 'terrace', name: 'Terraza' },
  { id: 'parking', name: 'Parking' }
];

// Methods
const search = () => {
  emit('search', { ...searchParams.value });
};

const applyFilters = () => {
  emit('applyFilters', { ...filters.value });
};

const clearFilters = () => {
  filters.value = {
    minPrice: null,
    maxPrice: null,
    types: [],
    amenities: []
  };
};
</script>

<style scoped>
.property-search {
  margin-bottom: 3rem;
}

.search-container {
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  border-radius: 16px;
  padding: 2rem;
  color: white;
  box-shadow: 0 8px 20px rgba(0, 53, 128, 0.2);
  position: relative;
  overflow: hidden;
}

.search-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(0, 113, 194, 0.4) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(0, 53, 128, 0.3) 0%, transparent 60%);
  z-index: 0;
}

.search-header {
  position: relative;
  z-index: 1;
  text-align: center;
  margin-bottom: 2rem;
}

.search-title {
  font-size: 2rem;
  margin: 0 0 0.5rem;
  font-weight: 700;
}

.search-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  max-width: 600px;
  margin: 0 auto;
}

.search-form {
  position: relative;
  z-index: 1;
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  background-color: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.search-input-group {
  flex: 1;
  min-width: 200px;
}

/* Ajuste específico para el grupo de huéspedes */
.guests-group {
  flex: 0 0 auto;
  min-width: 150px;
  width: auto;
}

.search-input-group label {
  display: block;
  font-weight: 600;
  color: #003580;
  margin-bottom: 0.5rem;
}

.input-wrapper {
  position: relative;
}

.dates-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.input-wrapper.date {
  flex: 1;
}

.date-separator {
  color: #64748b;
  font-weight: 500;
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

.search-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.5rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
  color: #1e293b;
}

.search-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.search-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
  align-self: flex-end;
  height: 48px;
  min-width: 120px;
}

.search-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
}

.search-filters {
  margin-top: 2rem;
}

.filter-section {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.filter-title {
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 1.5rem;
  position: relative;
}

.filter-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 40px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.filter-group {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.filter-group:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.filter-group h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.price-range {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.price-input {
  position: relative;
  flex: 1;
}

.currency {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  font-weight: 500;
}

.range-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
}

.range-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.range-separator {
  color: #64748b;
  font-weight: 500;
}

.checkbox-group {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 0.75rem;
}

.checkbox-group.amenities {
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-label input {
  width: 18px;
  height: 18px;
  accent-color: #0071c2;
}

.filter-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.clear-button {
  padding: 0.75rem 1.25rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  background-color: white;
  color: #64748b;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.clear-button:hover {
  background-color: #f8fafc;
  color: #1e293b;
}

.apply-button {
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.apply-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

@media (max-width: 992px) {
  .search-form {
    flex-direction: column;
  }
  
  .search-button {
    width: 100%;
    margin-top: 0.5rem;
  }
  
  .dates-wrapper {
    flex-direction: column;
    gap: 1rem;
  }
  
  .date-separator {
    display: none;
  }
  
  .guests-group {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .search-container {
    padding: 1.5rem;
  }
  
  .search-title {
    font-size: 1.6rem;
  }
  
  .search-subtitle {
    font-size: 1rem;
  }
  
  .checkbox-group, .checkbox-group.amenities {
    grid-template-columns: 1fr;
  }
  
  .filter-actions {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .clear-button, .apply-button {
    width: 100%;
  }
}
</style>