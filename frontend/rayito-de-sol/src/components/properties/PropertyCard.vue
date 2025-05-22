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
        <span>{{ property.location }}</span>
      </div>
      <div class="property-details">
        <div class="property-detail">
          <span>{{ property.bedrooms }} dormitorios</span>
        </div>
        <div class="property-detail">
          <span>{{ property.capacity }} huéspedes</span>
        </div>
        <div class="property-detail">
          <span>€{{ property.price }}/noche</span>
        </div>
        <div class="property-description">
          <p>{{ property.description }}</p>
        </div>
      </div>
    </div>
    <div class="property-actions">
      <button class="action-button edit" @click="$emit('edit')">
        <EditIcon class="action-icon" />
        Editar
      </button>
      <button class="action-button calendar" @click="$emit('calendar')">
        Calendario
      </button>
    </div>
  </div>
</template>

<script setup>
import { EditIcon } from 'lucide-vue-next'
defineProps({ property: Object })
defineEmits(['edit', 'calendar'])
</script>