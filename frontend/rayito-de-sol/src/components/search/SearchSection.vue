<template>
  <div class="search-section">
    <div class="search-container">
      <div class="search-header">
        <h2 class="section-title">Buscar Alojamientos</h2>
      </div>

      <!-- Barra de búsqueda principal -->
      <div class="search-bar">
        <div class="search-input-wrapper">
          <SearchIcon class="search-icon" />
          <input
            type="text"
            class="search-input"
            placeholder="Buscar por ubicación, nombre o tipo de propiedad..."
            :value="searchQuery"
            @input="$emit('update-search', $event.target.value)"
            @keyup.enter="$emit('search')"
          />
          <button
            v-if="searchQuery"
            class="clear-search-button"
            @click="$emit('update-search', '')"
          >
            <XIcon class="clear-icon" />
          </button>
        </div>
        <button class="search-button" @click="$emit('search')">
          <SearchIcon class="button-icon" />
          <span>Buscar</span>
        </button>
      </div>

      <!-- Filtros básicos -->
      <div class="basic-filters">
        <div class="filter-group date-filters">
          <div class="filter-item">
            <label>Llegada</label>
            <div class="date-input">
              <CalendarIcon class="filter-icon" />
              <input
                type="date"
                :min="today"
                :value="filters.checkIn"
                @input="updateFilter('checkIn', $event.target.value)"
              />
            </div>
          </div>
          <div class="filter-item">
            <label>Salida</label>
            <div class="date-input">
              <CalendarIcon class="filter-icon" />
              <input
                type="date"
                :min="filters.checkIn || today"
                :value="filters.checkOut"
                @input="updateFilter('checkOut', $event.target.value)"
              />
            </div>
          </div>
        </div>

        <div class="filter-group">
          <div class="filter-item">
            <label>Huéspedes</label>
            <div class="number-input">
              <UsersIcon class="filter-icon" />
              <input
                type="number"
                min="1"
                max="20"
                :value="filters.guests"
                @input="
                  updateFilter('guests', parseInt($event.target.value) || 1)
                "
              />
            </div>
          </div>
          <div class="filter-item">
            <label>Precio máx. (€)</label>
            <div class="number-input">
              <EuroIcon class="filter-icon" />
              <input
                type="number"
                min="0"
                step="10"
                :value="filters.maxPrice"
                @input="
                  updateFilter('maxPrice', parseInt($event.target.value) || 0)
                "
              />
            </div>
          </div>
        </div>

        <div class="filter-actions">
          <button
            class="advanced-filter-toggle"
            @click="$emit('toggle-advanced')"
          >
            <SlidersIcon class="button-icon" />
            {{ showAdvancedFilters ? "Ocultar filtros" : "Más filtros" }}
          </button>
          <button class="clear-filters-button" @click="$emit('clear-filters')">
            <RefreshCwIcon class="button-icon" />
            Limpiar
          </button>
        </div>
      </div>

      <!-- Filtros avanzados -->
      <div v-if="showAdvancedFilters" class="advanced-filters">
        <div class="advanced-filters-header">
          <h3>Filtros avanzados</h3>
        </div>

        <div class="advanced-filters-content">
          <div class="filter-section">
            <h4>Tipo de propiedad</h4>
            <div class="checkbox-group">
              <label class="checkbox-label">
                <input
                  type="checkbox"
                  :checked="filters.propertyTypes?.includes('apartment')"
                  @change="togglePropertyType('apartment')"
                />
                <span>Apartamento</span>
              </label>
              <label class="checkbox-label">
                <input
                  type="checkbox"
                  :checked="filters.propertyTypes?.includes('house')"
                  @change="togglePropertyType('house')"
                />
                <span>Casa</span>
              </label>
              <label class="checkbox-label">
                <input
                  type="checkbox"
                  :checked="filters.propertyTypes?.includes('villa')"
                  @change="togglePropertyType('villa')"
                />
                <span>Villa</span>
              </label>
              <label class="checkbox-label">
                <input
                  type="checkbox"
                  :checked="filters.propertyTypes?.includes('studio')"
                  @change="togglePropertyType('studio')"
                />
                <span>Estudio</span>
              </label>
            </div>
          </div>

          <div class="filter-section">
            <h4>Habitaciones y baños</h4>
            <div class="rooms-filters">
              <div class="filter-item">
                <label>Habitaciones</label>
                <div class="number-input">
                  <BedIcon class="filter-icon" />
                  <input
                    type="number"
                    min="0"
                    :value="filters.bedrooms"
                    @input="
                      updateFilter(
                        'bedrooms',
                        parseInt($event.target.value) || 0
                      )
                    "
                  />
                </div>
              </div>
              <div class="filter-item">
                <label>Baños</label>
                <div class="number-input">
                  <ShowerHeadIcon class="filter-icon" />
                  <input
                    type="number"
                    min="0"
                    :value="filters.bathrooms"
                    @input="
                      updateFilter(
                        'bathrooms',
                        parseInt($event.target.value) || 0
                      )
                    "
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="filter-section">
            <h4>Comodidades</h4>
            <div class="amenities-grid">
              <label
                v-for="amenity in amenities"
                :key="amenity.id"
                class="checkbox-label"
              >
                <input
                  type="checkbox"
                  :checked="filters.amenities?.includes(amenity.id)"
                  @change="toggleAmenity(amenity.id)"
                />
                <span>{{ amenity.name }}</span>
              </label>
            </div>
          </div>
        </div>

        <div class="advanced-filters-footer">
          <button class="apply-filters-button" @click="$emit('apply-filters')">
            Aplicar filtros
          </button>
        </div>
      </div>

      <!-- Resultados de búsqueda -->
      <div class="search-results">
        <div v-if="isLoading" class="loading-state">
          <div class="spinner"></div>
          <p>Buscando propiedades...</p>
        </div>

        <div v-else-if="properties.length === 0" class="empty-state">
          <SearchXIcon class="empty-icon" />
          <h3>No se encontraron propiedades</h3>
          <p>
            Intenta con otros criterios de búsqueda o ajusta los filtros para
            encontrar más opciones.
          </p>
        </div>

        <div v-else class="results-grid">
          <div
            v-for="property in properties"
            :key="property.id"
            class="property-card"
            @click="$emit('view-property', property.id)"
          >
            <div class="property-image-container">
              <img
                :src="property.image || '/img/placeholder.jpg'"
                :alt="property.name"
                class="property-image"
              />
              <button
                class="favorite-button"
                :class="{ active: isFavorite(property.id) }"
                @click.stop="handleFavoriteClick(property)"
              >
                <HeartIcon class="heart-icon" />
              </button>
              <div v-if="property.discount" class="discount-badge">
                {{ property.discount }}% OFF
              </div>
            </div>

            <div class="property-content">
              <div class="property-header">
                <h3 class="property-name">{{ property.name }}</h3>
                <div class="property-rating">
                  <StarIcon class="star-icon" />
                  <span>{{ getAvgRating(property) }}</span>
                </div>
              </div>

              <p class="property-location">
                <MapPinIcon class="location-icon" />
                {{ property.location }}
              </p>

              <div class="property-features">
                <span class="feature">
                  <BedIcon class="feature-icon" />
                  {{ property.bedrooms || 1 }} hab.
                </span>
                <span class="feature">
                  <UsersIcon class="feature-icon" />
                  {{ property.capacity || 2 }} huésp.
                </span>
                <span class="feature">
                  <ShowerHeadIcon class="feature-icon" />
                  {{ property.bathrooms || 1 }} baños
                </span>
              </div>

              <div class="property-footer">
                <div class="property-price">
                  <span class="price">{{ property.price }}€</span>
                  <span class="price-period">noche</span>
                </div>
                <button class="view-button">Ver detalles</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  SearchIcon,
  XIcon,
  CalendarIcon,
  UsersIcon,
  EuroIcon,
  SlidersIcon,
  RefreshCwIcon,
  BedIcon,
  ShowerHeadIcon,
  MapPinIcon,
  HeartIcon,
  StarIcon,
  SearchXIcon,
} from "lucide-vue-next";
import { useFavorites } from "@/helpers/favoriteManager";
import { computed } from "vue";

const { isFavorite, toggleFavorite, saveFavoriteSummary, removeFavoriteSummary } = useFavorites();

// Props
const props = defineProps({
  properties: {
    type: Array,
    default: () => [],
  },
  searchQuery: {
    type: String,
    default: "",
  },
  filters: {
    type: Object,
    default: () => ({
      checkIn: "",
      checkOut: "",
      guests: 1,
      maxPrice: 0,
      bedrooms: 0,
      bathrooms: 0,
      propertyTypes: [],
      amenities: [],
    }),
  },
  showAdvancedFilters: {
    type: Boolean,
    default: false,
  },
  amenities: {
    type: Array,
    default: () => [
      { id: 1, name: "WiFi" },
      { id: 2, name: "Piscina" },
      { id: 3, name: "Cocina" },
      { id: 4, name: "Aire acondicionado" },
      { id: 5, name: "Calefacción" },
      { id: 6, name: "Lavadora" },
      { id: 7, name: "TV" },
      { id: 8, name: "Parking" },
      { id: 9, name: "Terraza" },
      { id: 10, name: "Gimnasio" },
      { id: 11, name: "Desayuno" },
      { id: 12, name: "Admite mascotas" },
    ],
  },
  today: {
    type: String,
    default: () => new Date().toISOString().split("T")[0],
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  isFavorite: {
    type: Function,
    default: () => false,
  },
  toggleFavorite: {
    type: Function,
    default: () => {},
  },
});

// Emits
const emit = defineEmits([
  "update-search",
  "update-filters",
  "toggle-advanced",
  "clear-filters",
  "search",
  "apply-filters",
  "view-property",
]);

// Métodos
const updateFilter = (key, value) => {
  emit("update-filters", { [key]: value });
};

function getAvgRating(property) {
  console.log("reviews", property.reviews); // ← aquí va en plural

  if (!property.reviews || !property.reviews.length) return 4.5;
  const sum = property.reviews.reduce((acc, r) => acc + r.rating, 0);
  return (sum / property.reviews.length).toFixed(1);
}

const avgRating = computed(() => {
  if (!property.reviews || !property.reviews.length) return 4.5;
  const sum = property.reviews.reduce((acc, r) => acc + r.rating, 0);
  return (sum / property.reviews.length).toFixed(1);
});

const togglePropertyType = (type) => {
  const currentTypes = [...(props.filters.propertyTypes || [])];
  const index = currentTypes.indexOf(type);

  if (index === -1) {
    currentTypes.push(type);
  } else {
    currentTypes.splice(index, 1);
  }

  emit("update-filters", { propertyTypes: currentTypes });
};


function handleFavoriteClick(property) {
  if (!isFavorite(property.id)) {
    toggleFavorite(property.id);
    saveFavoriteSummary(property); // Guarda el resumen con la info básica
  } else {
    toggleFavorite(property.id);
    removeFavoriteSummary(property.id); // Borra el resumen si quitas el favorito
  }
}

const toggleAmenity = (amenityId) => {
  const currentAmenities = [...(props.filters.amenities || [])];
  const index = currentAmenities.indexOf(amenityId);

  if (index === -1) {
    currentAmenities.push(amenityId);
  } else {
    currentAmenities.splice(index, 1);
  }

  emit("update-filters", { amenities: currentAmenities });
};

defineExpose({
  isFavorite,
  toggleFavorite,
});
</script>

<style scoped>
.search-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 60vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  position: relative;
}

.search-header {
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0;
  position: relative;
}

.section-title::after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

/* Barra de búsqueda */
.search-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.search-input-wrapper {
  position: relative;
  flex: 1;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #64748b;
}

.search-input {
  width: 100%;
  padding: 0.75rem 2.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 1rem;
  color: #1e293b;
  background-color: white;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.2);
}

.clear-search-button {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  color: #94a3b8;
}

.clear-icon {
  width: 16px;
  height: 16px;
}

.search-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0 1.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.search-button:hover {
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.2);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Filtros básicos */
.basic-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
}

.filter-group {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.date-filters {
  flex: 2;
  min-width: 300px;
}

.filter-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
  min-width: 140px;
}

.filter-item label {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 500;
}

.date-input,
.number-input {
  position: relative;
}

.filter-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #64748b;
}

.date-input input,
.number-input input {
  width: 100%;
  padding: 0.6rem 0.75rem 0.6rem 2.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
}

.date-input input:focus,
.number-input input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

.filter-actions {
  display: flex;
  align-items: flex-end;
  gap: 1rem;
  margin-left: auto;
}

.advanced-filter-toggle,
.clear-filters-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.advanced-filter-toggle {
  background-color: #f0f7ff;
  color: #0071c2;
  border: 1px solid #e6f0ff;
}

.advanced-filter-toggle:hover {
  background-color: #e6f0ff;
}

.clear-filters-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #e6f0ff;
}

.clear-filters-button:hover {
  background-color: #f8fafc;
  color: #475569;
}

/* Filtros avanzados */
.advanced-filters {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  margin-bottom: 1.5rem;
  overflow: hidden;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.advanced-filters-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.advanced-filters-header h3 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0;
}

.advanced-filters-content {
  padding: 1.5rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.filter-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.filter-section h4 {
  font-size: 1rem;
  color: #1e293b;
  margin: 0;
}

.checkbox-group {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.95rem;
  color: #475569;
  padding: 0.25rem 0;
  min-width: 120px;
}

.checkbox-label input {
  width: 16px;
  height: 16px;
  accent-color: #0071c2;
}

.rooms-filters {
  display: flex;
  gap: 1rem;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 0.75rem;
}

.advanced-filters-footer {
  padding: 1.25rem 1.5rem;
  border-top: 1px solid #e6f0ff;
  display: flex;
  justify-content: flex-end;
}

.apply-filters-button {
  padding: 0.6rem 1.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.apply-filters-button:hover {
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.2);
}

/* Resultados de búsqueda */
.search-results {
  margin-top: 2rem;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e6f0ff;
  border-top: 4px solid #0071c2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.loading-state p {
  color: #64748b;
  font-size: 1rem;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
  text-align: center;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  font-size: 1.2rem;
  color: #1e293b;
  margin: 0 0 0.5rem;
}

.empty-state p {
  color: #64748b;
  max-width: 400px;
  margin: 0;
}

.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.property-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 53, 128, 0.1);
}

.property-image-container {
  position: relative;
  height: 200px;
}

.property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.favorite-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.8);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.favorite-button:hover {
  background-color: white;
  transform: scale(1.1);
}

.heart-icon {
  width: 20px;
  height: 20px;
  color: #94a3b8;
  transition: all 0.2s;
}

.favorite-button.active .heart-icon {
  color: #ef4444;
  fill: #ef4444;
}

.discount-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background-color: #ef4444;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.property-content {
  padding: 1.25rem;
}

.property-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.property-name {
  font-size: 1.1rem;
  color: #003580;
  margin: 0;
  flex: 1;
}

.property-rating {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  background-color: #003580;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
}

.star-icon {
  width: 14px;
  height: 14px;
  color: #fbbf24;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  color: #64748b;
  margin: 0 0 1rem;
}

.location-icon {
  width: 14px;
  height: 14px;
  color: #64748b;
}

.property-features {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.85rem;
  color: #475569;
}

.feature-icon {
  width: 14px;
  height: 14px;
  color: #64748b;
}

.property-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.property-price {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
}

.price {
  font-size: 1.2rem;
  font-weight: 700;
  color: #003580;
}

.price-period {
  font-size: 0.85rem;
  color: #64748b;
}

.view-button {
  padding: 0.5rem 1rem;
  background-color: #f0f7ff;
  color: #0071c2;
  border: 1px solid #e6f0ff;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.view-button:hover {
  background-color: #e6f0ff;
  color: #003580;
}

/* Responsive */
@media (max-width: 768px) {
  .search-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }

  .search-bar {
    flex-direction: column;
  }

  .basic-filters {
    flex-direction: column;
    gap: 1rem;
  }

  .filter-group {
    width: 100%;
  }

  .filter-actions {
    width: 100%;
    margin-left: 0;
    justify-content: space-between;
  }

  .advanced-filters-content {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .results-grid {
    grid-template-columns: 1fr;
  }
}
</style>