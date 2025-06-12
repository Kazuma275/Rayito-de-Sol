<template>
  <div class="favorites-section">
    <div class="section-header">
      <h2 class="section-title">Mis Favoritos</h2>
      <div class="header-actions">
        <button
          v-if="favorites.length > 0"
          class="clear-all-button"
          @click="showClearAllModal = true"
        >
          <TrashIcon class="button-icon" />
          Limpiar todo
        </button>
      </div>
    </div>

    <!-- Search and Filters -->
    <div v-if="favorites.length > 0" class="favorites-controls">
      <div class="search-container">
        <div class="search-bar">
          <SearchIcon class="search-icon" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar en favoritos..."
            class="search-input"
          />
          <button v-if="searchQuery" @click="clearSearch" class="clear-search">
            <XIcon class="clear-icon" />
          </button>
        </div>
      </div>

      <div class="filters-container">
        <div class="filter-group">
          <label for="sortBy">Ordenar por:</label>
          <select id="sortBy" v-model="sortBy" class="filter-select">
            <option value="date-desc">Añadido recientemente</option>
            <option value="date-asc">Añadido hace tiempo</option>
            <option value="price-asc">Precio menor</option>
            <option value="price-desc">Precio mayor</option>
            <option value="rating-desc">Mejor valorado</option>
            <option value="name-asc">Nombre A-Z</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="filterBy">Filtrar por:</label>
          <select id="filterBy" v-model="filterBy" class="filter-select">
            <option value="all">Todos</option>
            <option value="available">Disponibles</option>
            <option value="unavailable">No disponibles</option>
            <option value="recently-viewed">Vistos recientemente</option>
          </select>
        </div>

        <div class="results-count">
          {{ filteredFavorites.length }} de {{ favorites.length }} propiedades
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Cargando favoritos...</p>
    </div>

    <!-- Favorites Grid -->
    <div v-else-if="filteredFavorites.length > 0" class="favorites-grid">
      <div
        v-for="property in filteredFavorites"
        :key="property.id"
        class="favorite-card"
        :class="{ unavailable: !property.available }"
      >
        <!-- Property Image -->
        <div class="property-image-container">
          <img
            :src="getPropertyImage(property)"
            :alt="property.name"
            class="property-image"
            @click="openPropertyModal(property)"
            @error="handleImageError"
          />

          <!-- Availability Badge -->
          <div
            class="availability-badge"
            :class="{ available: property.available }"
          >
            {{ property.available ? "Disponible" : "No disponible" }}
          </div>

          <!-- Favorite Button -->
          <button
            class="favorite-button active"
            @click="removeFavorite(property.id)"
            title="Quitar de favoritos"
          >
            <HeartIcon class="heart-icon filled" />
          </button>

          <!-- Quick Actions -->
          <div class="quick-actions">
            <button
              class="quick-action-button"
              @click="openImageModal(getPropertyImage(property), property.name)"
              title="Ver imagen en grande"
            >
              <EyeIcon class="action-icon" />
            </button>
            <button
              class="quick-action-button"
              @click="shareProperty(property)"
              title="Compartir"
            >
              <ShareIcon class="action-icon" />
            </button>
            <button
              class="quick-action-button remove"
              @click="removeSingleFavorite(property.id)"
              title="Eliminar de favoritos"
            >
              <TrashIcon class="action-icon" />
            </button>
          </div>
        </div>

        <!-- Property Info -->
        <div class="property-info" @click="openPropertyModal(property)">
          <div class="property-header">
            <h3 class="property-name">{{ property.name }}</h3>
            <div class="property-rating" v-if="property.rating && property.rating > 0">
              <StarIcon class="star-icon" />
              <span class="rating-value">{{ calculateRating(property.rating) }}</span>
              <span class="rating-count">({{ property.reviewCount || 0 }})</span>
            </div>
          </div>

          <div class="property-location">
            <MapPinIcon class="location-icon" />
            <span>{{ property.location }}</span>
          </div>

          <div class="property-features">
            <div class="feature" v-if="property.bedrooms > 0">
              <BedIcon class="feature-icon" />
              <span>{{ property.bedrooms }} dormitorios</span>
            </div>
            <div class="feature" v-if="property.maxGuests > 0">
              <UsersIcon class="feature-icon" />
              <span>{{ property.maxGuests }} huéspedes</span>
            </div>
            <div class="feature" v-if="property.bathrooms > 0">
              <BathIcon class="feature-icon" />
              <span>{{ property.bathrooms }} baños</span>
            </div>
          </div>

          <div class="property-amenities" v-if="property.amenities && property.amenities.length > 0">
            <span
              v-for="amenity in property.amenities.slice(0, 3)"
              :key="amenity"
              class="amenity-tag"
            >
              {{ getAmenityName(amenity) }}
            </span>
            <span
              v-if="property.amenities.length > 3"
              class="amenity-tag more"
            >
              +{{ property.amenities.length - 3 }} más
            </span>
          </div>

          <div class="property-footer">
            <div class="property-price">
              <span class="price-amount">€{{ property.pricePerNight || 0 }}</span>
              <span class="price-period">/ noche</span>
            </div>
            <div class="added-date">
              Añadido {{ formatDate(property.addedToFavorites) }}
            </div>
          </div>
        </div>

        <!-- Property Actions -->
        <div class="property-actions">
          <button
            class="action-button primary"
            @click="openPropertyModal(property)"
            :disabled="!property.available"
          >
            <CalendarIcon class="action-icon" />
            {{ property.available ? "Ver disponibilidad" : "Ver detalles" }}
          </button>
          <button
            class="action-button secondary"
            @click="contactOwner(property)"
          >
            <MessageSquareIcon class="action-icon" />
            Contactar
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state">
      <div class="empty-icon-container">
        <HeartIcon class="empty-icon" />
      </div>
      <h3 class="empty-title">
        {{ searchQuery ? "No se encontraron favoritos" : "No tienes favoritos aún" }}
      </h3>
      <p class="empty-description">
        {{ searchQuery 
          ? "Prueba a cambiar los términos de búsqueda o filtros." 
          : "Cuando encuentres propiedades que te gusten, añádelas a favoritos para verlas aquí." 
        }}
      </p>
      <div class="empty-actions">
        <button
          v-if="searchQuery"
          @click="clearSearch"
          class="empty-action-button secondary"
        >
          Limpiar búsqueda
        </button>
<!--         <button @click="addSampleData" class="empty-action-button primary">
          <SearchIcon class="action-icon" />
          Añadir datos de ejemplo
        </button> -->
      </div>
    </div>

    <!-- Property Details Modal -->
    <div v-if="showPropertyModal" class="modal-overlay" @click="closePropertyModal">
      <div class="property-modal-content" @click.stop>
        <div class="property-modal-header">
          <h2>{{ selectedProperty?.name }}</h2>
          <button class="close-button" @click="closePropertyModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        
        <div class="property-modal-body">
          <!-- Image Gallery -->
          <div class="property-gallery">
            <div class="main-image-container">
              <img
                :src="getPropertyImage(selectedProperty)"
                :alt="selectedProperty?.name"
                class="main-image"
                @error="handleImageError"
              />
              <button
                class="expand-image-btn"
                @click="openImageModal(getPropertyImage(selectedProperty), selectedProperty?.name)"
                title="Ver imagen en grande"
              >
                <ExpandIcon class="expand-icon" />
              </button>
            </div>
            
            <!-- Thumbnail Gallery -->
            <div v-if="selectedProperty?.images && selectedProperty.images.length > 1" class="thumbnail-gallery">
              <div
                v-for="(image, index) in selectedProperty.images.slice(0, 4)"
                :key="index"
                class="thumbnail"
                @click="changeMainImage(image)"
              >
                <img :src="image" :alt="`${selectedProperty.name} - ${index + 1}`" @error="handleImageError" />
              </div>
            </div>
          </div>

          <!-- Property Details -->
          <div class="property-details">
            <div class="property-location-detail">
              <MapPinIcon class="location-icon" />
              <span>{{ selectedProperty?.location }}</span>
            </div>

            <div class="property-features-detail">
              <div class="feature-item">
                <BedIcon class="feature-icon" />
                <span>{{ selectedProperty?.bedrooms || 0 }} Dormitorios</span>
              </div>
              <div class="feature-item">
                <UsersIcon class="feature-icon" />
                <span>{{ selectedProperty?.maxGuests || 0 }} Huéspedes</span>
              </div>
              <div class="feature-item">
                <BathIcon class="feature-icon" />
                <span>{{ selectedProperty?.bathrooms || 0 }} Baños</span>
              </div>
            </div>

            <div class="property-rating-detail" v-if="selectedProperty?.rating && selectedProperty.rating > 0">
              <div class="rating-stars">
                <StarIcon
                  v-for="star in 5"
                  :key="star"
                  class="star"
                  :class="{ filled: star <= Math.round(selectedProperty.rating) }"
                />
              </div>
              <span class="rating-text">
                {{ calculateRating(selectedProperty.rating) }} ({{ selectedProperty?.reviewCount || 0 }} reseñas)
              </span>
            </div>

            <div class="property-description" v-if="selectedProperty?.description">
              <h3>Descripción</h3>
              <p>{{ selectedProperty.description }}</p>
            </div>

            <div class="property-amenities-detail" v-if="selectedProperty?.amenities && selectedProperty.amenities.length > 0">
              <h3>Servicios</h3>
              <div class="amenities-grid">
                <div
                  v-for="amenity in selectedProperty.amenities"
                  :key="amenity"
                  class="amenity-item"
                >
                  <component :is="getAmenityIcon(amenity)" class="amenity-icon" />
                  <span>{{ getAmenityName(amenity) }}</span>
                </div>
              </div>
            </div>

            <div class="property-price-detail">
              <div class="price-info">
                <span class="price-amount-large">€{{ selectedProperty?.pricePerNight || 0 }}</span>
                <span class="price-period">por noche</span>
              </div>
              <div class="availability-info">
                <div class="availability-status" :class="{ available: selectedProperty?.available }">
                  {{ selectedProperty?.available ? "Disponible" : "No disponible" }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="property-modal-footer">
          <button class="modal-action-button secondary" @click="shareProperty(selectedProperty)">
            <ShareIcon class="action-icon" />
            Compartir
          </button>
          <button class="modal-action-button secondary" @click="contactOwner(selectedProperty)">
            <MessageSquareIcon class="action-icon" />
            Contactar
          </button>
          <button
            class="modal-action-button primary"
            @click="openBookingModal(selectedProperty)"
            :disabled="!selectedProperty?.available"
          >
            <CalendarIcon class="action-icon" />
            {{ selectedProperty?.available ? "Reservar ahora" : "Ver más detalles" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="showImageModal" class="image-modal-overlay" @click="closeImageModal">
      <div class="image-modal-content" @click.stop>
        <button class="image-modal-close" @click="closeImageModal">
          <XIcon class="close-icon" />
        </button>
        <img :src="selectedImage" :alt="selectedImageAlt" class="modal-image" @error="handleImageError" />
        <div class="image-modal-caption">{{ selectedImageAlt }}</div>
      </div>
    </div>

    <!-- Clear All Confirmation Modal -->
    <div v-if="showClearAllModal" class="modal-overlay" @click="closeClearAllModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Confirmar acción</h3>
          <button class="close-button" @click="closeClearAllModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <div class="confirmation-content">
            <AlertTriangleIcon class="warning-icon" />
            <div class="confirmation-text">
              <h4>¿Estás seguro de que quieres eliminar todos los favoritos?</h4>
              <p>Esta acción no se puede deshacer. Se eliminarán {{ favorites.length }} propiedades de tu lista de favoritos.</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="cancel-button" @click="closeClearAllModal">
            Cancelar
          </button>
          <button class="confirm-button" @click="confirmClearAll">
            Eliminar todos
          </button>
        </div>
      </div>
    </div>

    <!-- Remove Single Favorite Modal -->
    <div v-if="showRemoveModal" class="modal-overlay" @click="closeRemoveModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Eliminar de favoritos</h3>
          <button class="close-button" @click="closeRemoveModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <div class="confirmation-content">
            <HeartIcon class="warning-icon" />
            <div class="confirmation-text">
              <h4>¿Quitar "{{ propertyToRemove?.name }}" de favoritos?</h4>
              <p>Podrás añadirla de nuevo cuando quieras.</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="cancel-button" @click="closeRemoveModal">
            Cancelar
          </button>
          <button class="confirm-button" @click="confirmRemoveSingle">
            Quitar de favoritos
          </button>
        </div>
      </div>
    </div>

    <!-- Booking Modal -->
    <BookingModal
      v-if="showBookingModal"
      :property="selectedPropertyForBooking"
      @close="closeBookingModal"
      @booking-success="handleBookingSuccess"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "axios";
import {
  TrashIcon,
  SearchIcon,
  XIcon,
  HeartIcon,
  EyeIcon,
  ShareIcon,
  MapPinIcon,
  StarIcon,
  BedIcon,
  UsersIcon,
  BathIcon,
  CalendarIcon,
  MessageSquareIcon,
  AlertTriangleIcon,
  ExpandIcon,
  WifiIcon,
  CarIcon,
  WavesIcon,
  DumbbellIcon,
  ChefHatIcon,
  AirVentIcon,
  ThermometerIcon,
  TvIcon,
  WashingMachineIcon,
  TreesIcon,
  PawPrintIcon
} from "lucide-vue-next";
import BookingModal from './BookingModal.vue';

// Estado reactivo
const favorites = ref([]);
const isLoading = ref(false);
const searchQuery = ref("");
const sortBy = ref("date-desc");
const filterBy = ref("all");
const showClearAllModal = ref(false);
const showRemoveModal = ref(false);
const propertyToRemove = ref(null);

// Estados para modales
const showPropertyModal = ref(false);
const selectedProperty = ref(null);
const showImageModal = ref(false);
const selectedImage = ref("");
const selectedImageAlt = ref("");

// Estados para modal de reserva
const showBookingModal = ref(false);
const selectedPropertyForBooking = ref(null);

// Remover estas líneas del estado reactivo:
// const currentMainImage = ref("");

const STORAGE_KEY = "favorites";

// Función para manejar errores de imagen
const handleImageError = (event) => {
  event.target.src = "/placeholder.svg?height=200&width=300&text=Imagen+no+disponible";
};

// Función para obtener imagen de propiedad con fallback mejorado
const getPropertyImage = (property) => {
  if (!property) return "/img/placeholder.jpg";
  
  // Usar directamente property.image como en tu otro componente
  if (property.image) {
    return property.image;
  }
  
  // Si no hay image, buscar en images array
  if (property.images && property.images.length > 0) {
    const validImages = property.images.filter(img => img && img.trim() !== "");
    if (validImages.length > 0) {
      return validImages[0];
    }
  }
  
  // Fallback a placeholder
  return "/img/placeholder.jpg";
};

// Función para obtener imágenes de la propiedad con mejor manejo
const getPropertyImages = (raw) => {
  const images = [];
  
  // Priorizar property.image
  if (raw.image && raw.image.trim()) {
    images.push(raw.image.trim());
  }
  
  // Añadir imágenes adicionales del array si existen
  if (raw.images && Array.isArray(raw.images) && raw.images.length > 0) {
    raw.images.forEach(img => {
      if (typeof img === 'string' && img.trim() && !images.includes(img.trim())) {
        images.push(img.trim());
      }
    });
  }
  
  // Si no hay imágenes válidas, usar placeholder
  if (images.length === 0) {
    images.push("/img/placeholder.jpg");
  }
  
  return images;
};

// Función para obtener icono de amenidad
const getAmenityIcon = (amenity) => {
  const iconMap = {
    wifi: WifiIcon,
    parking: CarIcon,
    pool: WavesIcon,
    gym: DumbbellIcon,
    kitchen: ChefHatIcon,
    ac: AirVentIcon,
    heating: ThermometerIcon,
    tv: TvIcon,
    washer: WashingMachineIcon,
    balcony: TreesIcon,
    garden: TreesIcon,
    pets: PawPrintIcon
  };
  return iconMap[amenity] || StarIcon;
};

// Función mejorada para normalizar propiedades
const normalizeProperty = (raw) => {
  return {
    id: raw.id || raw._id || Math.random().toString(36),
    name: raw.title || raw.name || raw.property_name || "Propiedad sin nombre",
    location: raw.location || raw.address || raw.city || "Ubicación no especificada",
    pricePerNight: parseFloat(raw.nightly_price || raw.price_per_night || raw.pricePerNight || raw.price || 0),
    bedrooms: parseInt(raw.bedrooms || raw.bedroom_count || raw.beds || 1),
    bathrooms: parseInt(raw.bathrooms || raw.bathroom_count || raw.baths || 1),
    maxGuests: parseInt(raw.max_guests || raw.maxGuests || raw.guests || raw.capacity || 2),
    rating: parseFloat(raw.rating || raw.average_rating || raw.score || 0),
    reviewCount: parseInt(raw.reviews_count || raw.reviewCount || raw.review_count || raw.total_reviews || 0),
    available: raw.status === "available" || raw.available === true || raw.is_available === true || true,
    image: raw.image || (raw.images && raw.images[0]) || "/img/placeholder.jpg", // Campo principal de imagen
    images: getPropertyImages(raw),
    amenities: getPropertyAmenities(raw),
    addedToFavorites: raw.added_to_favorites || raw.addedToFavorites || new Date().toISOString(),
    description: raw.description || raw.summary || "Esta hermosa propiedad ofrece una experiencia única con todas las comodidades necesarias para una estancia perfecta.",
    lastViewed: raw.lastViewed || raw.last_viewed
  };
};

// Función para obtener amenidades
const getPropertyAmenities = (raw) => {
  if (raw.amenities && Array.isArray(raw.amenities)) {
    return raw.amenities;
  }
  
  if (raw.features && Array.isArray(raw.features)) {
    return raw.features;
  }
  
  // Crear amenidades basadas en características conocidas
  const amenities = [];
  if (raw.wifi || raw.has_wifi) amenities.push('wifi');
  if (raw.parking || raw.has_parking) amenities.push('parking');
  if (raw.pool || raw.has_pool) amenities.push('pool');
  if (raw.kitchen || raw.has_kitchen) amenities.push('kitchen');
  if (raw.ac || raw.air_conditioning) amenities.push('ac');
  
  // Si no hay amenidades, añadir algunas por defecto
  if (amenities.length === 0) {
    amenities.push('wifi', 'kitchen', 'tv');
  }
  
  return amenities;
};

// Funciones para modales
const openPropertyModal = (property) => {
  selectedProperty.value = property;
  showPropertyModal.value = true;
  document.body.style.overflow = 'hidden';
};

const closePropertyModal = () => {
  showPropertyModal.value = false;
  selectedProperty.value = null;
  document.body.style.overflow = 'auto';
};

const openImageModal = (imageSrc, imageAlt) => {
  selectedImage.value = imageSrc;
  selectedImageAlt.value = imageAlt || "Imagen de propiedad";
  showImageModal.value = true;
  document.body.style.overflow = 'hidden';
};

const closeImageModal = () => {
  showImageModal.value = false;
  selectedImage.value = "";
  selectedImageAlt.value = "";
  document.body.style.overflow = 'auto';
};

const changeMainImage = (imageSrc) => {
  if (selectedProperty.value) {
    selectedProperty.value.image = imageSrc;
  }
};

const openBookingModal = (property) => {
  if (property && property.available) {
    selectedPropertyForBooking.value = property;
    showBookingModal.value = true;
    closePropertyModal(); // Cerrar el modal de detalles
  }
};

const closeBookingModal = () => {
  showBookingModal.value = false;
  selectedPropertyForBooking.value = null;
  document.body.style.overflow = 'auto';
};

const handleBookingSuccess = (bookingData) => {
  console.log('Reserva exitosa:', bookingData);
  closeBookingModal();
  // Aquí puedes añadir lógica adicional como mostrar un mensaje de éxito
  alert(`¡Reserva confirmada! ID: ${bookingData.bookingId}`);
};

// Cargar favoritos mejorado
const loadFavorites = async () => {
  isLoading.value = true;
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    let storedFavorites = stored ? JSON.parse(stored) : [];
    
    console.log('Stored favorites:', storedFavorites);
    
    // Si ya tenemos objetos completos en localStorage, usarlos directamente
    if (storedFavorites.length > 0 && typeof storedFavorites[0] === 'object' && storedFavorites[0].name) {
      favorites.value = storedFavorites.map(fav => ({
        ...fav,
        available: fav.available !== false,
        images: fav.images && fav.images.length > 0 ? fav.images : getPropertyImages(fav)
      }));
      isLoading.value = false;
      return;
    }
    
    // Si solo tenemos IDs, intentar cargar desde API
    const favoriteIds = storedFavorites.map(fav => 
      typeof fav === 'object' ? fav.id : fav
    );

    if (favoriteIds.length === 0) {
      favorites.value = [];
      isLoading.value = false;
      return;
    }

    try {
      const params = {
        guests: 2,
        maxPrice: 1000,
      };

      const { data } = await axios.get(
        "http://localhost:8000/api/properties/search",
        { params }
      );

      console.log('API response:', data);

      // Filtrar y normalizar propiedades
      favorites.value = data
        .filter(property => favoriteIds.includes(property.id))
        .map(normalizeProperty);
        
      console.log('Normalized favorites:', favorites.value);

    } catch (apiError) {
      console.error('Error loading from API:', apiError);
      favorites.value = [];
    }

  } catch (e) {
    console.error("Error loading favorites:", e);
    favorites.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Guardar favoritos en localStorage
const saveFavorites = () => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites.value));
  } catch (e) {
    console.error("Error saving favorites:", e);
  }
};

// Computed para filtrar y ordenar favoritos
const filteredFavorites = computed(() => {
  let filtered = [...favorites.value];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (property) =>
        property.name.toLowerCase().includes(query) ||
        property.location.toLowerCase().includes(query) ||
        (property.description && property.description.toLowerCase().includes(query))
    );
  }

  switch (filterBy.value) {
    case "available":
      filtered = filtered.filter((property) => property.available === true);
      break;
    case "unavailable":
      filtered = filtered.filter((property) => property.available === false);
      break;
    case "recently-viewed":
      filtered = filtered.filter(
        (property) =>
          property.lastViewed &&
          new Date(property.lastViewed) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
      );
      break;
  }

  return filtered.sort((a, b) => {
    switch (sortBy.value) {
      case "date-desc":
        return new Date(b.addedToFavorites || Date.now()) - new Date(a.addedToFavorites || Date.now());
      case "date-asc":
        return new Date(a.addedToFavorites || Date.now()) - new Date(b.addedToFavorites || Date.now());
      case "price-asc":
        return (a.pricePerNight || 0) - (b.pricePerNight || 0);
      case "price-desc":
        return (b.pricePerNight || 0) - (a.pricePerNight || 0);
      case "rating-desc":
        return (b.rating || 0) - (a.rating || 0);
      case "name-asc":
        return a.name.localeCompare(b.name);
      default:
        return 0;
    }
  });
});

// Métodos auxiliares
const clearSearch = () => {
  searchQuery.value = "";
};

const getAmenityName = (amenity) => {
  const amenityNames = {
    wifi: "WiFi",
    parking: "Parking",
    pool: "Piscina",
    gym: "Gimnasio",
    kitchen: "Cocina",
    ac: "Aire acondicionado",
    heating: "Calefacción",
    tv: "TV",
    washer: "Lavadora",
    balcony: "Balcón",
    garden: "Jardín",
    pets: "Se admiten mascotas",
  };
  return amenityNames[amenity] || amenity;
};

const formatDate = (dateString) => {
  if (!dateString) return "hace tiempo";

  try {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) return "ayer";
    if (diffDays < 7) return `hace ${diffDays} días`;
    if (diffDays < 30) return `hace ${Math.ceil(diffDays / 7)} semanas`;
    if (diffDays < 365) return `hace ${Math.ceil(diffDays / 30)} meses`;
    return `hace ${Math.ceil(diffDays / 365)} años`;
  } catch {
    return "hace tiempo";
  }
};

const calculateRating = (rating) => {
  if (typeof rating === "number" && rating > 0) {
    return rating.toFixed(1);
  }
  return "0.0";
};

const shareProperty = (property) => {
  if (navigator.share) {
    navigator.share({
      title: property.name,
      text: `Echa un vistazo a esta propiedad: ${property.name}`,
      url: `${window.location.origin}/property/${property.id}`,
    }).catch((err) => {
      console.error("Error compartiendo:", err);
    });
  } else {
    const url = `${window.location.origin}/property/${property.id}`;
    navigator.clipboard.writeText(url).then(() => {
      alert("Enlace copiado al portapapeles");
    }).catch((err) => {
      console.error("Error copiando al portapapeles:", err);
    });
  }
};

const contactOwner = (property) => {
  console.log("Contactar propietario de:", property.name);
  alert(`Contactando al propietario de: ${property.name}`);
};

const removeFavorite = (propertyId) => {
  favorites.value = favorites.value.filter((p) => p.id !== propertyId);
  saveFavorites();
};

const removeSingleFavorite = (propertyId) => {
  propertyToRemove.value = favorites.value.find((p) => p.id === propertyId) || null;
  if (propertyToRemove.value) {
    showRemoveModal.value = true;
  }
};

const confirmRemoveSingle = () => {
  if (propertyToRemove.value) {
    removeFavorite(propertyToRemove.value.id);
  }
  closeRemoveModal();
};

const closeRemoveModal = () => {
  showRemoveModal.value = false;
  propertyToRemove.value = null;
};

const closeClearAllModal = () => {
  showClearAllModal.value = false;
};

const confirmClearAll = () => {
  favorites.value = [];
  localStorage.removeItem(STORAGE_KEY);
  closeClearAllModal();
};

// Función para añadir datos de ejemplo con imágenes
const addSampleData = () => {
  const sampleProperties = [
    {
      id: "sample-1",
      name: "Apartamento moderno en el centro",
      location: "Madrid, España",
      pricePerNight: 85,
      bedrooms: 2,
      bathrooms: 1,
      maxGuests: 4,
      rating: 4.8,
      reviewCount: 127,
      available: true,
      image: "/img/placeholder.jpg", // Imagen principal
      images: ["/img/placeholder.jpg"], // Array de imágenes
      amenities: ["wifi", "kitchen", "ac", "tv"],
      addedToFavorites: new Date().toISOString(),
      description: "Hermoso apartamento en el corazón de Madrid con todas las comodidades modernas."
    },
    {
      id: "sample-2",
      name: "Casa con jardín cerca de la playa",
      location: "Valencia, España",
      pricePerNight: 120,
      bedrooms: 3,
      bathrooms: 2,
      maxGuests: 6,
      rating: 4.6,
      reviewCount: 89,
      available: true,
      image: "/img/placeholder.jpg",
      images: ["/img/placeholder.jpg"],
      amenities: ["wifi", "garden", "parking", "pool"],
      addedToFavorites: new Date(Date.now() - 86400000).toISOString(),
      description: "Casa perfecta para vacaciones familiares con amplio jardín y a pocos minutos de la playa."
    },
    {
      id: "sample-3",
      name: "Loft industrial en barrio artístico",
      location: "Barcelona, España",
      pricePerNight: 95,
      bedrooms: 1,
      bathrooms: 1,
      maxGuests: 2,
      rating: 4.9,
      reviewCount: 203,
      available: false,
      image: "/img/placeholder.jpg",
      images: ["/img/placeholder.jpg"],
      amenities: ["wifi", "kitchen", "heating", "balcony"],
      addedToFavorites: new Date(Date.now() - 172800000).toISOString(),
      description: "Loft único en zona bohemia de Barcelona con diseño industrial y vistas espectaculares."
    }
  ];
  
  favorites.value = sampleProperties;
  saveFavorites();
};

// Sincronización con otras pestañas
const handleStorageChange = (event) => {
  if (event.key === STORAGE_KEY) {
    loadFavorites();
  }
};

// Cerrar modales con tecla Escape
const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    if (showImageModal.value) {
      closeImageModal();
    } else if (showPropertyModal.value) {
      closePropertyModal();
    } else if (showClearAllModal.value) {
      closeClearAllModal();
    } else if (showRemoveModal.value) {
      closeRemoveModal();
    } else if (showBookingModal.value) {
      closeBookingModal();
    }
  }
};

onMounted(() => {
  loadFavorites();
  window.addEventListener("storage", handleStorageChange);
  window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener("storage", handleStorageChange);
  window.removeEventListener("keydown", handleKeydown);
  document.body.style.overflow = 'auto';
});
</script>

<style scoped>
.favorites-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 70vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  position: relative;
  overflow: hidden;
}

.favorites-section::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(
      circle at 20% 150%,
      rgba(0, 113, 194, 0.05) 0%,
      transparent 50%
    ),
    radial-gradient(
      circle at 80% -50%,
      rgba(0, 53, 128, 0.03) 0%,
      transparent 60%
    );
  z-index: 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  position: relative;
  z-index: 1;
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

.header-actions {
  display: flex;
  gap: 1rem;
}

.clear-all-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #fee2e2;
  color: #dc2626;
  border: 1px solid #fecaca;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.clear-all-button:hover {
  background-color: #fecaca;
  transform: translateY(-2px);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Controls */
.favorites-controls {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  position: relative;
  z-index: 1;
}

.search-container {
  margin-bottom: 1.5rem;
}

.search-bar {
  position: relative;
  max-width: 500px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #6c757d;
}

.search-input {
  width: 100%;
  padding: 12px 12px 12px 44px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  font-size: 14px;
  background-color: #fff;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

.clear-search {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.clear-search:hover {
  background-color: #f8f9fa;
}

.clear-icon {
  width: 16px;
  height: 16px;
}

.filters-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-group label {
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 500;
  white-space: nowrap;
}

.filter-select {
  padding: 0.5rem 0.75rem;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  background-color: #fff;
  color: #495057;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

.results-count {
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 500;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #6c757d;
  position: relative;
  z-index: 1;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f8f9fa;
  border-top: 3px solid #0071c2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Favorites Grid */
.favorites-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
  position: relative;
  z-index: 1;
}

.favorite-card {
  background-color: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.favorite-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 53, 128, 0.15);
}

.favorite-card.unavailable {
  opacity: 0.7;
}

.favorite-card.unavailable .property-image {
  filter: grayscale(0.3);
}

/* Property Image */
.property-image-container {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.favorite-card:hover .property-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background-color: #fee2e2;
  color: #dc2626;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.availability-badge.available {
  background-color: #dcfce7;
  color: #166534;
}

.favorite-button {
  position: absolute;
  top: 12px;
  right: 12px;
  background-color: rgba(255, 255, 255, 0.9);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(4px);
}

.favorite-button:hover {
  background-color: white;
  transform: scale(1.1);
}

.heart-icon {
  width: 20px;
  height: 20px;
  color: #dc2626;
}

.heart-icon.filled {
  fill: currentColor;
}

.quick-actions {
  position: absolute;
  bottom: 12px;
  right: 12px;
  display: flex;
  gap: 0.5rem;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.favorite-card:hover .quick-actions {
  opacity: 1;
  transform: translateY(0);
}

.quick-action-button {
  background-color: rgba(255, 255, 255, 0.9);
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(4px);
}

.quick-action-button:hover {
  background-color: white;
  transform: scale(1.1);
}

.quick-action-button.remove {
  background-color: rgba(220, 38, 38, 0.9);
  color: white;
}

.quick-action-button.remove:hover {
  background-color: #dc2626;
}

.action-icon {
  width: 16px;
  height: 16px;
}

/* Property Info */
.property-info {
  padding: 1.5rem;
  cursor: pointer;
}

.property-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.property-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
  line-height: 1.3;
  flex: 1;
  margin-right: 1rem;
}

.property-rating {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  flex-shrink: 0;
}

.star-icon {
  width: 14px;
  height: 14px;
  color: #fbbf24;
  fill: currentColor;
}

.rating-value {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1a1a1a;
}

.rating-count {
  font-size: 0.75rem;
  color: #6c757d;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.location-icon {
  width: 14px;
  height: 14px;
}

.property-features {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  color: #495057;
  font-size: 0.875rem;
}

.feature-icon {
  width: 14px;
  height: 14px;
  color: #0071c2;
}

.property-amenities {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.amenity-tag {
  background-color: #f8f9fa;
  color: #495057;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.amenity-tag.more {
  background-color: #e9ecef;
  color: #6c757d;
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

.price-amount {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0071c2;
}

.price-period {
  font-size: 0.875rem;
  color: #6c757d;
}

.added-date {
  font-size: 0.75rem;
  color: #6c757d;
}

/* Property Actions */
.property-actions {
  display: flex;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #f8f9fa;
}

.action-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  flex: 1;
}

.action-button.primary {
  background-color: #0071c2;
  color: white;
  border: none;
}

.action-button.primary:hover:not(:disabled) {
  background-color: #005999;
  transform: translateY(-2px);
}

.action-button.primary:disabled {
  background-color: #cbd5e1;
  cursor: not-allowed;
  transform: none;
}

.action-button.secondary {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #e9ecef;
}

.action-button.secondary:hover {
  background-color: #e9ecef;
}

/* Property Modal */
.property-modal-content {
  background-color: white;
  border-radius: 16px;
  width: 95%;
  max-width: 900px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
}

.property-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e9ecef;
  position: sticky;
  top: 0;
  background-color: white;
  z-index: 10;
}

.property-modal-header h2 {
  font-size: 1.5rem;
  margin: 0;
  font-weight: 600;
  color: #1a1a1a;
}

.property-modal-body {
  padding: 2rem;
}

.property-gallery {
  margin-bottom: 2rem;
}

.main-image-container {
  position: relative;
  height: 400px;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.expand-image-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  background-color: rgba(255, 255, 255, 0.9);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(4px);
}

.expand-image-btn:hover {
  background-color: white;
  transform: scale(1.1);
}

.expand-icon {
  width: 20px;
  height: 20px;
}

.thumbnail-gallery {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
  padding: 0.5rem 0;
}

.thumbnail {
  flex-shrink: 0;
  width: 80px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.thumbnail:hover {
  border-color: #0071c2;
  transform: scale(1.05);
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.property-details {
  display: grid;
  gap: 1.5rem;
}

.property-location-detail {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
  font-size: 1rem;
}

.property-features-detail {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #495057;
  font-size: 1rem;
}

.property-rating-detail {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.rating-stars {
  display: flex;
  gap: 0.25rem;
}

.star {
  width: 20px;
  height: 20px;
  color: #e5e7eb;
}

.star.filled {
  color: #fbbf24;
  fill: currentColor;
}

.rating-text {
  font-size: 1rem;
  color: #495057;
  font-weight: 500;
}

.property-description h3,
.property-amenities-detail h3 {
  font-size: 1.2rem;
  color: #1a1a1a;
  margin: 0 0 0.75rem 0;
  font-weight: 600;
}

.property-description p {
  color: #6c757d;
  line-height: 1.6;
  margin: 0;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.amenity-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.amenity-icon {
  width: 20px;
  height: 20px;
  color: #0071c2;
}

.property-price-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-radius: 12px;
}

.price-info {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.price-amount-large {
  font-size: 2rem;
  font-weight: 700;
  color: #0071c2;
}

.availability-info {
  display: flex;
  align-items: center;
}

.availability-status {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background-color: #fee2e2;
  color: #dc2626;
}

.availability-status.available {
  background-color: #dcfce7;
  color: #166534;
}

.property-modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e9ecef;
  position: sticky;
  bottom: 0;
  background-color: white;
}

.modal-action-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.modal-action-button.primary {
  background-color: #0071c2;
  color: white;
  border: none;
}

.modal-action-button.primary:hover:not(:disabled) {
  background-color: #005999;
  transform: translateY(-2px);
}

.modal-action-button.primary:disabled {
  background-color: #cbd5e1;
  cursor: not-allowed;
  transform: none;
}

.modal-action-button.secondary {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #e9ecef;
}

.modal-action-button.secondary:hover {
  background-color: #e9ecef;
}

/* Image Modal */
.image-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(4px);
}

.image-modal-content {
  position: relative;
  max-width: 95vw;
  max-height: 95vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.image-modal-close {
  position: absolute;
  top: -50px;
  right: 0;
  background-color: rgba(255, 255, 255, 0.9);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 10;
}

.image-modal-close:hover {
  background-color: white;
  transform: scale(1.1);
}

.modal-image {
  max-width: 100%;
  max-height: 85vh;
  object-fit: contain;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.image-modal-caption {
  color: white;
  text-align: center;
  margin-top: 1rem;
  font-size: 1.1rem;
  font-weight: 500;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  position: relative;
  z-index: 1;
}

.empty-icon-container {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}

.empty-icon {
  width: 40px;
  height: 40px;
  color: #6c757d;
}

.empty-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 12px 0;
}

.empty-description {
  color: #6c757d;
  margin: 0 0 24px 0;
  max-width: 400px;
  line-height: 1.5;
}

.empty-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  justify-content: center;
}

.empty-action-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.empty-action-button.primary {
  background-color: #0071c2;
  color: white;
  border: none;
}

.empty-action-button.primary:hover {
  background-color: #005999;
  transform: translateY(-2px);
}

.empty-action-button.secondary {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #e9ecef;
}

.empty-action-button.secondary:hover {
  background-color: #e9ecef;
}

/* Modal Styles */
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
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e9ecef;
}

.modal-header h3 {
  font-size: 1.4rem;
  margin: 0;
  font-weight: 600;
  color: #1a1a1a;
}

.close-button {
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.3s;
}

.close-button:hover {
  background-color: #f8f9fa;
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 2rem;
}

.confirmation-content {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.warning-icon {
  width: 48px;
  height: 48px;
  color: #f59e0b;
  flex-shrink: 0;
}

.confirmation-text h4 {
  font-size: 1.1rem;
  color: #1a1a1a;
  margin: 0 0 0.5rem 0;
}

.confirmation-text p {
  color: #6c757d;
  margin: 0;
  line-height: 1.5;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e9ecef;
}

.cancel-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #d1d5db;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover {
  background-color: #f9fafb;
  color: #374151;
}

.confirm-button {
  background-color: #dc2626;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.confirm-button:hover {
  background-color: #b91c1c;
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .favorites-section {
    padding: 1.5rem;
  }

  .favorites-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  .property-modal-content {
    width: 98%;
    max-width: none;
  }

  .property-modal-body {
    padding: 1.5rem;
  }

  .amenities-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .favorites-section {
    padding: 1rem;
    margin: 1rem;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .header-actions {
    width: 100%;
  }

  .clear-all-button {
    width: 100%;
    justify-content: center;
  }

  .filters-container {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .filter-group {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .filter-select {
    width: 100%;
  }

  .results-count {
    text-align: center;
  }

  .favorites-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .property-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .property-name {
    margin-right: 0;
  }

  .property-features {
    flex-direction: column;
    gap: 0.5rem;
  }

  .property-actions {
    flex-direction: column;
  }

  .empty-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .modal-content {
    width: 95%;
    margin: 1rem;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .confirmation-content {
    flex-direction: column;
    text-align: center;
  }

  .modal-footer {
    flex-direction: column;
  }

  .property-modal-header {
    padding: 1rem 1.5rem;
  }

  .property-modal-body {
    padding: 1rem;
  }

  .property-modal-footer {
    padding: 1rem 1.5rem;
    flex-direction: column;
  }

  .main-image-container {
    height: 250px;
  }

  .property-features-detail {
    flex-direction: column;
    gap: 1rem;
  }

  .property-price-detail {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .property-rating-detail {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .thumbnail-gallery {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .favorites-section {
    padding: 0.75rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .favorites-controls {
    padding: 1rem;
  }

  .property-info {
    padding: 1rem;
  }

  .property-actions {
    padding: 0.75rem 1rem;
  }

  .empty-state {
    padding: 40px 15px;
  }

  .empty-title {
    font-size: 1.3rem;
  }

  .property-modal-header h2 {
    font-size: 1.2rem;
  }

  .main-image-container {
    height: 200px;
  }

  .price-amount-large {
    font-size: 1.5rem;
  }
}
</style>