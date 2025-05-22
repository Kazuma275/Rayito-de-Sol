<template>
  <div class="property-detail-container">
    <div class="property-header">
      <div class="back-button" @click="goBack">
        <ArrowLeftIcon class="back-icon" />
        <span>Volver</span>
      </div>
      <button 
        class="favorite-button" 
        :class="{ active: isFavorite }"
        @click="toggleFavorite"
      >
        <HeartIcon class="favorite-icon" />
        <span>{{ isFavorite ? 'Guardado' : 'Guardar' }}</span>
      </button>
    </div>
    
    <div class="property-gallery">
      <img :src="property.image" alt="Property" class="main-image" />
      <div class="gallery-thumbnails">
        <img 
          v-for="(image, index) in additionalImages" 
          :key="index" 
          :src="image" 
          alt="Property view" 
          class="thumbnail" 
          @click="selectImage(image)"
        />
      </div>
    </div>
    
    <div class="property-content">
      <div class="property-info">
        <h1 class="property-title">{{ property.name }}</h1>
        <div class="property-location">
          <MapPinIcon class="location-icon" />
          <span>{{ property.location }}</span>
        </div>
        
        <div class="property-features">
          <div class="feature">
            <BedIcon class="feature-icon" />
            <span>{{ property.bedrooms }} dormitorios</span>
          </div>
          <div class="feature">
            <UsersIcon class="feature-icon" />
            <span>{{ property.capacity }} huéspedes</span>
          </div>
          <div class="feature">
            <HomeIcon class="feature-icon" />
            <span>Apartamento completo</span>
          </div>
        </div>
        
        <div class="property-description">
          <h2>Descripción</h2>
          <p>{{ property.description }}</p>
        </div>
        
        <div class="property-amenities">
          <h2>Servicios</h2>
          <div class="amenities-grid">
            <div v-for="amenity in amenities" :key="amenity.id" class="amenity-item">
              <CheckIcon class="amenity-icon" />
              <span>{{ amenity.name }}</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="booking-card">
        <div class="booking-price">
          <span class="price-value">€{{ property.price }}</span>
          <span class="price-period">noche</span>
        </div>
        
        <div class="booking-dates">
          <div class="date-input">
            <label>Llegada</label>
            <input type="date" v-model="checkInDate" class="form-input" />
          </div>
          <div class="date-input">
            <label>Salida</label>
            <input type="date" v-model="checkOutDate" class="form-input" />
          </div>
        </div>
        
        <div class="booking-guests">
          <label>Huéspedes</label>
          <select v-model="guests" class="form-input">
            <option v-for="n in property.capacity" :key="n" :value="n">{{ n }} {{ n === 1 ? 'huésped' : 'huéspedes' }}</option>
          </select>
        </div>
        
        <div v-if="checkInDate && checkOutDate" class="booking-summary">
          <div class="summary-item">
            <span>€{{ property.price }} x {{ calculateNights() }} noches</span>
            <span>€{{ calculateSubtotal() }}</span>
          </div>
          <div class="summary-item">
            <span>Tarifa de limpieza</span>
            <span>€50</span>
          </div>
          <div class="summary-item total">
            <span>Total</span>
            <span>€{{ calculateTotal() }}</span>
          </div>
        </div>
        
        <button class="booking-button" @click="bookProperty" :disabled="!canBook">
          {{ checkInDate && checkOutDate ? 'Reservar' : 'Verificar disponibilidad' }}
        </button>
        
        <div class="booking-note">
          No se te cobrará todavía
        </div>
      </div>
    </div>
    
    <div class="property-map">
      <h2>Ubicación</h2>
      <div class="map-container">
        <img src="https://via.placeholder.com/800x400?text=Mapa+de+ubicación" alt="Location map" class="map-image" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowLeftIcon, HeartIcon, MapPinIcon, BedIcon, UsersIcon, HomeIcon, CheckIcon } from 'lucide-vue-next';

const props = defineProps({
  property: {
    type: Object,
    required: true
  },
  isFavorite: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['toggleFavorite', 'bookProperty']);

const router = useRouter();

// Sample additional images
const additionalImages = ref([
  'https://images.unsplash.com/photo-1560185007-cde436f6a4d0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80',
  'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80',
  'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80'
]);

// Sample amenities
const amenities = ref([
  { id: 1, name: 'Wi-Fi' },
  { id: 2, name: 'Piscina' },
  { id: 3, name: 'Aire acondicionado' },
  { id: 4, name: 'Cocina equipada' },
  { id: 5, name: 'Lavadora' },
  { id: 6, name: 'TV' },
  { id: 7, name: 'Terraza' },
  { id: 8, name: 'Parking' }
]);

// Booking state
const checkInDate = ref('');
const checkOutDate = ref('');
const guests = ref(1);

const canBook = computed(() => {
  return checkInDate.value && checkOutDate.value;
});

const calculateNights = () => {
  if (!checkInDate.value || !checkOutDate.value) return 0;
  
  const start = new Date(checkInDate.value);
  const end = new Date(checkOutDate.value);
  const diffTime = end - start;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays > 0 ? diffDays : 0;
};

const calculateSubtotal = () => {
  return props.property.price * calculateNights();
};

const calculateTotal = () => {
  return calculateSubtotal() + 50; // Adding cleaning fee
};

const goBack = () => {
  router.back();
};

const toggleFavorite = () => {
  emit('toggleFavorite', props.property.id);
};

const selectImage = (image) => {
  // In a real app, this would update the main image
  console.log('Selected image:', image);
};

const bookProperty = () => {
  if (!canBook.value) return;
  
  emit('bookProperty', {
    propertyId: props.property.id,
    checkIn: checkInDate.value,
    checkOut: checkOutDate.value,
    guests: guests.value,
    total: calculateTotal()
  });
};
</script>

<style scoped>
.property-detail-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.property-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #003580;
  font-weight: 500;
  cursor: pointer;
  transition: color 0.3s;
}

.back-button:hover {
  color: #0071c2;
}

.back-icon {
  width: 20px;
  height: 20px;
}

.favorite-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: white;
  border: 1px solid #e6f0ff;
  color: #64748b;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.favorite-button:hover {
  background-color: #f8fafc;
}

.favorite-button.active {
  color: #e41c00;
  border-color: #e41c00;
  background-color: #fff2f0;
}

.favorite-button.active .favorite-icon {
  fill: #e41c00;
  color: #e41c00;
}

.favorite-icon {
  width: 18px;
  height: 18px;
}

.property-gallery {
  margin-bottom: 2rem;
}

.main-image {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 12px;
  margin-bottom: 1rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
}

.gallery-thumbnails {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  padding-bottom: 0.5rem;
}

.thumbnail {
  width: 100px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  border: 2px solid transparent;
}

.thumbnail:hover {
  transform: translateY(-2px);
  border-color: #0071c2;
}

.property-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.property-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 0.5rem;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  margin-bottom: 1.5rem;
}

.location-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.property-features {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #e6f0ff;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  color: #003580;
}

.feature-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.property-description, .property-amenities {
  margin-bottom: 2rem;
}

.property-description h2, .property-amenities h2 {
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 1rem;
}

.property-description p {
  color: #1e293b;
  line-height: 1.6;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
}

.amenity-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #1e293b;
}

.amenity-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.booking-card {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  border: 1px solid #e6f0ff;
  position: sticky;
  top: 2rem;
}

.booking-price {
  margin-bottom: 1.5rem;
}

.price-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #003580;
}

.price-period {
  font-size: 1rem;
  color: #64748b;
}

.booking-dates {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.date-input {
  flex: 1;
}

.date-input label, .booking-guests label {
  display: block;
  font-weight: 500;
  color: #003580;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.booking-guests {
  margin-bottom: 1.5rem;
}

.booking-summary {
  margin-bottom: 1.5rem;
  border-top: 1px solid #e6f0ff;
  padding-top: 1.5rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  color: #1e293b;
}

.summary-item.total {
  font-weight: 600;
  color: #003580;
  border-top: 1px solid #e6f0ff;
  padding-top: 0.75rem;
  margin-top: 0.75rem;
}

.booking-button {
  width: 100%;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.875rem 0;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.booking-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.booking-button:disabled {
  background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
  cursor: not-allowed;
}

.booking-note {
  text-align: center;
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #64748b;
}

.property-map {
  margin-bottom: 2rem;
}

.property-map h2 {
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 1rem;
}

.map-container {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
}

.map-image {
  width: 100%;
  height: auto;
  display: block;
}

@media (max-width: 992px) {
  .property-content {
    grid-template-columns: 1fr;
  }
  
  .booking-card {
    position: static;
    order: -1;
    margin-bottom: 2rem;
  }
}

@media (max-width: 768px) {
  .property-detail-container {
    padding: 1rem;
  }
  
  .main-image {
    height: 300px;
  }
  
  .booking-dates {
    flex-direction: column;
    gap: 1rem;
  }
  
  .property-features {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .amenities-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}
</style>