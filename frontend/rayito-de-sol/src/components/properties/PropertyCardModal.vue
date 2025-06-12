<template>
  <div class="property-card" @click="viewProperty">
    <div class="property-image-container">
      <img :src="property.image" alt="Property" class="property-image" />
      <div class="property-badge" v-if="property.featured">Destacado</div>
<!--       <button 
        class="favorite-button" 
        :class="{ active: isFavorite }"
        @click.stop="toggleFavorite"
      >
        <HeartIcon class="favorite-icon" />
      </button> -->
    </div>
    <div class="property-content">
      <h3 class="property-name">{{ property.name }}</h3>
      <div class="property-location">
        <MapPinIcon class="location-icon" />
        <span>{{ property.location }}</span>
      </div>
      <div class="property-details">
        <div class="property-detail">
          <BedIcon class="detail-icon" />
          <span>{{ property.bedrooms }} dormitorios</span>
        </div>
        <div class="property-detail">
          <UsersIcon class="detail-icon" />
          <span>{{ property.capacity }} huéspedes</span>
        </div>
      </div>
      <div v-if="showAmenities && property.amenities" class="property-amenities">
        <div v-for="amenityId in property.amenities.slice(0, 3)" :key="amenityId" class="property-amenity">
          <CheckIcon class="amenity-icon" />
          <span>{{ getAmenityName(amenityId) }}</span>
        </div>
        <div v-if="property.amenities.length > 3" class="property-amenity more">
          <span>+{{ property.amenities.length - 3 }} más</span>
        </div>
      </div>
      <div class="property-price">
        <span class="price-value">€{{ property.price }}</span>
        <span class="price-period">noche</span>
      </div>
      <button class="view-property-button" @click.stop="openModal">Ver Detalles</button>
    </div>

    <!-- Modal de detalles de la propiedad -->
    <div v-if="showModal" class="property-modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ property.name }}</h2>
          <button class="close-button" @click="closeModal">
            <XIcon class="close-icon" />
          </button>
        </div>

        <div class="modal-body">
          <!-- Galería de imágenes -->
          <div class="property-gallery">
            <img :src="property.image" :alt="property.name" class="main-image" />
          </div>

          <!-- Información principal -->
          <div class="property-main-info">
            <div class="property-header">
              <div class="property-title-section">
                <h3>{{ property.name }}</h3>
                <div class="property-location-detail">
                  <MapPinIcon class="location-icon" />
                  <span>{{ property.location }}</span>
                </div>
              </div>
              <div class="property-price-section">
                <div class="price-display">
                  <span class="price-amount">€{{ property.price }}</span>
                  <span class="price-period">por noche</span>
                </div>
              </div>
            </div>

            <!-- Características principales -->
            <div class="property-features">
              <div class="feature-item">
                <BedIcon class="feature-icon" />
                <div class="feature-info">
                  <span class="feature-label">Dormitorios</span>
                  <span class="feature-value">{{ property.bedrooms }}</span>
                </div>
              </div>
              <div class="feature-item">
                <UsersIcon class="feature-icon" />
                <div class="feature-info">
                  <span class="feature-label">Capacidad</span>
                  <span class="feature-value">{{ property.capacity }} huéspedes</span>
                </div>
              </div>
              <div class="feature-item">
                <HomeIcon class="feature-icon" />
                <div class="feature-info">
                  <span class="feature-label">Tipo</span>
                  <span class="feature-value">{{ getPropertyType() }}</span>
                </div>
              </div>
              <div class="feature-item">
                <CalendarIcon class="feature-icon" />
                <div class="feature-info">
                  <span class="feature-label">Estado</span>
                  <span class="feature-value">{{ getPropertyStatus() }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Descripción -->
          <div class="property-description">
            <h4>Descripción</h4>
            <p>{{ property.description || 'Esta hermosa propiedad ofrece una experiencia única con todas las comodidades necesarias para una estancia perfecta. Ubicada en una zona privilegiada, cuenta con fácil acceso a los principales puntos de interés de la ciudad.' }}</p>
          </div>

          <!-- Servicios y comodidades -->
          <div class="property-amenities-section">
            <h4>Servicios y comodidades</h4>
            <div class="amenities-grid">
              <div 
                v-for="amenity in parsedAmenities" 
                :key="amenity"
                class="amenity-item"
              >
                <CheckCircleIcon class="amenity-check" />
                <span>{{ amenity }}</span>
              </div>
            </div>
          </div>

          <!-- Información adicional -->
          <div class="property-additional-info">
            <div class="info-section">
              <h4>Información de la propiedad</h4>
              <div class="info-grid">
                <div class="info-item">
                  <span class="info-label">ID de propiedad:</span>
                  <span class="info-value">#{{ property.id }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Fecha de registro:</span>
                  <span class="info-value">{{ formatDate(property.created_at) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Última actualización:</span>
                  <span class="info-value">{{ formatDate(property.updated_at) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Propietario:</span>
                  <span class="info-value">Usuario #{{ property.user_id }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Políticas y reglas -->
          <div class="property-policies">
            <h4>Políticas de la propiedad</h4>
            <div class="policies-grid">
              <div class="policy-item">
                <ClockIcon class="policy-icon" />
                <div class="policy-info">
                  <span class="policy-title">Check-in</span>
                  <span class="policy-detail">A partir de las 15:00</span>
                </div>
              </div>
              <div class="policy-item">
                <ClockIcon class="policy-icon" />
                <div class="policy-info">
                  <span class="policy-title">Check-out</span>
                  <span class="policy-detail">Hasta las 11:00</span>
                </div>
              </div>
              <div class="policy-item">
                <ShieldIcon class="policy-icon" />
                <div class="policy-info">
                  <span class="policy-title">Cancelación</span>
                  <span class="policy-detail">Flexible</span>
                </div>
              </div>
              <div class="policy-item">
                <InfoIcon class="policy-icon" />
                <div class="policy-info">
                  <span class="policy-title">Reglas</span>
                  <span class="policy-detail">No fumar, No fiestas</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="contact-button">
            <MessageCircleIcon class="button-icon" />
            Contactar propietario
          </button>
          <button class="reserve-button">
            <CalendarIcon class="button-icon" />
            Reservar ahora
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  HeartIcon, 
  MapPinIcon, 
  BedIcon, 
  UsersIcon, 
  CheckIcon,
  XIcon,
  HomeIcon,
  CalendarIcon,
  CheckCircleIcon,
  ClockIcon,
  ShieldIcon,
  InfoIcon,
  MessageCircleIcon
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  property: {
    type: Object,
    required: true
  },
  favorites: {
    type: Array,
    default: () => []
  },
  showAmenities: {
    type: Boolean,
    default: true
  },
  amenities: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['toggleFavorite', 'viewProperty']);

const router = useRouter();
const showModal = ref(false);

const isFavorite = computed(() => {
  return props.favorites.includes(props.property.id);
});

const parsedAmenities = computed(() => {
  if (!props.property.amenities) return [];
  
  try {
    // Si amenities es un string JSON, parsearlo
    if (typeof props.property.amenities === 'string') {
      return JSON.parse(props.property.amenities);
    }
    // Si ya es un array, devolverlo
    if (Array.isArray(props.property.amenities)) {
      return props.property.amenities.map(amenity => {
        if (typeof amenity === 'object' && amenity.name) {
          return amenity.name;
        }
        return amenity;
      });
    }
    return [];
  } catch (error) {
    console.error('Error parsing amenities:', error);
    return [];
  }
});

const toggleFavorite = () => {
  emit('toggleFavorite', props.property.id);
};

const viewProperty = () => {
  // No navegamos automáticamente al hacer click en la card
};

const openModal = () => {
  showModal.value = true;
  emit('viewProperty', props.property.id);
};

const closeModal = () => {
  showModal.value = false;
};

const getAmenityName = (id) => {
  const amenity = props.amenities.find(a => a.id === id);
  return amenity ? amenity.name : '';
};

const getPropertyType = () => {
  // Extraer tipo de propiedad del nombre o usar un valor por defecto
  if (props.property.name.toLowerCase().includes('apartamento')) return 'Apartamento';
  if (props.property.name.toLowerCase().includes('casa')) return 'Casa';
  if (props.property.name.toLowerCase().includes('villa')) return 'Villa';
  if (props.property.name.toLowerCase().includes('tiny house')) return 'Tiny House';
  return 'Propiedad';
};

const getPropertyStatus = () => {
  return props.property.status === 'active' ? 'Disponible' : 'No disponible';
};

const formatDate = (dateString) => {
  if (!dateString) return 'No disponible';
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};
</script>

<style scoped>
.property-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 53, 128, 0.15);
}

.property-image-container {
  position: relative;
}

.property-image {
  width: 100%;
  height: 220px;
  object-fit: cover;
}

.property-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(0, 53, 128, 0.2);
}

.favorite-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: white;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.favorite-button:hover {
  transform: scale(1.1);
}

.favorite-button.active .favorite-icon {
  fill: #e41c00;
  color: #e41c00;
}

.favorite-icon {
  width: 20px;
  height: 20px;
  color: #64748b;
}

.property-content {
  padding: 1.5rem;
}

.property-name {
  font-size: 1.2rem;
  margin: 0 0 0.5rem;
  color: #003580;
  font-weight: 600;
}

.property-location {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  color: #64748b;
}

.location-icon {
  width: 16px;
  height: 16px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.property-details {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.property-detail {
  display: flex;
  align-items: center;
  background-color: #e6f0ff;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.9rem;
  color: #003580;
}

.detail-icon {
  width: 14px;
  height: 14px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.property-amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.property-amenity {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  color: #64748b;
}

.amenity-icon {
  width: 14px;
  height: 14px;
  color: #0071c2;
  margin-right: 0.25rem;
}

.property-amenity.more {
  color: #0071c2;
  font-weight: 500;
}

.property-price {
  margin-bottom: 1rem;
}

.price-value {
  font-size: 1.3rem;
  font-weight: 700;
  color: #003580;
}

.price-period {
  font-size: 0.9rem;
  color: #64748b;
  margin-left: 0.25rem;
}

.view-property-button {
  width: 100%;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 0;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.view-property-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

/* Estilos del Modal */
.property-modal {
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
  padding: 1rem;
}

.modal-content {
  background-color: white;
  border-radius: 16px;
  width: 100%;
  max-width: 900px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e6f0ff;
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  border-radius: 16px 16px 0 0;
}

.modal-header h2 {
  font-size: 1.5rem;
  margin: 0;
  font-weight: 600;
}

.close-button {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.close-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 2rem;
}

.property-gallery {
  margin-bottom: 2rem;
}

.main-image {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-radius: 12px;
}

.property-main-info {
  margin-bottom: 2rem;
}

.property-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
  gap: 2rem;
}

.property-title-section h3 {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 0.5rem;
  font-weight: 700;
}

.property-location-detail {
  display: flex;
  align-items: center;
  color: #64748b;
  font-size: 1.1rem;
}

.property-location-detail .location-icon {
  width: 20px;
  height: 20px;
  margin-right: 0.5rem;
}

.property-price-section {
  text-align: right;
}

.price-display {
  background: linear-gradient(135deg, #e6f0ff 0%, #f0f7ff 100%);
  padding: 1rem 1.5rem;
  border-radius: 12px;
  border: 1px solid #0071c2;
}

.price-amount {
  font-size: 2rem;
  font-weight: 700;
  color: #003580;
  display: block;
}

.price-period {
  color: #64748b;
  font-size: 1rem;
}

.property-features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.feature-item {
  display: flex;
  align-items: center;
  background-color: #f8fafc;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.feature-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-right: 1rem;
}

.feature-info {
  display: flex;
  flex-direction: column;
}

.feature-label {
  font-size: 0.9rem;
  color: #64748b;
  margin-bottom: 0.25rem;
}

.feature-value {
  font-size: 1rem;
  font-weight: 600;
  color: #003580;
}

.property-description {
  margin-bottom: 2rem;
}

.property-description h4 {
  font-size: 1.3rem;
  color: #003580;
  margin: 0 0 1rem;
  font-weight: 600;
}

.property-description p {
  color: #1e293b;
  line-height: 1.6;
  margin: 0;
}

.property-amenities-section {
  margin-bottom: 2rem;
}

.property-amenities-section h4 {
  font-size: 1.3rem;
  color: #003580;
  margin: 0 0 1rem;
  font-weight: 600;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 0.75rem;
}

.amenity-item {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.amenity-check {
  width: 20px;
  height: 20px;
  color: #00703c;
  margin-right: 0.75rem;
}

.property-additional-info {
  margin-bottom: 2rem;
}

.info-section h4 {
  font-size: 1.3rem;
  color: #003580;
  margin: 0 0 1rem;
  font-weight: 600;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.info-label {
  color: #64748b;
  font-weight: 500;
}

.info-value {
  color: #003580;
  font-weight: 600;
}

.property-policies {
  margin-bottom: 2rem;
}

.property-policies h4 {
  font-size: 1.3rem;
  color: #003580;
  margin: 0 0 1rem;
  font-weight: 600;
}

.policies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.policy-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.policy-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-right: 1rem;
}

.policy-info {
  display: flex;
  flex-direction: column;
}

.policy-title {
  font-weight: 600;
  color: #003580;
  margin-bottom: 0.25rem;
}

.policy-detail {
  color: #64748b;
  font-size: 0.9rem;
}

.modal-footer {
  display: flex;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e6f0ff;
  background-color: #f8fafc;
  border-radius: 0 0 16px 16px;
}

.contact-button,
.reserve-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.contact-button {
  background-color: white;
  color: #0071c2;
  border: 2px solid #0071c2;
}

.contact-button:hover {
  background-color: #f0f7ff;
}

.reserve-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
}

.reserve-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Responsive */
@media (max-width: 768px) {
  .property-modal {
    padding: 0.5rem;
  }

  .modal-content {
    max-height: 95vh;
  }

  .modal-header {
    padding: 1rem 1.5rem;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .property-header {
    flex-direction: column;
    gap: 1rem;
  }

  .property-price-section {
    text-align: left;
  }

  .property-features {
    grid-template-columns: 1fr;
  }

  .amenities-grid {
    grid-template-columns: 1fr;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .policies-grid {
    grid-template-columns: 1fr;
  }

  .modal-footer {
    flex-direction: column;
    padding: 1rem 1.5rem;
  }
}
</style>