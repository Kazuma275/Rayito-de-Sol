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

    <div class="booking-details-row">
      <div class="booking-detail">
        <CalendarIcon class="detail-icon" />
        <span class="detail-label">Llegada</span>
        <span class="detail-value">{{ formatDate(booking.checkIn) }}</span>
      </div>
      <div class="booking-detail">
        <CalendarIcon class="detail-icon" />
        <span class="detail-label">Salida</span>
        <span class="detail-value">{{ formatDate(booking.checkOut) }}</span>
      </div>
      <div class="booking-detail">
        <UserIcon class="detail-icon" />
        <span class="detail-label">Huésped</span>
        <span class="detail-value">{{ booking.guestName || 'No disponible' }}</span>
      </div>
      <div class="booking-detail">
        <UsersIcon class="detail-icon" />
        <span class="detail-label">Huéspedes</span>
        <span class="detail-value">{{ booking.guests }}</span>
      </div>
      <div class="booking-detail">
        <EuroIcon class="detail-icon" />
        <span class="detail-label">Total</span>
        <span class="detail-value">€{{ booking.total || booking.total_price || 0 }}</span>
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
    'cancelled': 'Cancelada',
    'active': 'Activa'
  };
  return statusMap[status] || status;
};
</script>

<style scoped>
.booking-card {
  background: linear-gradient(to bottom, #ffffff, #f8fafc);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.08);
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 113, 194, 0.1);
}

.booking-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 53, 128, 0.12);
}

.booking-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(0, 113, 194, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(0, 53, 128, 0.03) 0%, transparent 60%);
  z-index: 0;
}

/* Header con propiedad, nombre y estado */
.booking-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 1;
}

.booking-property-info {
  display: flex;
  align-items: center;
}

.booking-property-image {
  width: 64px;
  height: 64px;
  border-radius: 8px;
  object-fit: cover;
  margin-right: 1rem;
  box-shadow: 0 2px 6px rgba(0, 53, 128, 0.1);
  border: 2px solid white;
}

.booking-property-info h3 {
  font-size: 1.2rem;
  margin: 0 0 0.25rem;
  color: #003580;
  font-weight: 600;
}

.booking-id {
  font-size: 0.9rem;
  color: #64748b;
  margin-top: 0.15rem;
}

.booking-status {
  padding: 0.35rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  text-align: center;
  min-width: 110px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  letter-spacing: 0.3px;
}

.booking-status.completed {
  background: linear-gradient(135deg, #e6f7ee 0%, #d1f0e0 100%);
  color: #00703c;
}

.booking-status.confirmed {
  background: linear-gradient(135deg, #e6f0ff 0%, #d1e5ff 100%);
  color: #0071c2;
}

.booking-status.pending, .booking-status.active {
  background: linear-gradient(135deg, #fff8e6 0%, #ffefc8 100%);
  color: #b27b00;
}

.booking-status.cancelled {
  background: linear-gradient(135deg, #fff2f0 0%, #ffe5e2 100%);
  color: #e41c00;
}

/* Fila de detalles alineados */
.booking-details-row {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  position: relative;
  z-index: 1;
  background-color: rgba(255, 255, 255, 0.7);
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid rgba(0, 113, 194, 0.08);
}

.booking-detail {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  min-width: 120px;
}

.detail-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.detail-label {
  font-size: 0.85rem;
  color: #64748b;
  margin-left: 0.25rem;
}

.detail-value {
  font-weight: 600;
  color: #003580;
  margin-left: 0.35rem;
}

/* Acciones */
.booking-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-top: 1rem;
  position: relative;
  z-index: 1;
}

.action-button {
  padding: 0.6rem 1.2rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  border: none;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.action-button.view {
  background: linear-gradient(135deg, #f5f5f5 0%, #e6e6e6 100%);
  color: #1e293b;
  border: 1px solid #e2e8f0;
}

.action-button.view:hover {
  background: linear-gradient(135deg, #e6e6e6 0%, #d9d9d9 100%);
  transform: translateY(-1px);
}

.action-button.accept {
  background: linear-gradient(135deg, #e6f7ee 0%, #d1f0e0 100%);
  color: #00703c;
  border: 1px solid rgba(0, 112, 60, 0.2);
}

.action-button.accept:hover {
  background: linear-gradient(135deg, #d1f0e0 0%, #b3e6c8 100%);
  transform: translateY(-1px);
}

.action-button.reject {
  background: linear-gradient(135deg, #fff2f0 0%, #ffe5e2 100%);
  color: #e41c00;
  border: 1px solid rgba(228, 28, 0, 0.2);
}

.action-button.reject:hover {
  background: linear-gradient(135deg, #ffe5e2 0%, #ffd1cc 100%);
  transform: translateY(-1px);
}

.action-button.message {
  background: linear-gradient(135deg, #e6f0ff 0%, #d1e5ff 100%);
  color: #0071c2;
  border: 1px solid rgba(0, 113, 194, 0.2);
}

.action-button.message:hover {
  background: linear-gradient(135deg, #d1e5ff 0%, #b3d7ff 100%);
  transform: translateY(-1px);
}

.action-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

/* Responsive */
@media (max-width: 900px) {
  .booking-details-row {
    gap: 1.2rem;
    padding: 0.8rem;
  }
  .booking-detail {
    min-width: 100px;
  }
}

@media (max-width: 600px) {
  .booking-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.7rem;
  }
  
  .booking-status {
    min-width: 80px;
    align-self: flex-start;
  }
  
  .booking-details-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.7rem;
  }
  
  .booking-actions {
    flex-direction: column;
    gap: 0.6rem;
    width: 100%;
  }
  
  .action-button {
    width: 100%;
    justify-content: center;
  }
}
</style>
