<template>
  <div v-if="show" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2 class="modal-title">Detalles de la Reserva</h2>
        <button @click="closeModal" class="close-button">
          <XIcon class="icon" />
        </button>
      </div>

      <div class="modal-body">
        <!-- Property Information -->
        <div class="property-section">
          <div class="property-header">
            <img 
              :src="booking.property.image" 
              :alt="booking.property.name"
              class="property-image"
              @error="handleImageError"
            />
            <div class="property-info">
              <h3 class="property-name">{{ booking.property.name }}</h3>
              <div class="property-location">
                <MapPinIcon class="location-icon" />
                <span>{{ booking.property.location }}</span>
              </div>
              <div class="property-details">
                <span class="detail-item">
                  <BedIcon class="detail-icon" />
                  {{ booking.property.bedrooms }} habitaciones
                </span>
                <span class="detail-item">
                  <UsersIcon class="detail-icon" />
                  Hasta {{ booking.property.maxGuests }} huéspedes
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Status -->
        <div class="status-section">
          <div class="status-badge" :class="booking.status">
            <component :is="getStatusIcon(booking.status)" class="status-icon" />
            {{ getStatusText(booking.status) }}
          </div>
          <div class="booking-reference">
            Reserva #{{ booking.bookingReference }}
          </div>
        </div>

        <!-- Dates and Guest Information -->
        <div class="booking-info-section">
          <div class="info-grid">
            <div class="info-card">
              <div class="info-label">Check-in</div>
              <div class="info-value">
                <CalendarIcon class="info-icon" />
                <div>
                  <div class="date">{{ formatDate(booking.checkIn) }}</div>
                  <div class="time">15:00</div>
                </div>
              </div>
            </div>

            <div class="info-card">
              <div class="info-label">Check-out</div>
              <div class="info-value">
                <CalendarIcon class="info-icon" />
                <div>
                  <div class="date">{{ formatDate(booking.checkOut) }}</div>
                  <div class="time">11:00</div>
                </div>
              </div>
            </div>

            <div class="info-card">
              <div class="info-label">Huéspedes</div>
              <div class="info-value">
                <UsersIcon class="info-icon" />
                <span>{{ booking.guests }} {{ booking.guests === 1 ? 'huésped' : 'huéspedes' }}</span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-label">Noches</div>
              <div class="info-value">
                <ClockIcon class="info-icon" />
                <span>{{ calculateNights(booking.checkIn, booking.checkOut) }} noches</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Guest Information -->
        <div class="guest-section">
          <h4 class="section-title">Información del Huésped</h4>
          <div class="guest-info">
            <div class="guest-item">
              <UserIcon class="guest-icon" />
              <span>{{ booking.guestName }}</span>
            </div>
            <div class="guest-item">
              <MailIcon class="guest-icon" />
              <span>{{ booking.guestEmail }}</span>
            </div>
            <div class="guest-item" v-if="booking.guestPhone">
              <PhoneIcon class="guest-icon" />
              <span>{{ booking.guestPhone }}</span>
            </div>
          </div>
        </div>

        <!-- Price Breakdown -->
        <div class="price-section">
          <h4 class="section-title">Desglose de Precios</h4>
          <div class="price-breakdown">
            <div class="price-item">
              <span>€{{ booking.pricePerNight }} x {{ calculateNights(booking.checkIn, booking.checkOut) }} noches</span>
              <span>€{{ booking.subtotal }}</span>
            </div>
            <div class="price-item">
              <span>Tarifa de servicio</span>
              <span>€{{ booking.serviceFee }}</span>
            </div>
            <div class="price-item" v-if="booking.cleaningFee">
              <span>Tarifa de limpieza</span>
              <span>€{{ booking.cleaningFee }}</span>
            </div>
            <div class="price-item" v-if="booking.taxes">
              <span>Impuestos</span>
              <span>€{{ booking.taxes }}</span>
            </div>
            <div class="price-item total">
              <span>Total</span>
              <span>€{{ booking.totalPrice }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="payment-section" v-if="booking.paymentIntentId">
          <h4 class="section-title">Información de Pago</h4>
          <div class="payment-info">
            <div class="payment-item">
              <CreditCardIcon class="payment-icon" />
              <span>ID de Pago: {{ booking.paymentIntentId }}</span>
            </div>
            <div class="payment-status">
              <CheckCircleIcon class="payment-status-icon" />
              <span>Pago procesado exitosamente</span>
            </div>
          </div>
        </div>

        <!-- Booking Timeline -->
        <div class="timeline-section">
          <h4 class="section-title">Historial de la Reserva</h4>
          <div class="timeline">
            <div class="timeline-item">
              <div class="timeline-icon">
                <CheckIcon class="icon" />
              </div>
              <div class="timeline-content">
                <div class="timeline-title">Reserva creada</div>
                <div class="timeline-date">{{ formatDateTime(booking.createdAt) }}</div>
              </div>
            </div>
            <!-- Add more timeline items based on booking status -->
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button 
          v-if="canCancel(booking)"
          @click="cancelBooking"
          class="action-button cancel-button"
        >
          <XIcon class="button-icon" />
          Cancelar Reserva
        </button>
        
        <button 
          @click="contactOwner"
          class="action-button contact-button"
        >
          <MessageSquareIcon class="button-icon" />
          Contactar Propietario
        </button>
        
        <button 
          @click="viewProperty"
          class="action-button view-button"
        >
          <HomeIcon class="button-icon" />
          Ver Propiedad
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  XIcon,
  MapPinIcon,
  BedIcon,
  UsersIcon,
  CalendarIcon,
  ClockIcon,
  UserIcon,
  MailIcon,
  PhoneIcon,
  CreditCardIcon,
  CheckCircleIcon,
  CheckIcon,
  MessageSquareIcon,
  HomeIcon,
  AlertCircleIcon,
  XCircleIcon
} from 'lucide-vue-next'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  booking: {
    type: Object,
    required: true
  },
  formatDate: {
    type: Function,
    required: true
  },
  formatDateTime: {
    type: Function,
    required: true
  },
  getStatusText: {
    type: Function,
    required: true
  },
  canCancel: {
    type: Function,
    required: true
  },
  calculateNights: {
    type: Function,
    required: true
  }
})

// Emits
const emit = defineEmits([
  'close',
  'cancel-booking',
  'contact-owner',
  'view-property'
])

// Methods
const closeModal = () => {
  emit('close')
}

const cancelBooking = () => {
  emit('cancel-booking', props.booking)
}

const contactOwner = () => {
  emit('contact-owner', props.booking)
}

const viewProperty = () => {
  emit('view-property', props.booking.property)
}

const handleImageError = (event) => {
  event.target.src = '/img/placeholder.jpg'
}

const getStatusIcon = (status) => {
  const icons = {
    pending: AlertCircleIcon,
    confirmed: CheckCircleIcon,
    active: CheckIcon,
    completed: CheckCircleIcon,
    cancelled: XCircleIcon
  }
  return icons[status] || AlertCircleIcon
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background-color: white;
  border-radius: 12px;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 24px 0 24px;
  border-bottom: 1px solid #e5e7eb;
  margin-bottom: 24px;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 6px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
}

.close-button:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 0 24px;
}

.property-section {
  margin-bottom: 24px;
}

.property-header {
  display: flex;
  gap: 16px;
}

.property-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  object-fit: cover;
}

.property-info {
  flex: 1;
}

.property-name {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 8px;
}

.location-icon {
  width: 16px;
  height: 16px;
}

.property-details {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  color: #6b7280;
}

.detail-icon {
  width: 14px;
  height: 14px;
}

.status-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding: 16px;
  background-color: #f9fafb;
  border-radius: 8px;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-badge.pending {
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.confirmed {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-badge.active {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.completed {
  background-color: #e5e7eb;
  color: #374151;
}

.status-badge.cancelled {
  background-color: #fee2e2;
  color: #991b1b;
}

.status-icon {
  width: 14px;
  height: 14px;
}

.booking-reference {
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.booking-info-section {
  margin-bottom: 24px;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 16px;
}

.info-card {
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.info-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.info-value {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: #1f2937;
}

.info-icon {
  width: 18px;
  height: 18px;
  color: #3b82f6;
}

.date {
  font-size: 16px;
}

.time {
  font-size: 12px;
  color: #6b7280;
  font-weight: normal;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 16px 0;
}

.guest-section,
.price-section,
.payment-section,
.timeline-section {
  margin-bottom: 24px;
}

.guest-info {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.guest-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #374151;
}

.guest-icon {
  width: 16px;
  height: 16px;
  color: #6b7280;
}

.price-breakdown {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

.price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
}

.price-item:last-child {
  border-bottom: none;
}

.price-item.total {
  background-color: #f9fafb;
  font-weight: 600;
  font-size: 16px;
}

.payment-info {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.payment-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #374151;
}

.payment-icon {
  width: 16px;
  height: 16px;
  color: #6b7280;
}

.payment-status {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #059669;
  font-size: 14px;
  font-weight: 500;
}

.payment-status-icon {
  width: 16px;
  height: 16px;
}

.timeline {
  position: relative;
}

.timeline-item {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.timeline-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #10b981;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.timeline-icon .icon {
  width: 16px;
  height: 16px;
  color: white;
}

.timeline-content {
  flex: 1;
  padding-top: 4px;
}

.timeline-title {
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.timeline-date {
  font-size: 14px;
  color: #6b7280;
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid #e5e7eb;
  flex-wrap: wrap;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

.cancel-button {
  background-color: #ef4444;
  color: white;
}

.cancel-button:hover {
  background-color: #dc2626;
}

.contact-button {
  background-color: #f3f4f6;
  color: #374151;
  border-color: #d1d5db;
}

.contact-button:hover {
  background-color: #e5e7eb;
}

.view-button {
  background-color: #3b82f6;
  color: white;
}

.view-button:hover {
  background-color: #2563eb;
}

.button-icon {
  width: 16px;
  height: 16px;
}

.icon {
  width: 16px;
  height: 16px;
}

/* Responsive Design */
@media (max-width: 640px) {
  .modal-overlay {
    padding: 10px;
  }
  
  .modal-content {
    max-height: 95vh;
  }
  
  .property-header {
    flex-direction: column;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-footer {
    flex-direction: column;
  }
  
  .action-button {
    justify-content: center;
  }
}
</style>
