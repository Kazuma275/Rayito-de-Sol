<template>
  <div class="property-card" @click="viewProperty">
    <div class="property-image-container">
      <img :src="property.image" alt="Property" class="property-image" />
      <div class="property-badge" v-if="property.featured">Destacado</div>
      <button 
        class="favorite-button" 
        :class="{ active: isFavorite }"
        @click.stop="toggleFavorite"
      >
        <HeartIcon class="favorite-icon" />
      </button>
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
      <button class="view-property-button" @click.stop="viewProperty">Ver Detalles</button>
    </div>
  </div>
</template>

<script setup>
import { HeartIcon, MapPinIcon, BedIcon, UsersIcon, CheckIcon } from 'lucide-vue-next';
import { computed, onMounted } from 'vue';
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

let isFavorite = computed(() => {
  return props.favorites.includes(props.property.id);
});

const toggleFavorite = () => {
  emit('toggleFavorite', props.property.id);
};

const viewProperty = () => {
  emit('viewProperty', props.property.id);
  router.push(`/renters/property/${props.property.id}`);
};

const getAmenityName = (id) => {
  const amenity = props.amenities.find(a => a.id === id);
  return amenity ? amenity.name : '';
};

onMounted(() => {
  isFavorite = computed(() => {
    return props.favorites.includes(props.property.id);
  });
});
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
</style>