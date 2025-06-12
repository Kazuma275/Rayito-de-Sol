<template>
  <section class="renters-bookings-section">
    <div class="section-header">
      <h2 class="section-title">Mis Reservas</h2>
      <div class="header-actions">
        <button class="search-button" @click="showSearch = !showSearch">
          <SearchIcon class="icon" />
          Buscar
        </button>
      </div>
    </div>

    <!-- Search Bar -->
    <div v-if="showSearch" class="search-container">
      <div class="search-bar">
        <SearchIcon class="search-icon" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por propiedad, ubicación o ID de reserva..."
          class="search-input"
        />
        <button v-if="searchQuery" @click="searchQuery = ''" class="clear-search">
          <XIcon class="icon" />
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bookings-filters">
      <div class="filter-tabs">
        <button
          v-for="filter in bookingFilters"
          :key="filter.id"
          class="filter-tab"
          :class="{ active: activeFilter === filter.id }"
          @click="$emit('change-filter', filter.id)"
        >
          <component :is="getFilterIcon(filter.id)" class="filter-icon" />
          {{ filter.name }}
          <span v-if="getFilterCount(filter.id)" class="filter-count">
            {{ getFilterCount(filter.id) }}
          </span>
        </button>
      </div>

      <div class="filter-actions">
        <select v-model="sortBy" class="sort-select">
          <option value="date-desc">Más recientes</option>
          <option value="date-asc">Más antiguos</option>
          <option value="checkin-desc">Check-in próximo</option>
          <option value="checkin-asc">Check-in lejano</option>
          <option value="price-desc">Precio mayor</option>
          <option value="price-asc">Precio menor</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Cargando reservas...</p>
    </div>

    <!-- Bookings List -->
    <div v-else-if="filteredAndSortedBookings.length > 0" class="bookings-list">
      <div
        v-for="booking in filteredAndSortedBookings"
        :key="booking.id"
        class="booking-card"
        :class="{ 'booking-card--past': isPastBooking(booking) }"
      >
        <!-- Booking Header -->
        <div class="booking-header">
          <div class="booking-property">
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
              <div class="booking-id">Reserva #{{ booking.bookingReference || booking.id }}</div>
            </div>
          </div>

          <div class="booking-status-container">
            <div class="booking-status" :class="booking.status">
              <component :is="getStatusIcon(booking.status)" class="status-icon" />
              {{ getStatusText(booking.status) }}
            </div>
            <div class="booking-actions-menu">
              <button class="menu-button" @click="toggleBookingMenu(booking.id)">
                <MoreVerticalIcon class="icon" />
              </button>
              <div
                v-if="activeBookingMenu === booking.id"
                class="menu-dropdown"
                @click.stop
              >
                <button @click="$emit('view-details', booking)" class="menu-item">
                  <EyeIcon class="menu-icon" />
                  Ver detalles
                </button>
                <button
                  v-if="canCancel(booking)"
                  @click="$emit('cancel-booking', booking)"
                  class="menu-item menu-item--danger"
                >
                  <XIcon class="menu-icon" />
                  Cancelar reserva
                </button>
                <button @click="$emit('contact-owner', booking)" class="menu-item">
                  <MessageSquareIcon class="menu-icon" />
                  Contactar propietario
                </button>
                <button @click="$emit('view-property', booking.property)" class="menu-item">
                  <HomeIcon class="menu-icon" />
                  Ver propiedad
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Details -->
        <div class="booking-details">
          <div class="booking-dates">
            <div class="date-section">
              <div class="date-label">Check-in</div>
              <div class="date-value">
                <CalendarIcon class="date-icon" />
                <span>{{ formatDate(booking.checkIn) }}</span>
              </div>
              <div class="date-time">15:00</div>
            </div>

            <div class="date-separator">
              <ArrowRightIcon class="arrow-icon" />
            </div>

            <div class="date-section">
              <div class="date-label">Check-out</div>
              <div class="date-value">
                <CalendarIcon class="date-icon" />
                <span>{{ formatDate(booking.checkOut) }}</span>
              </div>
              <div class="date-time">11:00</div>
            </div>
          </div>

          <div class="booking-info">
            <div class="info-item">
              <UsersIcon class="info-icon" />
              <span>{{ booking.guests }} huéspedes</span>
            </div>
            <div class="info-item">
              <ClockIcon class="info-icon" />
              <span>{{ calculateNights(booking.checkIn, booking.checkOut) }} noches</span>
            </div>
            <div class="info-item">
              <EuroIcon class="info-icon" />
              <span>€{{ booking.totalPrice }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="booking-actions">
          <button
            @click="$emit('view-details', booking)"
            class="action-button action-button--primary"
          >
            <EyeIcon class="action-icon" />
            Ver detalles
          </button>

          <button
            v-if="canCancel(booking)"
            @click="$emit('cancel-booking', booking)"
            class="action-button action-button--danger"
          >
            <XIcon class="action-icon" />
            Cancelar
          </button>

          <button
            @click="$emit('contact-owner', booking)"
            class="action-button action-button--secondary"
          >
            <MessageSquareIcon class="action-icon" />
            Contactar
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state">
      <div class="empty-icon-container">
        <CalendarOffIcon class="empty-icon" />
      </div>
      <h3 class="empty-title">
        {{ getEmptyStateTitle() }}
      </h3>
      <p class="empty-description">
        {{ getEmptyStateDescription() }}
      </p>
      <button
        v-if="activeFilter === 'all'"
        @click="$emit('change-tab', 'search')"
        class="empty-action-button"
      >
        Buscar propiedades
      </button>
    </div>

    <!-- Booking Details Modal -->
    <BookingDetailsModal
      :show="showBookingDetails"
      :booking="selectedBooking"
      :format-date="formatDate"
      :format-date-time="formatDateTime"
      :get-status-text="getStatusText"
      :can-cancel="canCancel"
      :calculate-nights="calculateNights"
      @close="$emit('close-details')"
      @cancel-booking="$emit('cancel-booking', $event)"
      @contact-owner="$emit('contact-owner', $event)"
      @view-property="$emit('view-property', $event)"
    />
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  SearchIcon,
  XIcon,
  MapPinIcon,
  MoreVerticalIcon,
  EyeIcon,
  MessageSquareIcon,
  HomeIcon,
  CalendarIcon,
  ArrowRightIcon,
  UsersIcon,
  ClockIcon,
  EuroIcon,
  CheckIcon,
  StarIcon,
  CalendarOffIcon,
  BedIcon,
  UserIcon,
  AlertCircleIcon,
  CheckCircleIcon,
  XCircleIcon
} from 'lucide-vue-next'
import BookingDetailsModal from './BookingDetailsModal.vue'

// Props
const props = defineProps({
  bookings: {
    type: Array,
    default: () => []
  },
  bookingFilters: {
    type: Array,
    default: () => []
  },
  activeFilter: {
    type: String,
    default: 'all'
  },
  showBookingDetails: {
    type: Boolean,
    default: false
  },
  selectedBooking: {
    type: Object,
    default: null
  },
  isLoading: {
    type: Boolean,
    default: false
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
  },
  getEventIcon: {
    type: Function,
    required: true
  }
})

// Emits
const emit = defineEmits([
  'change-filter',
  'view-details',
  'cancel-booking',
  'contact-owner',
  'close-details',
  'view-property',
  'change-tab'
])

// Local state
const showSearch = ref(false)
const searchQuery = ref('')
const sortBy = ref('date-desc')
const activeBookingMenu = ref(null)

// Computed properties
const filteredAndSortedBookings = computed(() => {
  let filtered = props.bookings

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(booking =>
      booking.property.name.toLowerCase().includes(query) ||
      booking.property.location.toLowerCase().includes(query) ||
      booking.id.toString().toLowerCase().includes(query) ||
      (booking.bookingReference && booking.bookingReference.toLowerCase().includes(query))
    )
  }

  // Sort bookings
  return filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'date-desc':
        return new Date(b.createdAt) - new Date(a.createdAt)
      case 'date-asc':
        return new Date(a.createdAt) - new Date(b.createdAt)
      case 'checkin-desc':
        return new Date(b.checkIn) - new Date(a.checkIn)
      case 'checkin-asc':
        return new Date(a.checkIn) - new Date(b.checkIn)
      case 'price-desc':
        return b.totalPrice - a.totalPrice
      case 'price-asc':
        return a.totalPrice - b.totalPrice
      default:
        return 0
    }
  })
})

// Methods
const getFilterIcon = (filterId) => {
  const icons = {
    all: CalendarIcon,
    upcoming: ClockIcon,
    active: CheckCircleIcon,
    completed: CheckIcon,
    cancelled: XCircleIcon,
    pending: AlertCircleIcon,
    confirmed: CheckCircleIcon
  }
  return icons[filterId] || CalendarIcon
}

const getFilterCount = (filterId) => {
  if (filterId === 'all') return null
  return props.bookings.filter(booking => booking.status === filterId).length
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

const isPastBooking = (booking) => {
  return new Date(booking.checkOut) < new Date()
}

const toggleBookingMenu = (bookingId) => {
  activeBookingMenu.value = activeBookingMenu.value === bookingId ? null : bookingId
}

const handleImageError = (event) => {
  event.target.src = '/img/placeholder.jpg'
}

const getEmptyStateTitle = () => {
  const titles = {
    all: 'No tienes reservas',
    upcoming: 'No tienes reservas próximas',
    active: 'No tienes reservas activas',
    completed: 'No tienes reservas completadas',
    cancelled: 'No tienes reservas canceladas',
    pending: 'No tienes reservas pendientes',
    confirmed: 'No tienes reservas confirmadas'
  }
  return titles[props.activeFilter] || 'No se encontraron reservas'
}

const getEmptyStateDescription = () => {
  const descriptions = {
    all: 'Cuando hagas una reserva, aparecerá aquí.',
    upcoming: 'Tus próximas reservas aparecerán aquí.',
    active: 'No tienes ninguna estancia activa en este momento.',
    completed: 'Tus reservas completadas aparecerán aquí.',
    cancelled: 'No tienes reservas canceladas.',
    pending: 'No tienes reservas pendientes de confirmación.',
    confirmed: 'No tienes reservas confirmadas.'
  }
  return descriptions[props.activeFilter] || 'Prueba a cambiar los filtros.'
}

// Close menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.booking-actions-menu')) {
    activeBookingMenu.value = null
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.renters-bookings-section {
  padding: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.section-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.search-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  color: #495057;
  cursor: pointer;
  transition: all 0.2s ease;
}

.search-button:hover {
  background-color: #e9ecef;
}

.search-container {
  margin-bottom: 24px;
}

.search-bar {
  position: relative;
  max-width: 500px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #6c757d;
}

.search-input {
  width: 100%;
  padding: 12px 12px 12px 44px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  font-size: 14px;
  background-color: #fff;
}

.search-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

.clear-search {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
}

.clear-search:hover {
  background-color: #f8f9fa;
}

.bookings-filters {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.filter-tabs {
  display: flex;
  gap: 4px;
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 4px;
}

.filter-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: none;
  border: none;
  border-radius: 6px;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 14px;
  font-weight: 500;
}

.filter-tab.active {
  background-color: #fff;
  color: #0071c2;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-icon {
  width: 16px;
  height: 16px;
}

.filter-count {
  background-color: #0071c2;
  color: white;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
}

.filter-tab.active .filter-count {
  background-color: #0071c2;
}

.sort-select {
  padding: 8px 12px;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  background-color: #fff;
  color: #495057;
  font-size: 14px;
  cursor: pointer;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #6c757d;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f8f9fa;
  border-top: 3px solid #0071c2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.bookings-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.booking-card {
  background-color: #fff;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 24px;
  transition: all 0.2s ease;
}

.booking-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.booking-card--past {
  opacity: 0.8;
}

.booking-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
}

.booking-property {
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
  font-size: 1.2rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #6c757d;
  font-size: 14px;
  margin-bottom: 8px;
}

.location-icon {
  width: 16px;
  height: 16px;
}

.booking-id {
  font-size: 12px;
  color: #6c757d;
  font-weight: 500;
}

.booking-status-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.booking-status {
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

.booking-status.pending {
  background-color: #fff3cd;
  color: #856404;
}

.booking-status.confirmed {
  background-color: #d1ecf1;
  color: #0c5460;
}

.booking-status.active {
  background-color: #d4edda;
  color: #155724;
}

.booking-status.completed {
  background-color: #e2e3e5;
  color: #383d41;
}

.booking-status.cancelled {
  background-color: #f8d7da;
  color: #721c24;
}

.status-icon {
  width: 14px;
  height: 14px;
}

.booking-actions-menu {
  position: relative;
}

.menu-button {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 6px;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.2s ease;
}

.menu-button:hover {
  background-color: #f8f9fa;
}

.menu-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: #fff;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 10;
  min-width: 180px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 12px 16px;
  background: none;
  border: none;
  text-align: left;
  color: #495057;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 14px;
}

.menu-item:hover {
  background-color: #f8f9fa;
}

.menu-item--danger {
  color: #dc3545;
}

.menu-item--danger:hover {
  background-color: #f8d7da;
}

.menu-icon {
  width: 16px;
  height: 16px;
}

.booking-details {
  margin-bottom: 20px;
}

.booking-dates {
  display: flex;
  align-items: center;
  gap: 24px;
  margin-bottom: 16px;
  padding: 16px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.date-section {
  flex: 1;
}

.date-label {
  font-size: 12px;
  color: #6c757d;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.date-value {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.date-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.date-time {
  font-size: 12px;
  color: #6c757d;
}

.date-separator {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 16px;
}

.arrow-icon {
  width: 20px;
  height: 20px;
  color: #6c757d;
}

.booking-info {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #495057;
  font-size: 14px;
}

.info-icon {
  width: 16px;
  height: 16px;
  color: #0071c2;
}

.booking-actions {
  display: flex;
  gap: 12px;
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

.action-button--primary {
  background-color: #0071c2;
  color: white;
}

.action-button--primary:hover {
  background-color: #005a9c;
}

.action-button--secondary {
  background-color: #f8f9fa;
  color: #495057;
  border-color: #e9ecef;
}

.action-button--secondary:hover {
  background-color: #e9ecef;
}

.action-button--danger {
  background-color: #dc3545;
  color: white;
}

.action-button--danger:hover {
  background-color: #c82333;
}

.action-icon {
  width: 16px;
  height: 16px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.empty-icon-container {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}

.empty-icon {
  width: 40px;
  height: 40px;
  color: #6c757d;
}

.empty-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 12px 0;
}

.empty-description {
  color: #6c757d;
  margin: 0 0 24px 0;
  max-width: 400px;
}

.empty-action-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.empty-action-button:hover {
  background-color: #005a9c;
}

.icon {
  width: 16px;
  height: 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .renters-bookings-section {
    padding: 16px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .bookings-filters {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-tabs {
    overflow-x: auto;
    padding: 4px;
  }

  .booking-header {
    flex-direction: column;
    gap: 16px;
  }

  .booking-dates {
    flex-direction: column;
    gap: 16px;
  }

  .date-separator {
    transform: rotate(90deg);
    padding: 8px 0;
  }

  .booking-info {
    flex-direction: column;
    gap: 12px;
  }

  .booking-actions {
    flex-direction: column;
  }

  .action-button {
    justify-content: center;
  }
}
</style>
