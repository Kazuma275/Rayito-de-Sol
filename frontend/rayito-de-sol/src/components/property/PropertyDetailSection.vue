<template>
  <div class="property-detail-section">
    <!-- Header con navegación -->
    <div class="detail-header">
      <button class="back-button" @click="$emit('go-back')">
        <ArrowLeftIcon class="back-icon" />
        Volver a resultados
      </button>
      <div class="header-actions">
        <button class="share-button" @click="shareProperty">
          <ShareIcon class="action-icon" />
          Compartir
        </button>
        <button 
          class="favorite-button" 
          :class="{ active: isFavorite(property?.id) }"
          @click="toggleFavorite(property?.id)"
        >
          <HeartIcon class="heart-icon" />
          {{ isFavorite(property?.id) ? 'Guardado' : 'Guardar' }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Cargando detalles de la propiedad...</p>
    </div>
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
    </div>
    <div v-else-if="!property" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Cargando detalles de la propiedad...</p>
    </div>

    <div v-else class="property-detail-content">
      <!-- Título y ubicación -->
      <div class="property-header">
        <div class="property-title-section">
          <h1 class="property-title">{{ property.name }}</h1>
          <div class="property-location">
            <MapPinIcon class="location-icon" />
            <span>{{ property.location }}</span>
          </div>
          <div class="property-meta">
            <div class="property-rating">
              <StarIcon class="star-icon" />
              <span class="rating-value">{{ property.rating || '4.5' }}</span>
              <span class="rating-count">({{ property.reviewCount || 0 }} valoraciones)</span>
            </div>
            <div class="property-features">
              <span class="feature">
                <BedIcon class="feature-icon" />
                {{ property.bedrooms || 1 }} dormitorios
              </span>
              <span class="feature">
                <UsersIcon class="feature-icon" />
                {{ property.maxGuests || 2 }} huéspedes
              </span>
              <span class="feature">
                <BathIcon class="feature-icon" />
                {{ property.bathrooms || 1 }} baños
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Galería de imágenes -->
      <div class="property-gallery">
        <div class="main-image">
          <img
            :src="selectedImage || property.images?.[0] || '/placeholder.svg?height=400&width=600'"
            :alt="property.name"
            class="gallery-main-image"
          />
        </div>
        <div class="thumbnail-grid">
          <div
            v-for="(image, index) in property.images?.slice(0, 4)"
            :key="index"
            class="thumbnail"
            :class="{ active: selectedImage === image }"
            @click="selectedImage = image"
          >
            <img :src="image" :alt="`${property.name} - imagen ${index + 1}`" class="thumbnail-image" />
            <div v-if="index === 3 && property.images?.length > 4" class="more-images-overlay">
              <PlusIcon class="plus-icon" />
              <span>+{{ property.images.length - 4 }} más</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido principal -->
      <div class="property-main-content">
        <!-- Información de la propiedad -->
        <div class="property-info">
          <!-- Descripción -->
          <div class="info-section">
            <h2 class="section-title">Descripción</h2>
            <p class="property-description">
              {{ property.description || 'Esta hermosa propiedad ofrece una experiencia única con todas las comodidades necesarias para una estancia perfecta.' }}
            </p>
          </div>

          <!-- Amenidades -->
          <div class="info-section">
            <h2 class="section-title">Comodidades</h2>
            <div class="amenities-grid">
              <div
                v-for="amenity in (showAllAmenities ? property.amenities : property.amenities?.slice(0, 8))"
                :key="amenity"
                class="amenity-item"
              >
                <component :is="getAmenityIcon(amenity)" class="amenity-icon" />
                <span>{{ getAmenityName(amenity) }}</span>
              </div>
              <button 
                v-if="property.amenities?.length > 8 && !showAllAmenities" 
                class="show-all-amenities"
                @click="showAllAmenities = true"
              >
                <PlusIcon class="plus-icon" />
                Ver todas las comodidades ({{ property.amenities.length }})
              </button>
              <button 
                v-if="property.amenities?.length > 8 && showAllAmenities" 
                class="show-less-amenities"
                @click="showAllAmenities = false"
              >
                Ver menos
              </button>
            </div>
          </div>

          <!-- Políticas -->
          <div class="info-section">
            <h2 class="section-title">Políticas de la propiedad</h2>
            <div class="policies-grid">
              <div class="policy-item">
                <ClockIcon class="policy-icon" />
                <div>
                  <h4>Check-in / Check-out</h4>
                  <p>{{ property.checkInTime || '15:00' }} / {{ property.checkOutTime || '11:00' }}</p>
                </div>
              </div>
              <div class="policy-item">
                <XCircleIcon class="policy-icon" />
                <div>
                  <h4>Cancelación</h4>
                  <p>{{ property.cancellationPolicy || 'Cancelación gratuita hasta 24h antes' }}</p>
                </div>
              </div>
              <div class="policy-item">
                <ShieldCheckIcon class="policy-icon" />
                <div>
                  <h4>Normas de la casa</h4>
                  <p>{{ property.houseRules || 'No se permite fumar, no fiestas' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Propietario -->
          <div class="info-section">
            <h2 class="section-title">Anfitrión</h2>
            <div class="host-info">
              <div class="host-avatar">
                <img
                  :src="property.owner?.avatar || '/placeholder.svg?height=60&width=60'"
                  :alt="property.owner?.name"
                  class="host-image"
                />
              </div>
              <div class="host-details">
                <h3 class="host-name">{{ property.owner?.name || 'Anfitrión' }}</h3>
                <div class="host-stats">
                  <span class="host-stat">
                    <StarIcon class="stat-icon" />
                    {{ property.owner?.rating || '4.8' }} valoración
                  </span>
                  <span class="host-stat">
                    <CalendarIcon class="stat-icon" />
                    {{ property.owner?.yearsHosting || '2' }} años como anfitrión
                  </span>
                </div>
                <p class="host-description">
                  {{ property.owner?.description || 'Anfitrión experimentado comprometido con brindar la mejor experiencia a los huéspedes.' }}
                </p>
                <button class="contact-host-button" @click="$emit('contact-owner', property)">
                  <MessageSquareIcon class="contact-icon" />
                  Contactar anfitrión
                </button>
              </div>
            </div>
          </div>

          <!-- Valoraciones -->
          <div class="info-section">
            <h2 class="section-title">
              Valoraciones 
              <span class="reviews-count">({{ property.reviews?.length || 0 }})</span>
            </h2>
            <div v-if="property.reviews?.length > 0" class="reviews-container">
              <div class="reviews-summary">
                <div class="overall-rating">
                  <span class="rating-number">{{ property.rating || '4.5' }}</span>
                  <div class="rating-stars">
                    <StarIcon 
                      v-for="star in 5" 
                      :key="star" 
                      class="star-icon"
                      :class="{ filled: star <= Math.floor(property.rating || 4.5) }"
                    />
                  </div>
                </div>
                <div class="rating-breakdown">
                  <div class="rating-category">
                    <span>Limpieza</span>
                    <div class="rating-bar">
                      <div class="rating-fill" :style="{ width: '90%' }"></div>
                    </div>
                    <span>4.5</span>
                  </div>
                  <div class="rating-category">
                    <span>Comunicación</span>
                    <div class="rating-bar">
                      <div class="rating-fill" :style="{ width: '95%' }"></div>
                    </div>
                    <span>4.8</span>
                  </div>
                  <div class="rating-category">
                    <span>Ubicación</span>
                    <div class="rating-bar">
                      <div class="rating-fill" :style="{ width: '85%' }"></div>
                    </div>
                    <span>4.3</span>
                  </div>
                </div>
              </div>
              <div class="reviews-list">
                <div
                  v-for="review in property.reviews?.slice(0, 3)"
                  :key="review.id"
                  class="review-item"
                >
                  <div class="review-header">
                    <div class="reviewer-info">
                      <img
                        :src="review.user?.avatar || '/placeholder.svg?height=40&width=40'"
                        :alt="review.user?.name"
                        class="reviewer-avatar"
                      />
                      <div>
                        <h4 class="reviewer-name">{{ review.user?.name }}</h4>
                        <span class="review-date">{{ formatDate(review.date) }}</span>
                      </div>
                    </div>
                    <div class="review-rating">
                      <StarIcon 
                        v-for="star in 5" 
                        :key="star" 
                        class="star-icon small"
                        :class="{ filled: star <= review.rating }"
                      />
                    </div>
                  </div>
                  <p class="review-text">{{ review.comment }}</p>
                </div>
                <button 
                  v-if="property.reviews?.length > 3" 
                  class="show-all-reviews"
                >
                  Ver todas las valoraciones
                </button>
              </div>
            </div>
            <div v-else class="no-reviews">
              <MessageSquareIcon class="no-reviews-icon" />
              <p>Aún no hay valoraciones para esta propiedad</p>
            </div>
          </div>
        </div>

        <!-- Panel de reserva -->
        <div class="booking-panel">
          <div class="booking-card">
            <div class="booking-header">
              <div class="price-display">
                <span class="price-amount">€{{ property.pricePerNight || 0 }}</span>
                <span class="price-period">/ noche</span>
              </div>
              <div class="booking-rating">
                <StarIcon class="star-icon" />
                <span>{{ property.rating || '4.5' }}</span>
                <span class="rating-count">({{ property.reviewCount || 0 }})</span>
              </div>
            </div>

            <div class="booking-form">
              <div class="date-inputs">
                <div class="date-input-group">
                  <label>Llegada</label>
                  <div class="date-input">
                    <CalendarIcon class="input-icon" />
                    <input
                      type="date"
                      :min="today"
                      :value="bookingData.checkIn"
                      @input="updateBookingData('checkIn', $event.target.value)"
                    />
                  </div>
                </div>
                <div class="date-input-group">
                  <label>Salida</label>
                  <div class="date-input">
                    <CalendarIcon class="input-icon" />
                    <input
                      type="date"
                      :min="bookingData.checkIn || today"
                      :value="bookingData.checkOut"
                      @input="updateBookingData('checkOut', $event.target.value)"
                    />
                  </div>
                </div>
              </div>

              <div class="guests-input">
                <label>Huéspedes</label>
                <div class="number-input">
                  <UsersIcon class="input-icon" />
                  <input
                    type="number"
                    min="1"
                    :max="property.maxGuests || 10"
                    :value="bookingData.guests"
                    @input="updateBookingData('guests', parseInt($event.target.value) || 1)"
                  />
                </div>
              </div>

              <button 
                class="book-button"
                :disabled="!canBook"
                @click="$emit('book-property', { property, bookingData })"
              >
                {{ canBook ? 'Reservar ahora' : 'Selecciona fechas' }}
              </button>

              <p class="booking-note">No se realizará ningún cargo todavía</p>

              <!-- Desglose de precios -->
              <div v-if="bookingData.checkIn && bookingData.checkOut" class="price-breakdown">
                <div class="price-item">
                  <span>€{{ property.pricePerNight }} x {{ calculateNights(bookingData.checkIn, bookingData.checkOut) }} noches</span>
                  <span>€{{ calculateSubtotal(property.pricePerNight, bookingData.checkIn, bookingData.checkOut) }}</span>
                </div>
                <div class="price-item">
                  <span>Tasas de limpieza</span>
                  <span>€{{ cleaningFee }}</span>
                </div>
                <div class="price-item">
                  <span>Tasas de servicio</span>
                  <span>€{{ calculateServiceFee(calculateSubtotal(property.pricePerNight, bookingData.checkIn, bookingData.checkOut)) }}</span>
                </div>
                <div class="price-item total">
                  <span>Total</span>
                  <span>€{{ calculateTotal(property.pricePerNight, bookingData.checkIn, bookingData.checkOut, cleaningFee) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Información adicional -->
          <div class="additional-info">
            <div class="info-item">
              <ShieldCheckIcon class="info-icon" />
              <div>
                <h4>Reserva segura</h4>
                <p>Tus datos están protegidos con encriptación SSL</p>
              </div>
            </div>
            <div class="info-item">
              <ClockIcon class="info-icon" />
              <div>
                <h4>Confirmación instantánea</h4>
                <p>Recibirás la confirmación inmediatamente</p>
              </div>
            </div>
            <div class="info-item">
              <XCircleIcon class="info-icon" />
              <div>
                <h4>Cancelación flexible</h4>
                <p>Cancela hasta 24h antes sin coste</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template><script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import {
  ArrowLeftIcon,
  ShareIcon,
  HeartIcon,
  MapPinIcon,
  StarIcon,
  BedIcon,
  UsersIcon,
  BathIcon,
  PlusIcon,
  ClockIcon,
  XCircleIcon,
  ShieldCheckIcon,
  CalendarIcon,
  MessageSquareIcon,
  WifiIcon,
  CarIcon,
  UtensilsIcon,
  AirVentIcon,
  TvIcon,
  WashingMachineIcon
} from 'lucide-vue-next'

// Props
const props = defineProps({
  propertyId: {
    type: [String, Number],
    required: true
  },
  bookingData: {
    type: Object,
    default: () => ({
      checkIn: '',
      checkOut: '',
      guests: 1
    })
  },
  amenities: {
    type: Array,
    default: () => []
  },
  today: {
    type: String,
    default: () => new Date().toISOString().split('T')[0]
  },
  cleaningFee: {
    type: Number,
    default: 25
  },
  canBook: {
    type: Boolean,
    default: false
  },
  isFavorite: {
    type: Function,
    required: true
  },
  toggleFavorite: {
    type: Function,
    required: true
  },
  calculateNights: {
    type: Function,
    required: true
  },
  calculateSubtotal: {
    type: Function,
    required: true
  },
  calculateServiceFee: {
    type: Function,
    required: true
  },
  calculateTotal: {
    type: Function,
    required: true
  },
  getAmenityName: {
    type: Function,
    required: true
  }
})

// Emits
const emit = defineEmits([
  'go-back',
  'update-booking-data',
  'book-property',
  'contact-owner'
])

// Local state
const selectedImage = ref(null)
const showAllAmenities = ref(false)
const loading = ref(true)
const error = ref(null)
const property = ref(null)

// Helper to always set property.images as an array and pricePerNight
function normalizeProperty(raw) {
  // Images: prefer images array, fallback to image string, fallback to placeholder
  let images = []
  if (Array.isArray(raw.images) && raw.images.length) {
    images = raw.images
  } else if (raw.image) {
    images = [raw.image]
  } else {
    images = ['/placeholder.svg?height=400&width=600']
  }

  // Price: fallback to 0
  let price = Number(raw.pricePerNight ?? raw.price)
  if (isNaN(price)) price = 0

  // Amenities: array or parseable string
  let amenities = []
  if (Array.isArray(raw.amenities)) {
    amenities = raw.amenities
  } else if (typeof raw.amenities === "string" && raw.amenities.length > 0) {
    try {
      amenities = JSON.parse(raw.amenities)
    } catch (e) {
      amenities = raw.amenities.split(',').map(a => a.trim())
    }
  }

  return {
    ...raw,
    images,
    pricePerNight: price,
    amenities
  }
}

// Fetch property from API
const fetchProperty = async () => {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get(`/api/properties/${props.propertyId}`)
    property.value = normalizeProperty(data)
    // Reset image selection on property change
    selectedImage.value = null
  } catch (e) {
    error.value = 'Error cargando los datos de la propiedad'
    property.value = null
  } finally {
    loading.value = false
  }
}

// Cargar al montar y cuando cambie el propertyId
onMounted(fetchProperty)
watch(() => props.propertyId, fetchProperty)

// Métodos que usan property local
const updateBookingData = (key, value) => {
  emit('update-booking-data', { [key]: value })
}

const shareProperty = () => {
  if (navigator.share && property.value) {
    navigator.share({
      title: property.value.name,
      text: `Echa un vistazo a esta propiedad: ${property.value.name}`,
      url: window.location.href
    })
  } else {
    navigator.clipboard.writeText(window.location.href).then(() => {
      // Aquí puedes mostrar un toast si tienes uno
      console.log('Enlace copiado al portapapeles')
    })
  }
}

const getAmenityIcon = (amenity) => {
  const icons = {
    wifi: WifiIcon,
    parking: CarIcon,
    kitchen: UtensilsIcon,
    ac: AirVentIcon,
    tv: TvIcon,
    washer: WashingMachineIcon
  }
  return icons[amenity] || PlusIcon
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<style scoped>
.property-detail-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 70vh;
  width: 100%;
  max-width: 1400px;
  margin: 2rem auto;
  position: relative;
  overflow: hidden;
}

.property-detail-section::before {
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

/* Header */
.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  position: relative;
  z-index: 1;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: white;
  color: #64748b;
  border: 1px solid #e2e8f0;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-button:hover {
  background-color: #f8fafc;
  color: #0071c2;
  border-color: #0071c2;
}

.back-icon {
  width: 18px;
  height: 18px;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.share-button,
.favorite-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.share-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.share-button:hover {
  background-color: #f8fafc;
  color: #0071c2;
  border-color: #0071c2;
}

.favorite-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.favorite-button.active {
  background-color: #fee2e2;
  color: #dc2626;
  border-color: #fecaca;
}

.favorite-button:hover {
  background-color: #f8fafc;
}

.action-icon,
.heart-icon {
  width: 18px;
  height: 18px;
}

.favorite-button.active .heart-icon {
  fill: currentColor;
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
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Property Content */
.property-detail-content {
  position: relative;
  z-index: 1;
}

/* Property Header */
.property-header {
  margin-bottom: 2rem;
}

.property-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 0.5rem 0;
  line-height: 1.2;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  font-size: 1rem;
  margin-bottom: 1rem;
}

.location-icon {
  width: 18px;
  height: 18px;
}

.property-meta {
  display: flex;
  gap: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.property-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.star-icon {
  width: 18px;
  height: 18px;
  color: #fbbf24;
  fill: currentColor;
}

.rating-value {
  font-size: 1rem;
  font-weight: 600;
  color: #1a1a1a;
}

.rating-count {
  color: #64748b;
  font-size: 0.9rem;
}

.property-features {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #495057;
  font-size: 0.95rem;
}

.feature-icon {
  width: 16px;
  height: 16px;
  color: #0071c2;
}

/* Gallery */
.property-gallery {
  margin-bottom: 3rem;
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1rem;
  height: 400px;
}

.main-image {
  border-radius: 12px;
  overflow: hidden;
}

.gallery-main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem;
}

.thumbnail {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
}

.thumbnail:hover {
  transform: scale(1.02);
}

.thumbnail.active {
  ring: 2px solid #0071c2;
}

.thumbnail-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.more-images-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.875rem;
  font-weight: 500;
}

.plus-icon {
  width: 20px;
  height: 20px;
  margin-bottom: 0.25rem;
}

/* Main Content */
.property-main-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 3rem;
}

.property-info {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.info-section {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
}

.section-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 1rem 0;
}

.property-description {
  color: #64748b;
  line-height: 1.6;
  margin: 0;
}

/* Amenities */
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
  background-color: #f8fafc;
  border-radius: 8px;
  color: #495057;
}

.amenity-icon {
  width: 20px;
  height: 20px;
  color: #0071c2;
}

.show-all-amenities {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background-color: #f0f7ff;
  color: #0071c2;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.show-all-amenities:hover {
  background-color: #e6f0ff;
}

/* Policies */
.policies-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.policy-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.policy-icon {
  width: 20px;
  height: 20px;
  color: #0071c2;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.policy-item h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 0.25rem 0;
}

.policy-item p {
  color: #64748b;
  margin: 0;
  font-size: 0.9rem;
}

/* Host Info */
.host-info {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.host-avatar {
  flex-shrink: 0;
}

.host-image {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
}

.host-details {
  flex: 1;
}

.host-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 0.5rem 0;
}

.host-stats {
  display: flex;
  gap: 1rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
}

.host-stat {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: #64748b;
  font-size: 0.875rem;
}

.stat-icon {
  width: 14px;
  height: 14px;
}

.host-description {
  color: #64748b;
  line-height: 1.5;
  margin: 0 0 1rem 0;
  font-size: 0.9rem;
}

.contact-host-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.contact-host-button:hover {
  background-color: #005999;
  transform: translateY(-2px);
}

.contact-icon {
  width: 16px;
  height: 16px;
}

/* Reviews */
.reviews-count {
  color: #64748b;
  font-weight: normal;
  font-size: 1rem;
}

.reviews-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.reviews-summary {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 2rem;
  align-items: center;
}

.overall-rating {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.rating-number {
  font-size: 2rem;
  font-weight: 700;
  color: #0071c2;
}

.rating-stars {
  display: flex;
  gap: 0.25rem;
}

.star-icon.small {
  width: 14px;
  height: 14px;
}

.star-icon.filled {
  color: #fbbf24;
  fill: currentColor;
}

.rating-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.rating-category {
  display: grid;
  grid-template-columns: 100px 1fr auto;
  gap: 1rem;
  align-items: center;
  font-size: 0.875rem;
}

.rating-bar {
  height: 8px;
  background-color: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.rating-fill {
  height: 100%;
  background-color: #0071c2;
  transition: width 0.3s ease;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.review-item {
  padding: 1rem;
  background-color: #f8fafc;
  border-radius: 8px;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.reviewer-info {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.reviewer-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.reviewer-name {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.review-date {
  font-size: 0.8rem;
  color: #64748b;
}

.review-rating {
  display: flex;
  gap: 0.125rem;
}

.review-text {
  color: #64748b;
  line-height: 1.5;
  margin: 0;
  font-size: 0.9rem;
}

.show-all-reviews {
  background-color: #f0f7ff;
  color: #0071c2;
  border: 1px solid #e6f0ff;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.show-all-reviews:hover {
  background-color: #e6f0ff;
}

.no-reviews {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
  color: #64748b;
}

.no-reviews-icon {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
  color: #cbd5e1;
}

/* Booking Panel */
.booking-panel {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.booking-card {
  background-color: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.booking-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.price-display {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
}

.price-amount {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0071c2;
}

.price-period {
  color: #64748b;
  font-size: 0.9rem;
}

.booking-rating {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
}

.booking-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.date-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem;
}

.date-input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.date-input-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.date-input,
.number-input {
  position: relative;
}

.input-icon {
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
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  color: #1e293b;
  background-color: white;
  transition: all 0.3s ease;
}

.date-input input:focus,
.number-input input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

.guests-input {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.guests-input label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.book-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 0.5rem;
}

.book-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.book-button:disabled {
  background: #cbd5e1;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.booking-note {
  text-align: center;
  font-size: 0.875rem;
  color: #64748b;
  margin: 0.5rem 0 0 0;
}

.price-breakdown {
  border-top: 1px solid #e2e8f0;
  padding-top: 1rem;
  margin-top: 1rem;
}

.price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  font-size: 0.9rem;
  color: #64748b;
}

.price-item.total {
  border-top: 1px solid #e2e8f0;
  margin-top: 0.5rem;
  padding-top: 1rem;
  font-weight: 600;
  color: #1a1a1a;
  font-size: 1rem;
}

/* Additional Info */
.additional-info {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
}

.info-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-icon {
  width: 20px;
  height: 20px;
  color: #0071c2;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.info-item h4 {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 0.25rem 0;
}

.info-item p {
  color: #64748b;
  margin: 0;
  font-size: 0.875rem;
  line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .property-detail-section {
    padding: 1.5rem;
  }

  .property-main-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .booking-panel {
    position: static;
  }

  .property-gallery {
    grid-template-columns: 1fr;
    height: auto;
  }

  .main-image {
    height: 300px;
  }

  .thumbnail-grid {
    grid-template-columns: repeat(4, 1fr);
    height: 80px;
  }
}

@media (max-width: 768px) {
  .property-detail-section {
    padding: 1rem;
    margin: 1rem;
  }

  .detail-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .header-actions {
    width: 100%;
    justify-content: space-between;
  }

  .property-title {
    font-size: 1.5rem;
  }

  .property-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .property-gallery {
    height: auto;
  }

  .main-image {
    height: 250px;
  }

  .thumbnail-grid {
    height: 60px;
  }

  .date-inputs {
    grid-template-columns: 1fr;
  }

  .reviews-summary {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .rating-category {
    grid-template-columns: 80px 1fr auto;
    gap: 0.5rem;
  }

  .host-info {
    flex-direction: column;
    text-align: center;
  }
}

@media (max-width: 576px) {
  .property-detail-section {
    padding: 0.75rem;
  }

  .property-features {
    flex-direction: column;
    gap: 0.75rem;
  }

  .amenities-grid {
    grid-template-columns: 1fr;
  }

  .booking-card {
    padding: 1rem;
  }

  .price-amount {
    font-size: 1.25rem;
  }
}
</style>
