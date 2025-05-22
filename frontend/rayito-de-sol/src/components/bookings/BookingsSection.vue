<template>
  <div class="bookings-section">
    <div class="section-header">
      <h2 class="section-title">Gestión de Reservas</h2>
      <div class="header-actions">
        <button class="export-button">
          <DownloadIcon class="button-icon" />
          Exportar Reservas
        </button>
      </div>
    </div>
    
    <div class="bookings-filter">
      <div class="filter-tabs">
        <button 
          v-for="filter in bookingFilters" 
          :key="filter.id" 
          class="filter-tab" 
          :class="{ active: activeBookingFilter === filter.id }"
          @click="activeBookingFilter = filter.id"
        >
          {{ filter.name }}
          <span class="filter-count">{{ getFilterCount(filter.id) }}</span>
        </button>
      </div>
      
      <div class="filter-controls">
        <div class="filter-property">
          <label>Filtrar por propiedad:</label>
          <select v-model="bookingPropertyFilter" class="property-select">
            <option value="all">Todas las propiedades</option>
            <option v-for="property in properties" :key="property.id" :value="property.id">
              {{ property.name }}
            </option>
          </select>
        </div>
        
        <div class="filter-date">
          <label>Filtrar por fecha:</label>
          <div class="date-inputs">
            <div class="date-input">
              <CalendarIcon class="input-icon" />
              <input type="date" v-model="dateFilter.start" class="date-field" />
            </div>
            <span class="date-separator">-</span>
            <div class="date-input">
              <CalendarIcon class="input-icon" />
              <input type="date" v-model="dateFilter.end" class="date-field" />
            </div>
            <button class="apply-date-filter" @click="applyDateFilter">Aplicar</button>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="filteredBookings.length > 0" class="bookings-list">
      <BookingCard 
        v-for="booking in filteredBookings" 
        :key="booking.id" 
        :booking="booking" 
        :property="getPropertyById(booking.propertyId)" 
        @accept-booking="acceptBooking"
        @reject-booking="rejectBooking"
        @view-details="viewBookingDetails"
        @send-message="sendMessage"
      />
    </div>
    
    <div v-else class="empty-bookings">
      <CalendarOffIcon class="empty-icon" />
      <h3>No hay reservas {{ activeBookingFilter === 'all' ? '' : 'en este estado' }}</h3>
      <p v-if="activeBookingFilter !== 'all'">Prueba a seleccionar otro filtro</p>
      <p v-else>Cuando recibas reservas, aparecerán aquí</p>
    </div>
    
    <div v-if="showPagination" class="bookings-pagination">
      <button 
        class="pagination-button" 
        :disabled="currentPage === 1"
        @click="changePage(currentPage - 1)"
      >
        <ChevronLeftIcon class="pagination-icon" />
      </button>
      
      <div class="pagination-pages">
        <button 
          v-for="page in paginationPages" 
          :key="page" 
          class="page-button" 
          :class="{ active: currentPage === page }"
          @click="changePage(page)"
        >
          {{ page }}
        </button>
      </div>
      
      <button 
        class="pagination-button" 
        :disabled="currentPage === totalPages"
        @click="changePage(currentPage + 1)"
      >
        <ChevronRightIcon class="pagination-icon" />
      </button>
    </div>
    
    <!-- Modal de detalles de reserva -->
    <div v-if="showBookingDetails" class="booking-details-modal" @click="closeBookingDetails">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Detalles de la Reserva #{{ selectedBooking.id }}</h3>
          <button class="close-button" @click="closeBookingDetails">
            <XIcon class="close-icon" />
          </button>
        </div>
        
        <div class="modal-body">
          <div class="booking-property-details">
            <img :src="getPropertyById(selectedBooking.propertyId).image" alt="Property" class="property-image" />
            <div class="property-info">
              <h4>{{ getPropertyById(selectedBooking.propertyId).name }}</h4>
              <p class="property-location">
                <MapPinIcon class="info-icon" />
                {{ getPropertyById(selectedBooking.propertyId).location }}
              </p>
            </div>
          </div>
          
          <div class="booking-info-grid">
            <div class="booking-info-item">
              <h5>Fechas</h5>
              <div class="info-content">
                <CalendarIcon class="info-icon" />
                <div>
                  <p><strong>Llegada:</strong> {{ formatDate(selectedBooking.checkIn) }}</p>
                  <p><strong>Salida:</strong> {{ formatDate(selectedBooking.checkOut) }}</p>
                  <p><strong>Noches:</strong> {{ calculateNights(selectedBooking) }}</p>
                </div>
              </div>
            </div>
            
            <div class="booking-info-item">
              <h5>Huésped</h5>
              <div class="info-content">
                <UserIcon class="info-icon" />
                <div>
                  <p><strong>Nombre:</strong> {{ selectedBooking.guestName }}</p>
                  <p><strong>Email:</strong> {{ selectedBooking.guestEmail || 'No disponible' }}</p>
                  <p><strong>Teléfono:</strong> {{ selectedBooking.guestPhone || 'No disponible' }}</p>
                </div>
              </div>
            </div>
            
            <div class="booking-info-item">
              <h5>Detalles</h5>
              <div class="info-content">
                <InfoIcon class="info-icon" />
                <div>
                  <p><strong>Huéspedes:</strong> {{ selectedBooking.guests }}</p>
                  <p><strong>Estado:</strong> <span :class="['status-badge', selectedBooking.status]">{{ getStatusText(selectedBooking.status) }}</span></p>
                  <p><strong>Fecha de reserva:</strong> {{ formatDate(selectedBooking.bookingDate) }}</p>
                </div>
              </div>
            </div>
            
            <div class="booking-info-item">
              <h5>Pago</h5>
              <div class="info-content">
                <CreditCardIcon class="info-icon" />
                <div>
                  <p><strong>Total:</strong> €{{ selectedBooking.total }}</p>
                  <p><strong>Estado del pago:</strong> <span :class="['payment-status', selectedBooking.paymentStatus]">{{ getPaymentStatusText(selectedBooking.paymentStatus) }}</span></p>
                  <p><strong>Método de pago:</strong> {{ selectedBooking.paymentMethod || 'No especificado' }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="booking-notes" v-if="selectedBooking.notes">
            <h5>Notas</h5>
            <p>{{ selectedBooking.notes }}</p>
          </div>
          
          <div class="booking-timeline">
            <h5>Historial</h5>
            <div class="timeline">
              <div class="timeline-item" v-for="(event, index) in selectedBooking.history" :key="index">
                <div class="timeline-icon" :class="event.type">
                  <component :is="getEventIcon(event.type)" class="event-icon" />
                </div>
                <div class="timeline-content">
                  <p class="event-text">{{ event.text }}</p>
                  <p class="event-date">{{ formatDateTime(event.date) }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-actions">
            <button v-if="selectedBooking.status === 'pending'" class="action-button accept" @click="acceptBooking(selectedBooking.id)">
              <CheckIcon class="action-icon" />
              Aceptar Reserva
            </button>
            <button v-if="selectedBooking.status === 'pending'" class="action-button reject" @click="rejectBooking(selectedBooking.id)">
              <XIcon class="action-icon" />
              Rechazar Reserva
            </button>
            <button class="action-button message" @click="sendMessage(selectedBooking.id)">
              <MessageSquareIcon class="action-icon" />
              Enviar Mensaje
            </button>
            <button v-if="selectedBooking.status === 'confirmed'" class="action-button calendar">
              <CalendarIcon class="action-icon" />
              Añadir al Calendario
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { 
  CalendarIcon, 
  CalendarOffIcon, 
  ChevronLeftIcon, 
  ChevronRightIcon,
  DownloadIcon,
  XIcon,
  CheckIcon,
  UserIcon,
  MapPinIcon,
  InfoIcon,
  CreditCardIcon,
  MessageSquareIcon,
  ClockIcon,
  AlertTriangleIcon,
  CheckCircleIcon
} from 'lucide-vue-next';
import BookingCard from './BookingCard.vue';

const props = defineProps({
  bookings: {
    type: Array,
    required: true
  },
  properties: {
    type: Array,
    required: true
  }
});

// Estado de filtros y paginación
const activeBookingFilter = ref('all');
const bookingPropertyFilter = ref('all');
const dateFilter = ref({ start: '', end: '' });
const currentPage = ref(1);
const itemsPerPage = ref(5);
const showPagination = ref(true);

// Estado para el modal de detalles
const showBookingDetails = ref(false);
const selectedBooking = ref({});

// Filtros predefinidos
const bookingFilters = [
  { id: 'all', name: 'Todas' },
  { id: 'pending', name: 'Pendientes' },
  { id: 'confirmed', name: 'Confirmadas' },
  { id: 'completed', name: 'Completadas' },
  { id: 'cancelled', name: 'Canceladas' }
];

// Reservas filtradas
const filteredBookings = computed(() => {
  let filtered = [...props.bookings];
  
  // Filtrar por estado
  if (activeBookingFilter.value !== 'all') {
    filtered = filtered.filter(booking => booking.status === activeBookingFilter.value);
  }
  
  // Filtrar por propiedad
  if (bookingPropertyFilter.value !== 'all') {
    filtered = filtered.filter(booking => booking.propertyId === parseInt(bookingPropertyFilter.value));
  }
  
  // Filtrar por fecha
  if (dateFilter.value.start && dateFilter.value.end) {
    const startDate = new Date(dateFilter.value.start);
    const endDate = new Date(dateFilter.value.end);
    
    filtered = filtered.filter(booking => {
      const checkIn = new Date(booking.checkIn);
      return checkIn >= startDate && checkIn <= endDate;
    });
  }
  
  // Aplicar paginación
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  
  return filtered.slice(startIndex, endIndex);
});

// Total de páginas para paginación
const totalPages = computed(() => {
  const filteredTotal = props.bookings.filter(booking => {
    // Aplicar filtros para calcular el total
    if (activeBookingFilter.value !== 'all' && booking.status !== activeBookingFilter.value) {
      return false;
    }
    
    if (bookingPropertyFilter.value !== 'all' && booking.propertyId !== parseInt(bookingPropertyFilter.value)) {
      return false;
    }
    
    if (dateFilter.value.start && dateFilter.value.end) {
      const startDate = new Date(dateFilter.value.start);
      const endDate = new Date(dateFilter.value.end);
      const checkIn = new Date(booking.checkIn);
      if (!(checkIn >= startDate && checkIn <= endDate)) {
        return false;
      }
    }
    
    return true;
  }).length;
  
  return Math.ceil(filteredTotal / itemsPerPage.value);
});

// Páginas a mostrar en la paginación
const paginationPages = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;
  
  if (totalPages.value <= maxVisiblePages) {
    // Mostrar todas las páginas si hay pocas
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i);
    }
  } else {
    // Mostrar un subconjunto de páginas con la página actual en el medio
    let startPage = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1);
    
    // Ajustar si estamos cerca del final
    if (endPage === totalPages.value) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
  }
  
  return pages;
});

// Obtener el conteo para cada filtro
const getFilterCount = (filterId) => {
  if (filterId === 'all') {
    return props.bookings.length;
  }
  
  return props.bookings.filter(booking => booking.status === filterId).length;
};

// Obtener propiedad por ID
const getPropertyById = (id) => {
  return props.properties.find(property => property.id === id) || { 
    name: 'Propiedad no encontrada', 
    image: '/placeholder.svg?height=100&width=100',
    location: 'Ubicación desconocida'
  };
};

// Formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  
  const options = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

// Formatear fecha y hora
const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return 'N/A';
  
  const options = { 
    day: 'numeric', 
    month: 'short', 
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return new Date(dateTimeString).toLocaleString('es-ES', options);
};

// Calcular noches
const calculateNights = (booking) => {
  if (!booking.checkIn || !booking.checkOut) return 0;
  
  const checkIn = new Date(booking.checkIn);
  const checkOut = new Date(booking.checkOut);
  const diffTime = checkOut - checkIn;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays;
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

// Obtener texto de estado de pago
const getPaymentStatusText = (status) => {
  const statusMap = {
    'paid': 'Pagado',
    'pending': 'Pendiente',
    'refunded': 'Reembolsado',
    'failed': 'Fallido'
  };
  
  return statusMap[status] || status;
};

// Obtener icono para eventos del timeline
const getEventIcon = (type) => {
  const iconMap = {
    'created': ClockIcon,
    'confirmed': CheckCircleIcon,
    'cancelled': XIcon,
    'completed': CheckIcon,
    'message': MessageSquareIcon,
    'payment': CreditCardIcon,
    'warning': AlertTriangleIcon
  };
  
  return iconMap[type] || InfoIcon;
};

// Métodos para cambiar de página
const changePage = (page) => {
  currentPage.value = page;
};

// Aplicar filtro de fecha
const applyDateFilter = () => {
  currentPage.value = 1; // Resetear a la primera página al aplicar filtros
};

// Métodos para acciones de reservas
const viewBookingDetails = (bookingId) => {
  const booking = props.bookings.find(b => b.id === bookingId);
  if (booking) {
    selectedBooking.value = booking;
    showBookingDetails.value = true;
  }
};

const closeBookingDetails = () => {
  showBookingDetails.value = false;
};

const acceptBooking = (bookingId) => {
  console.log(`Aceptando reserva ${bookingId}`);
  // Aquí iría la lógica para aceptar la reserva
  // En una aplicación real, esto enviaría una solicitud al backend
};

const rejectBooking = (bookingId) => {
  console.log(`Rechazando reserva ${bookingId}`);
  // Aquí iría la lógica para rechazar la reserva
};

const sendMessage = (bookingId) => {
  console.log(`Enviando mensaje para la reserva ${bookingId}`);
  // Aquí iría la lógica para abrir el chat o enviar un mensaje
};
</script>

<style scoped>
.bookings-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 60vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
  position: relative;
  overflow: hidden;
}

.bookings-section::before {
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

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  position: relative;
  z-index: 1;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.export-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.export-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Filtros */
.bookings-filter {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  position: relative;
  z-index: 1;
}

.filter-tabs {
  display: flex;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.filter-tab {
  flex: 1;
  background: none;
  border: none;
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 0.95rem;
  color: #64748b;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.filter-tab:not(:last-child) {
  border-right: 1px solid #e6f0ff;
}

.filter-tab.active {
  background-color: #0071c2;
  color: white;
}

.filter-count {
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  padding: 0.1rem 0.5rem;
  font-size: 0.8rem;
  min-width: 24px;
  text-align: center;
}

.filter-tab:not(.active) .filter-count {
  background-color: #e6f0ff;
  color: #0071c2;
}

.filter-controls {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: flex-end;
}

.filter-property, .filter-date {
  flex: 1;
  min-width: 250px;
}

.filter-property label, .filter-date label {
  display: block;
  font-weight: 500;
  color: #003580;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.property-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: all 0.3s;
}

.property-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.date-inputs {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.date-input {
  position: relative;
  flex: 1;
}

.input-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #0071c2;
  width: 18px;
  height: 18px;
}

.date-field {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: all 0.3s;
}

.date-field:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.date-separator {
  color: #64748b;
  font-weight: 500;
}

.apply-date-filter {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.apply-date-filter:hover {
  background-color: #005999;
}

/* Lista de reservas */
.bookings-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  position: relative;
  z-index: 1;
}

/* Estado vacío */
.empty-bookings {
  text-align: center;
  padding: 4rem 2rem;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  position: relative;
  z-index: 1;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1.5rem;
}

.empty-bookings h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.empty-bookings p {
  color: #64748b;
  margin-bottom: 0;
}

/* Paginación */
.bookings-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.pagination-button {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  background-color: white;
  color: #0071c2;
  cursor: pointer;
  transition: all 0.3s;
}

.pagination-button:hover:not(:disabled) {
  background-color: #f0f7ff;
}

.pagination-button:disabled {
  color: #94a3b8;
  cursor: not-allowed;
}

.pagination-icon {
  width: 18px;
  height: 18px;
}

.pagination-pages {
  display: flex;
  gap: 0.5rem;
}

.page-button {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  background-color: white;
  color: #1e293b;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.page-button:hover {
  background-color: #f0f7ff;
}

.page-button.active {
  background-color: #0071c2;
  color: white;
  border-color: #0071c2;
}

/* Modal de detalles */
.booking-details-modal {
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
}

.modal-content {
  background-color: white;
  border-radius: 16px;
  width: 90%;
  max-width: 800px;
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

.modal-header h3 {
  font-size: 1.4rem;
  margin: 0;
  font-weight: 600;
}

.close-button {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
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
  width: 18px;
  height: 18px;
}

.modal-body {
  padding: 2rem;
}

.booking-property-details {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.property-image {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  object-fit: cover;
}

.property-info {
  flex: 1;
}

.property-info h4 {
  font-size: 1.3rem;
  color: #003580;
  margin: 0 0 0.5rem;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  margin-bottom: 0.5rem;
}

.info-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.booking-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.booking-info-item {
  background-color: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e6f0ff;
}

.booking-info-item h5 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.info-content {
  display: flex;
  gap: 1rem;
}

.info-content p {
  margin: 0 0 0.5rem;
  color: #1e293b;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.status-badge.pending {
  background-color: #fff8e6;
  color: #b27b00;
}

.status-badge.confirmed {
  background-color: #e6f7ee;
  color: #00703c;
}

.status-badge.completed {
  background-color: #e6f0ff;
  color: #0071c2;
}

.status-badge.cancelled {
  background-color: #fff2f0;
  color: #e41c00;
}

.payment-status {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.payment-status.paid {
  background-color: #e6f7ee;
  color: #00703c;
}

.payment-status.pending {
  background-color: #fff8e6;
  color: #b27b00;
}

.payment-status.refunded {
  background-color: #e6f0ff;
  color: #0071c2;
}

.payment-status.failed {
  background-color: #fff2f0;
  color: #e41c00;
}

.booking-notes {
  background-color: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  border: 1px solid #e6f0ff;
}

.booking-notes h5 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.booking-notes p {
  margin: 0;
  color: #1e293b;
  line-height: 1.6;
}

.booking-timeline {
  margin-bottom: 2rem;
}

.booking-timeline h5 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.timeline {
  position: relative;
  padding-left: 2rem;
}

.timeline::before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: 9px;
  width: 2px;
  background-color: #e6f0ff;
}

.timeline-item {
  position: relative;
  margin-bottom: 1.5rem;
}

.timeline-item:last-child {
  margin-bottom: 0;
}

.timeline-icon {
  position: absolute;
  left: -2rem;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.timeline-icon.created {
  background-color: #e6f0ff;
}

.timeline-icon.confirmed {
  background-color: #e6f7ee;
}

.timeline-icon.cancelled {
  background-color: #fff2f0;
}

.timeline-icon.completed {
  background-color: #e6f7ee;
}

.timeline-icon.message {
  background-color: #e6f0ff;
}

.timeline-icon.payment {
  background-color: #e6f7ee;
}

.timeline-icon.warning {
  background-color: #fff8e6;
}

.event-icon {
  width: 12px;
  height: 12px;
  color: #0071c2;
}

.timeline-icon.confirmed .event-icon,
.timeline-icon.completed .event-icon,
.timeline-icon.payment .event-icon {
  color: #00703c;
}

.timeline-icon.cancelled .event-icon {
  color: #e41c00;
}

.timeline-icon.warning .event-icon {
  color: #b27b00;
}

.timeline-content {
  padding-bottom: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.timeline-item:last-child .timeline-content {
  border-bottom: none;
  padding-bottom: 0;
}

.event-text {
  margin: 0 0 0.25rem;
  color: #1e293b;
}

.event-date {
  margin: 0;
  font-size: 0.85rem;
  color: #64748b;
}

.modal-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: 2rem;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
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

.action-button.calendar {
  background-color: #fff8e6;
  color: #b27b00;
  border: 1px solid #b27b00;
}

.action-button.calendar:hover {
  background-color: #ffefc8;
}

.action-icon {
  width: 18px;
  height: 18px;
}

/* Responsive */
@media (max-width: 992px) {
  .bookings-section {
    padding: 1.5rem;
  }
  
  .booking-info-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .filter-tabs {
    width: 100%;
    overflow-x: auto;
  }
  
  .filter-tab {
    white-space: nowrap;
  }
  
  .filter-controls {
    flex-direction: column;
    width: 100%;
  }
  
  .date-inputs {
    flex-direction: column;
    gap: 1rem;
  }
  
  .date-separator {
    display: none;
  }
  
  .booking-property-details {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .modal-actions {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .bookings-section {
    padding: 1rem;
  }
  
  .modal-content {
    width: 95%;
  }
  
  .modal-body {
    padding: 1.5rem;
  }
  
  .action-button {
    width: 100%;
    justify-content: center;
  }
}
</style>