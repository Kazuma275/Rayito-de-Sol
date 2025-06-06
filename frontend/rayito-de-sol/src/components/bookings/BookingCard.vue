<template>
  <div class="booking-card">
    <div class="booking-header">
      <div class="booking-property-info">
        <img :src="property.image" alt="Property" class="booking-property-image" />
        <div>
          <h3>{{ property.name }}</h3>
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
      
      <div class="booking-guest-info">
        <UserIcon class="guest-icon" />
        <div>
          <span class="guest-label">Huésped</span>
          <span class="guest-value">{{ booking.guestName }}</span>
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
      <button class="action-button view" @click="$emit('view-details', booking.id)">Ver detalles</button>
      <button v-if="booking.status === 'pending'" class="action-button accept" @click="$emit('accept-booking', booking.id)">Aceptar</button>
      <button v-if="booking.status === 'pending'" class="action-button reject" @click="$emit('reject-booking', booking.id)">Rechazar</button>
      <button v-if="booking.status === 'confirmed'" class="action-button message" @click="$emit('send-message', booking.id)">
        <MessageSquareIcon class="action-icon" />
        Mensaje
      </button>
    </div>
  </div>
</template>

<script setup>
import { 
  CalendarIcon, 
  UserIcon, 
  UsersIcon, 
  EuroIcon, 
  MessageSquareIcon 
} from 'lucide-vue-next';

const props = defineProps({
  booking: {
    type: Object,
    required: true
  },
  property: {
    type: Object,
    required: true
  }
});

// Formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  
  const options = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

// Obtener texto de estado
const getStatusText = (status) => {
  const statusMap = {
    'pending': 'Pendiente',
    'confirmed': 'Confirmada',
    'completed': 'Completada',
    'cancelled': 'Cancelada'
  };
  
  return statusMap[status] || status;
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
  color: #003580;
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

.booking-status.completed {
  background-color: #e6f7ee;
  color: #00703c;
}

.booking-status.confirmed {
  background-color: #e6f0ff;
  color: #0071c2;
}

.booking-status.pending {
  background-color: #fff8e6;
  color: #b27b00;
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

.date-item, .booking-guest-info, .booking-guests, .booking-price {
  display: flex;
  align-items: center;
}

.date-icon, .guest-icon, .guests-icon, .price-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.date-label, .guest-label, .guests-label, .price-label {
  display: block;
  font-size: 0.8rem;
  color: #666;
}

.date-value, .guest-value, .guests-value, .price-value {
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

.action-button.accept {
  background-color: #e6f7ee;
  color: #00703c;
  border: 1px solid #00703c;
}

.action-button.accept:hover {
  background-color: #d1f0e0;
}

.action-button.reject {
  background-color: #fff2f0;
  color: #e41c00;
  border: 1px solid #e41c00;
}

.action-button.reject:hover {
  background-color: #ffe5e2;
}

.action-button.message {
  background-color: #e6f0ff;
  color: #0071c2;
  border: 1px solid #0071c2;
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
  
  .booking-dates {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>