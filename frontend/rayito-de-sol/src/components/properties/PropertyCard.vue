<template>
  <div class="property-card">
    <div class="property-image-container">
      <img :src="property.image" alt="Property" class="property-image" />
      <div class="property-status" :class="property.status">
        {{ property.statusText }}
      </div>
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
        <div class="property-detail">
          <EuroIcon class="detail-icon" />
          <span>€{{ property.price }}/noche</span>
        </div>
      </div>
    </div>
    <div class="property-actions">
      <button class="action-button edit">
        <EditIcon class="action-icon" />
        Editar
      </button>
      <button class="action-button calendar">
        <CalendarIcon class="action-icon" />
        Calendario
      </button>
    </div>
  </div>
</template>

<script setup>
import { 
  MapPinIcon, 
  BedIcon, 
  UsersIcon, 
  EuroIcon, 
  EditIcon, 
  CalendarIcon 
} from 'lucide-vue-next';

defineProps({
  property: {
    type: Object,
    required: true
  }
});
</script>

<style scoped>
.property-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 53, 128, 0.15);
}

.property-image-container {
  position: relative;
}

.property-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.property-status {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.property-status.active {
  color: #00703c;
  border: 1px solid rgba(0, 112, 60, 0.2);
}

.property-status.inactive {
  color: #e41c00;
  border: 1px solid rgba(228, 28, 0, 0.2);
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

.property-actions {
  display: flex;
  border-top: 1px solid #e6f0ff;
}

.action-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s;
  font-weight: 500;
}

.action-button:hover {
  background-color: #f5f9ff;
}

.action-button.edit {
  color: #0071c2;
}

.action-button.calendar {
  color: #003580;
  border-left: 1px solid #e6f0ff;
}

.action-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}
</style>  