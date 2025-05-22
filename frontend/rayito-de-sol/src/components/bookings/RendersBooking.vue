<template>
  <div class="booking-card">
    <div class="booking-header">
      <div class="booking-property-info">
        <img :src="booking.property.image" alt="Property" class="booking-property-image" />
        <div>
          <h3>{{ booking.property.name }}</h3>
          <div class="booking-id">Reserva #{{ booking.id }}</div>
        </div>
      </div>
      <div class="booking-status" :class="booking.status">
        {{ getStatusText(booking.status) }}
      </div>
    </div>
    
    <div class="booking-details">
      <div class="booking-dates">
        <div class="date-item">
          <CalendarIcon class="date-icon" />
          <div>
            <span class="date-label">Llegada</span>
            <span class="date-value">{{ formatDate(booking.checkIn) }}</span>
          </div>
        </div>
        <div class="date-item">
          <CalendarIcon class="date-icon" />
          <div>
            <span class="date-label">Salida</span>
            <span class="date-value">{{ formatDate(booking.checkOut) }}</span>
          </div>
        </div>
      </div>
      
      <div class="booking-guests">
        <UsersIcon class="guests-icon" />
        <div>
          <span class="guests-label">Huéspedes</span>
          <span class="guests-value">{{ booking.guests }}</span>
        </div>
      </div>
      
      <div class="booking-price">
        <EuroIcon class="price-icon" />
        <div>
          <span class="price-label">Total</span>
          <span class="price-value">€{{ booking.total }}</span>
        </div>
      </div>
    </div>
    
    <div class="booking-actions">
      <button class="action-button view" @click="viewDetails">Ver detalles</button>
      <button 
        v-if="booking.status === 'confirmed' && canCancel" 
        class="action-button cancel"
        @click="cancelBooking"
      >
        Cancelar reserva
      </button>
      <button 
        v-if="booking.status === 'confirmed'" 
        class="action-button message"
        @click="contactOwner"
      >
        <MessageSquareIcon class="action-icon" />
        Mensaje
      </button>
    </div>
  </div>
</template>

<script setup>
import { 
  CalendarIcon, 
  UsersIcon, 
  EuroIcon, 
  MessageSquareIcon 
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
  booking: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['viewDetails', 'cancelBooking', 'contactOwner']);

const getStatusText = (status) => {
  switch (status) {
    case 'pending': return 'Pendiente';
    case 'confirmed': return 'Confirmada';
    case 'completed': return 'Completada';
    case 'cancelled': return 'Cancelada';
    default: return status;
  }
};

const formatDate = (dateString) => {
  const options = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const canCancel = computed(() => {
  // Check if booking is within cancellation period (e.g., more than 48 hours before check-in)
  const checkIn = new Date(props.booking.checkIn);
  const now = new Date();
  const diffTime = checkIn.getTime() - now.getTime();
  const diffHours = diffTime / (1000 * 60 * 60);
  
  return diffHours > 48;
});

const viewDetails = () => {
  emit('viewDetails', props.booking.id);
};

const cancelBooking = () => {
  emit('cancelBooking', props.booking.id);
};

const contactOwner = () => {
  emit('contactOwner', props.booking.id);
};
</script>

<style scoped>
.booking-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.booking-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.booking-property-info {
  display: flex;
  align-items: center;
}

.booking-property-image {
  width: 60px;
  height: 60px;
  border-radius: 4px;
  object-fit: cover;
  margin-right: 1rem;
}

.booking-property-info h3 {
  font-size: 1.2rem;
  margin: 0 0 0.25rem;
  color: var(--primary-color, #003580);
}

.booking-id {
  font-size: 0.9rem;
  color: #666;
}

.booking-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.booking-status.confirmed {
  background-color: #e6f0ff;
  color: var(--primary-color, #003580);
}

.booking-status.pending {
  background-color: #fff8e6;
  color: #b27b00;
}

.booking-status.completed {
  background-color: #e6f7ee;
  color: #00703c;
}

.booking-status.cancelled {
  background-color: #fff2f0;
  color: #e41c00;
}

.booking-details {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.booking-dates {
  display: flex;
  gap: 1.5rem;
}

.date-item, .booking-guests, .booking-price {
  display: flex;
  align-items: center;
}

.date-icon, .guests-icon, .price-icon {
  width: 18px;
  height: 18px;
  color: var(--primary-color, #003580);
  margin-right: 0.5rem;
}

.date-label, .guests-label, .price-label {
  display: block;
  font-size: 0.8rem;
  color: #666;
}

.date-value, .guests-value, .price-value {
  font-weight: 500;
}

.booking-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.action-button {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
  display: flex;
  align-items: center;
}

.action-button.view {
  background-color: #f5f5f5;
  color: #333;
  border: 1px solid #ddd;
}

.action-button.view:hover {
  background-color: #eee;
}

.action-button.cancel {
  background-color: #fff2f0;
  color: #e41c00;
  border: 1px solid #e41c00;
}

.action-button.cancel:hover {
  background-color: #ffe5e2;
}

.action-button.message {
  background-color: #e6f0ff;
  color: var(--primary-color, #003580);
  border: 1px solid var(--primary-color, #003580);
}

.action-button.message:hover {
  background-color: #d1e5ff;
}

.action-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

@media (max-width: 768px) {
  .booking-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .booking-status {
    align-self: flex-start;
  }
  
  .booking-details {
    flex-direction: column;
    gap: 1rem;
  }
}

@media (max-width: 576px) {
  .booking-dates {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>