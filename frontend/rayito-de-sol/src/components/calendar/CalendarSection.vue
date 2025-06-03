<template>
  <div class="calendar-section">
    <div class="section-header">
      <h2 class="section-title">Calendario de Disponibilidad</h2>
      <div class="header-actions">
        <button class="sync-button">
          <RefreshCwIcon class="button-icon" />
          Sincronizar Calendario
        </button>
      </div>
    </div>
    
    <div class="calendar-controls">
      <div class="property-selector">
        <label>Seleccionar Propiedad:</label>
        <select v-model="selectedPropertyId" class="property-select">
          <option v-for="property in properties" :key="property.id" :value="property.id">
            {{ property.name }}
          </option>
        </select>
      </div>
      
      <div class="view-selector">
        <button 
          v-for="view in calendarViews" 
          :key="view.id" 
          class="view-button" 
          :class="{ active: activeView === view.id }"
          @click="activeView = view.id"
        >
          {{ view.name }}
        </button>
      </div>
      
      <div class="month-navigator">
        <button class="nav-button" @click="previousPeriod">
          <ChevronLeftIcon class="nav-icon" />
        </button>
        <h3 class="current-period">{{ currentPeriodLabel }}</h3>
        <button class="nav-button" @click="nextPeriod">
          <ChevronRightIcon class="nav-icon" />
        </button>
      </div>
    </div>
    
    <!-- Vista de Mes -->
    <div v-if="activeView === 'month'" class="calendar-month-view">
      <div class="calendar-header">
        <div v-for="day in weekDays" :key="day" class="calendar-day-header">{{ day }}</div>
      </div>
      
      <div class="calendar-grid">
        <div 
          v-for="(day, index) in calendarDays" 
          :key="index" 
          class="calendar-day" 
          :class="{ 
            'empty': !day.date, 
            'today': day.isToday,
            'available': day.available && !day.booked,
            'unavailable': day.unavailable && !day.booked,
            'booked': day.booked,
            'partially-booked': day.partiallyBooked,
            'check-in': day.checkIn,
            'check-out': day.checkOut
          }"
          @click="day.date && handleDayClick(day)"
        >
          <div class="day-content">
            <span v-if="day.date" class="day-number">{{ day.date }}</span>
            <div v-if="day.date" class="day-status">
              <span v-if="day.booked" class="status-indicator booked">Reservado</span>
              <span v-else-if="day.partiallyBooked" class="status-indicator partial">Parcial</span>
              <span v-else-if="day.available" class="status-indicator available">Disponible</span>
              <span v-else class="status-indicator unavailable">No disponible</span>
            </div>
            <div v-if="day.bookingInfo" class="booking-info">
              <span class="guest-name">{{ day.bookingInfo.guestName }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Vista de Semana -->
    <div v-if="activeView === 'week'" class="calendar-week-view">
      <div class="week-header">
        <div class="time-column"></div>
        <div v-for="day in weekViewDays" :key="day.date" class="day-column-header">
          <div class="day-name">{{ getDayName(day.date) }}</div>
          <div class="day-number" :class="{ 'today': day.isToday }">{{ getDayNumber(day.date) }}</div>
        </div>
      </div>
      
      <div class="week-grid">
        <div v-for="hour in hours" :key="hour" class="hour-row">
          <div class="time-cell">{{ formatHour(hour) }}</div>
          <div 
            v-for="day in weekViewDays" 
            :key="`${day.date}-${hour}`" 
            class="day-cell"
            :class="{ 'booked': isHourBooked(day.date, hour) }"
            @click="handleHourClick(day.date, hour)"
          >
            <div v-if="getBookingAtHour(day.date, hour)" class="hour-booking-info">
              {{ getBookingAtHour(day.date, hour).guestName }}
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Leyenda del calendario -->
    <div class="calendar-legend">
      <div class="legend-item">
        <div class="legend-color available"></div>
        <span>Disponible</span>
      </div>
      <div class="legend-item">
        <div class="legend-color unavailable"></div>
        <span>No disponible</span>
      </div>
      <div class="legend-item">
        <div class="legend-color booked"></div>
        <span>Reservado</span>
      </div>
      <div class="legend-item">
        <div class="legend-color partially-booked"></div>
        <span>Parcialmente reservado</span>
      </div>
    </div>
    
    <!-- Acciones en bloque -->
    <div class="bulk-actions">
      <h3 class="bulk-title">Acciones en bloque</h3>
      
      <div class="bulk-form">
        <div class="date-range">
          <div class="date-input">
            <label>Desde</label>
            <div class="input-wrapper">
              <CalendarIcon class="input-icon" />
              <input type="date" v-model="bulkStartDate" class="form-input" />
            </div>
          </div>
          <div class="date-input">
            <label>Hasta</label>
            <div class="input-wrapper">
              <CalendarIcon class="input-icon" />
              <input type="date" v-model="bulkEndDate" class="form-input" />
            </div>
          </div>
        </div>
        
        <div class="action-buttons">
          <button class="bulk-button available" @click="setBulkAvailability(true)">
            <CheckIcon class="button-icon" />
            Marcar como disponible
          </button>
          <button class="bulk-button unavailable" @click="setBulkAvailability(false)">
            <XIcon class="button-icon" />
            Marcar como no disponible
          </button>
        </div>
      </div>
    </div>
    
    <!-- Modal para editar día -->
    <div v-if="showDayModal" class="day-edit-modal" @click="closeDayModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ formatSelectedDate(selectedDate) }}</h3>
          <button class="close-button" @click="closeDayModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        
        <div class="modal-body">
          <div class="day-status-selector">
            <h4>Estado del día</h4>
            <div class="status-options">
              <label class="status-option">
                <input 
                  type="radio" 
                  v-model="selectedDayStatus" 
                  :value="'available'" 
                  :disabled="selectedDayBooking"
                />
                <span class="status-label available">Disponible</span>
              </label>
              <label class="status-option">
                <input 
                  type="radio" 
                  v-model="selectedDayStatus" 
                  :value="'unavailable'" 
                  :disabled="selectedDayBooking"
                />
                <span class="status-label unavailable">No disponible</span>
              </label>
            </div>
          </div>
          
          <div v-if="selectedDayBooking" class="day-booking-info">
            <h4>Información de la reserva</h4>
            <div class="booking-details">
              <div class="booking-detail">
                <UserIcon class="detail-icon" />
                <div>
                  <span class="detail-label">Huésped</span>
                  <span class="detail-value">{{ selectedDayBooking.guestName }}</span>
                </div>
              </div>
              <div class="booking-detail">
                <CalendarIcon class="detail-icon" />
                <div>
                  <span class="detail-label">Fechas</span>
                  <span class="detail-value">{{ formatDate(selectedDayBooking.checkIn) }} - {{ formatDate(selectedDayBooking.checkOut) }}</span>
                </div>
              </div>
              <div class="booking-detail">
                <UsersIcon class="detail-icon" />
                <div>
                  <span class="detail-label">Huéspedes</span>
                  <span class="detail-value">{{ selectedDayBooking.guests }}</span>
                </div>
              </div>
              <div class="booking-detail">
                <CreditCardIcon class="detail-icon" />
                <div>
                  <span class="detail-label">Total</span>
                  <span class="detail-value">€{{ selectedDayBooking.total }}</span>
                </div>
              </div>
            </div>
            
            <div class="booking-actions">
              <button class="action-button view" @click="viewBookingDetails(selectedDayBooking.id)">
                <EyeIcon class="action-icon" />
                Ver detalles
              </button>
              <button class="action-button message" @click="sendMessage(selectedDayBooking.id)">
                <MessageSquareIcon class="action-icon" />
                Enviar mensaje
              </button>
            </div>
          </div>
          
          <div v-else class="day-price-editor">
            <h4>Precio para este día</h4>
            <div class="price-input">
              <EuroIcon class="input-icon" />
              <input 
                type="number" 
                v-model="selectedDayPrice" 
                class="form-input" 
                min="0" 
                step="1"
                :disabled="selectedDayStatus === 'unavailable'"
              />
            </div>
            <div class="price-options">
              <button 
                v-for="price in quickPrices" 
                :key="price" 
                class="quick-price-button"
                :disabled="selectedDayStatus === 'unavailable'"
                @click="selectedDayPrice = price"
              >
                €{{ price }}
              </button>
            </div>
          </div>
          
          <div class="day-notes">
            <h4>Notas</h4>
            <textarea 
              v-model="selectedDayNotes" 
              class="notes-input" 
              placeholder="Añade notas o recordatorios para este día..."
              rows="3"
            ></textarea>
          </div>
          
          <div class="modal-actions">
            <button class="cancel-button" @click="closeDayModal">Cancelar</button>
            <button class="save-button" @click="saveDayChanges">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import {
  RefreshCwIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  CalendarIcon,
  CheckIcon,
  XIcon,
  UserIcon,
  UsersIcon,
  CreditCardIcon,
  EyeIcon,
  MessageSquareIcon,
  EuroIcon,
} from 'lucide-vue-next'

// Estado dinámico para propiedades y reservas
const properties = ref([])
const bookings = ref([])
const unavailableDates = ref([])

// Estado del calendario
const selectedPropertyId = ref(null)
const activeView = ref('month')
const currentMonth = ref(new Date().getMonth())
const currentYear = ref(new Date().getFullYear())
const currentWeekStart = ref(getStartOfWeek(new Date()))

// Estado para acciones en bloque
const bulkStartDate = ref('')
const bulkEndDate = ref('')

// Estado para el modal de edición de día
const showDayModal = ref(false)
const selectedDate = ref(null)
const selectedDayStatus = ref('available')
const selectedDayPrice = ref(100)
const selectedDayNotes = ref('')
const selectedDayBooking = ref(null)
const selectedDayTime = ref('12:00:00')

// Opciones de precios rápidos
const quickPrices = [80, 100, 120, 150, 180, 200]

// Vistas disponibles del calendario
const calendarViews = [
  { id: 'month', name: 'Mes' },
  { id: 'week', name: 'Semana' }
]
const weekDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']
const hours = Array.from({ length: 15 }, (_, i) => i + 8)

// Etiqueta del período actual
const currentPeriodLabel = computed(() => {
  if (activeView.value === 'month') {
    const months = [
      'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ]
    return `${months[currentMonth.value]} ${currentYear.value}`
  } else {
    const startDate = new Date(currentWeekStart.value)
    const endDate = new Date(startDate)
    endDate.setDate(endDate.getDate() + 6)
    const formatOptions = { day: 'numeric', month: 'short' }
    return `${startDate.toLocaleDateString('es-ES', formatOptions)} - ${endDate.toLocaleDateString('es-ES', formatOptions)}`
  }
})

// Días del calendario para la vista mensual (INCLUYE unavailableDates)
const calendarDays = computed(() => {
  const days = []
  const firstDay = new Date(currentYear.value, currentMonth.value, 1)
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0)
  let firstDayOfWeek = firstDay.getDay() - 1
  if (firstDayOfWeek < 0) firstDayOfWeek = 6

  for (let i = 0; i < firstDayOfWeek; i++) {
    days.push({ date: null })
  }

  const today = new Date()
  today.setHours(0, 0, 0, 0)
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(currentYear.value, currentMonth.value, i)
    const dateString = date.toISOString().split('T')[0]
    // Solo mostrar reservas de la propiedad seleccionada
    const relevantBookings = bookings.value.filter(
      b => (b.property_id || b.propertyId) == selectedPropertyId.value
    )

    // Busca si hay reserva para el día exacto
    const booking = relevantBookings.find(
      b => (b.reservation_date || b.reservationDate) === dateString
    )
    const isUnavailable = unavailableDates.value.includes(dateString)

    days.push({
      date: i,
      dateObj: date,
      dateString,
      isToday: date.toDateString() === today.toDateString(),
      available: !booking && !isUnavailable,
      booked: !!booking,
      unavailable: isUnavailable,
      partiallyBooked: false,
      checkIn: false,
      checkOut: false,
      bookingInfo: booking
    })
  }
  return days
})

// Días para la vista semanal
const weekViewDays = computed(() => {
  const days = []
  const startDate = new Date(currentWeekStart.value)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  for (let i = 0; i < 7; i++) {
    const date = new Date(startDate)
    date.setDate(date.getDate() + i)
    const dateString = date.toISOString().split('T')[0]
    days.push({
      date: dateString,
      isToday: date.toDateString() === today.toDateString()
    })
  }
  return days
})

// Funciones auxiliares
function getStartOfWeek(date) {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1)
  d.setDate(diff)
  return d.toISOString().split('T')[0]
}
function isHourBooked(dateString, hour) {
  return false
}
function getBookingAtHour(dateString, hour) {
  return null
}
function formatDate(dateString) {
  if (!dateString) return ''
  const options = { day: 'numeric', month: 'short', year: 'numeric' }
  return new Date(dateString).toLocaleDateString('es-ES', options)
}
function formatSelectedDate(date) {
  if (!date) return ''
  const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }
  return new Date(date).toLocaleDateString('es-ES', options).replace(/^\w/, c => c.toUpperCase())
}
function getDayName(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', { weekday: 'short' })
}
function getDayNumber(dateString) {
  const date = new Date(dateString)
  return date.getDate()
}
function formatHour(hour) {
  return `${hour}:00`
}

// Navegación del calendario
function previousPeriod() {
  if (activeView.value === 'month') {
    if (currentMonth.value === 0) {
      currentMonth.value = 11
      currentYear.value--
    } else {
      currentMonth.value--
    }
  } else {
    const date = new Date(currentWeekStart.value)
    date.setDate(date.getDate() - 7)
    currentWeekStart.value = date.toISOString().split('T')[0]
  }
  // Recarga datos relevantes si hace falta (opcional)
}
function nextPeriod() {
  if (activeView.value === 'month') {
    if (currentMonth.value === 11) {
      currentMonth.value = 0
      currentYear.value++
    } else {
      currentMonth.value++
    }
  } else {
    const date = new Date(currentWeekStart.value)
    date.setDate(date.getDate() + 7)
    currentWeekStart.value = date.toISOString().split('T')[0]
  }
  // Recarga datos relevantes si hace falta (opcional)
}

// Carga de datos centralizada
async function fetchProperties() {
  const res = await axios.get('/api/properties')
  properties.value = res.data
  // Selecciona la primera propiedad por defecto si no hay una seleccionada
  if (properties.value.length > 0 && !selectedPropertyId.value) {
    selectedPropertyId.value = properties.value[0].id
  }
}

// Cargar reservas y unavailable dates para la propiedad seleccionada
async function fetchCalendarDataForProperty(propertyId) {
  if (!propertyId) return
  const [bookingsRes, unavailableRes] = await Promise.all([
    axios.get(`/api/properties/${propertyId}/bookings`),
    axios.get(`/api/properties/${propertyId}/unavailable-dates`)
  ])
  bookings.value = bookingsRes.data
  unavailableDates.value = unavailableRes.data
}

// Primera carga
onMounted(async () => {
  await fetchProperties()
  // Si se selecciona una propiedad, se dispara el watch de selectedPropertyId
})

// Cargar datos cada vez que cambia la propiedad seleccionada
watch(selectedPropertyId, async (newId) => {
  if (newId) {
    await fetchCalendarDataForProperty(newId)
  }
}, { immediate: true })

// Acciones en bloque (marcar como disponible/no disponible)
async function setBulkAvailability(available) {
  if (!bulkStartDate.value || !bulkEndDate.value || !selectedPropertyId.value) {
    alert('Por favor, selecciona una propiedad y un rango de fechas');
    return;
  }
  const start = new Date(bulkStartDate.value);
  const end = new Date(bulkEndDate.value);

  for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
    const dateString = d.toISOString().split('T')[0];
    if (available) {
      await axios.delete(`/api/properties/${selectedPropertyId.value}/unavailable-dates`, {
        data: { date: dateString }
      });
    } else {
      await axios.post(`/api/properties/${selectedPropertyId.value}/unavailable-dates`, { date: dateString });
    }
  }
  // Refresca todo el calendario
  await fetchCalendarDataForProperty(selectedPropertyId.value)
}

// Manejo de clics en el calendario
function handleDayClick(day) {
  if (!day.date) return
  selectedDate.value = day.dateString
  selectedDayStatus.value = day.unavailable ? 'unavailable' : 'available'
  selectedDayPrice.value = 100
  selectedDayNotes.value = ''
  selectedDayBooking.value = day.bookingInfo
  showDayModal.value = true
}
function handleHourClick(dateString, hour) {
  // Aquí iría la lógica para manejar el clic en una hora específica
}
function closeDayModal() {
  showDayModal.value = false
}
async function saveDayChanges() {
  if (!selectedPropertyId.value || !selectedDate.value) return;

  // Si hay reserva, no permitas modificar disponibilidad ni precio (opcional)
  if (selectedDayBooking.value) {
    closeDayModal();
    return;
  }

  // Cambia estado de disponibilidad
  if (selectedDayStatus.value === 'unavailable') {
    await axios.post(`/api/properties/${selectedPropertyId.value}/unavailable-dates`, {
      date: selectedDate.value
    });
  } else if (selectedDayStatus.value === 'available') {
    await axios.delete(`/api/properties/${selectedPropertyId.value}/unavailable-dates`, {
      data: { date: selectedDate.value }
    });
  }

  // Refresca todo el calendario
  await fetchCalendarDataForProperty(selectedPropertyId.value);

  closeDayModal();
}
function viewBookingDetails(bookingId) {
  // Redirige o abre modal de detalles para la reserva
}
function sendMessage(bookingId) {
  // Abre modal para enviar mensaje
}
</script>

<style scoped>
.calendar-section {
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

.calendar-section::before {
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

.sync-button {
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

.sync-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Controles del calendario */
.calendar-controls {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  position: relative;
  z-index: 1;
}

.property-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.property-selector label {
  font-weight: 600;
  color: #003580;
}

.property-select {
  padding: 0.75rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  min-width: 200px;
}

.property-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.view-selector {
  display: flex;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  overflow: hidden;
}

.view-button {
  background: none;
  border: none;
  padding: 0.75rem 1.25rem;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 0.95rem;
  color: #64748b;
}

.view-button:not(:last-child) {
  border-right: 1px solid #e6f0ff;
}

.view-button.active {
  background-color: #0071c2;
  color: white;
}

.month-navigator {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-button {
  background: none;
  border: 1px solid #e6f0ff;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
}

.nav-button:hover {
  background-color: #f0f7ff;
}

.nav-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.current-period {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
  min-width: 180px;
  text-align: center;
}

/* Vista de mes */
.calendar-month-view {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  position: relative;
  z-index: 1;
}

.calendar-header {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  margin-bottom: 0.5rem;
}

.calendar-day-header {
  text-align: center;
  padding: 0.75rem;
  font-weight: 600;
  color: #003580;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.calendar-day {
  aspect-ratio: 1 / 1;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
  position: relative;
}

.calendar-day:hover:not(.empty) {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 53, 128, 0.1);
}

.calendar-day.empty {
  background: none;
  cursor: default;
}

.calendar-day.today {
  border: 2px solid #0071c2;
}

.calendar-day.available {
  background-color: #e6f7ee;
}

.calendar-day.unavailable {
  background-color: #f1f5f9;
}

.calendar-day.booked {
  background-color: #e6f0ff;
}

.calendar-day.partially-booked {
  background: linear-gradient(135deg, #e6f0ff 50%, #e6f7ee 50%);
}

.calendar-day.check-in {
  background: linear-gradient(90deg, #e6f7ee 0%, #e6f7ee 20%, #e6f0ff 20%, #e6f0ff 100%);
}

.calendar-day.check-out {
  background: linear-gradient(90deg, #e6f0ff 0%, #e6f0ff 80%, #e6f7ee 80%, #e6f7ee 100%);
}

.day-content {
  height: 100%;
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
}

.day-number {
  font-weight: 600;
  font-size: 1.1rem;
  margin-bottom: 0.25rem;
  color: #1e293b;
}

.day-status {
  margin-top: auto;
}

.status-indicator {
  font-size: 0.7rem;
  padding: 0.1rem 0.3rem;
  border-radius: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: inline-block;
  max-width: 100%;
}

.status-indicator.available {
  background-color: rgba(0, 112, 60, 0.1);
  color: #00703c;
}

.status-indicator.unavailable {
  background-color: rgba(148, 163, 184, 0.1);
  color: #64748b;
}

.status-indicator.booked {
  background-color: rgba(0, 113, 194, 0.1);
  color: #0071c2;
}

.status-indicator.partial {
  background: linear-gradient(90deg, rgba(0, 113, 194, 0.1) 50%, rgba(0, 112, 60, 0.1) 50%);
  color: #003580;
}

.booking-info {
  font-size: 0.7rem;
  margin-top: 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.guest-name {
  color: #1e293b;
  font-weight: 500;
}

/* Vista de semana */
.calendar-week-view {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  position: relative;
  z-index: 1;
  overflow-x: auto;
}

.week-header {
  display: grid;
  grid-template-columns: 80px repeat(7, 1fr);
  margin-bottom: 0.5rem;
  position: sticky;
  top: 0;
  background-color: white;
  z-index: 2;
}

.day-column-header {
  text-align: center;
  padding: 0.75rem;
  border-bottom: 1px solid #e6f0ff;
}

.day-name {
  font-weight: 600;
  color: #003580;
  margin-bottom: 0.25rem;
}

.day-number {
  font-size: 1.2rem;
  color: #1e293b;
}

.day-number.today {
  color: #0071c2;
  font-weight: 700;
}

.week-grid {
  display: grid;
  grid-template-columns: 80px repeat(7, 1fr);
  grid-auto-rows: 60px;
}

.time-column {
  border-right: 1px solid #e6f0ff;
}

.time-cell {
  padding: 0.5rem;
  text-align: right;
  color: #64748b;
  font-size: 0.85rem;
  border-bottom: 1px solid #f1f5f9;
}

.day-cell {
  border-bottom: 1px solid #f1f5f9;
  border-right: 1px solid #f1f5f9;
  padding: 0.25rem;
  cursor: pointer;
  transition: background-color 0.3s;
  position: relative;
}

.day-cell:hover {
  background-color: #f8fafc;
}

.day-cell.booked {
  background-color: #e6f0ff;
}

.hour-booking-info {
  font-size: 0.8rem;
  color: #0071c2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Leyenda del calendario */
.calendar-legend {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-top: 1rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.legend-color.available {
  background-color: #e6f7ee;
}

.legend-color.unavailable {
  background-color: #f1f5f9;
}

.legend-color.booked {
  background-color: #e6f0ff;
}

.legend-color.partially-booked {
  background: linear-gradient(135deg, #e6f0ff 50%, #e6f7ee 50%);
}

/* Acciones en bloque */
.bulk-actions {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  position: relative;
  z-index: 1;
}

.bulk-title {
  font-size: 1.2rem;
  color: #003580;
  margin: 0 0 1.5rem;
}

.bulk-form {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: flex-end;
}

.date-range {
  display: flex;
  gap: 1rem;
  flex: 2;
}

.date-input {
  flex: 1;
}

.date-input label {
  display: block;
  font-weight: 500;
  color: #003580;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.input-wrapper {
  position: relative;
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

.form-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: white;
  transition: all 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.action-buttons {
  display: flex;
  gap: 1rem;
  flex: 1;
}

.bulk-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
}

.bulk-button.available {
  background-color: #e6f7ee;
  color: #00703c;
}

.bulk-button.available:hover {
  background-color: #d1f0e0;
}

.bulk-button.unavailable {
  background-color: #f1f5f9;
  color: #64748b;
}

.bulk-button.unavailable:hover {
  background-color: #e2e8f0;
}

/* Modal para editar día */
.day-edit-modal {
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
  max-width: 500px;
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
  text-transform: capitalize;
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

.day-status-selector {
  margin-bottom: 1.5rem;
}

.day-status-selector h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.status-options {
  display: flex;
  gap: 1rem;
}

.status-option {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.status-option input {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.status-option input:disabled {
  cursor: not-allowed;
}

.status-label {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.9rem;
  flex: 1;
  text-align: center;
}

.status-label.available {
  background-color: #e6f7ee;
  color: #00703c;
}

.status-label.unavailable {
  background-color: #f1f5f9;
  color: #64748b;
}

.day-booking-info {
  background-color: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #e6f0ff;
}

.day-booking-info h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.booking-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.booking-detail {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.detail-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-top: 0.25rem;
}

.detail-label {
  display: block;
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 0.25rem;
}

.detail-value {
  font-weight: 600;
  color: #1e293b;
}

.booking-actions {
  display: flex;
  gap: 1rem;
}

.action-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
}

.action-button.view {
  background-color: #f1f5f9;
  color: #1e293b;
}

.action-button.view:hover {
  background-color: #e2e8f0;
}

.action-button.message {
  background-color: #e6f0ff;
  color: #0071c2;
}

.action-button.message:hover {
  background-color: #d1e5ff;
}

.action-icon {
  width: 16px;
  height: 16px;
}

.day-price-editor {
  margin-bottom: 1.5rem;
}

.day-price-editor h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.price-input {
  position: relative;
  margin-bottom: 1rem;
}

.price-options {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.quick-price-button {
  background-color: #f8fafc;
  border: 1px solid #e6f0ff;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.9rem;
  color: #1e293b;
  cursor: pointer;
  transition: all 0.3s;
}

.quick-price-button:hover:not(:disabled) {
  background-color: #e6f0ff;
  color: #0071c2;
}

.quick-price-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.day-notes {
  margin-bottom: 1.5rem;
}

.day-notes h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.notes-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #1e293b;
  resize: vertical;
  transition: all 0.3s;
}

.notes-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.modal-actions {
  display: flex;
  gap: 1rem;
}

.cancel-button {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  background-color: white;
  color: #64748b;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.cancel-button:hover {
  background-color: #f8fafc;
}

.save-button {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.save-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

/* Responsive */
@media (max-width: 992px) {
  .calendar-section {
    padding: 1.5rem;
  }
  
  .calendar-controls {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .property-selector, .view-selector, .month-navigator {
    width: 100%;
  }
  
  .property-selector {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .property-select {
    width: 100%;
  }
  
  .view-selector {
    justify-content: center;
  }
  
  .month-navigator {
    justify-content: space-between;
  }
  
  .bulk-form {
    flex-direction: column;
  }
  
  .date-range {
    width: 100%;
  }
  
  .action-buttons {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .calendar-section {
    padding: 1rem;
  }
  
  .calendar-day-header, .calendar-day {
    padding: 0.5rem;
  }
  
  .day-number {
    font-size: 0.9rem;
  }
  
  .status-indicator {
    font-size: 0.6rem;
  }
  
  .date-range {
    flex-direction: column;
  }
  
  .booking-details {
    grid-template-columns: 1fr;
  }
  
  .booking-actions, .modal-actions {
    flex-direction: column;
  }
}

@media (max-width: 576px) {
  .calendar-grid {
    gap: 0.25rem;
  }
  
  .calendar-day {
    aspect-ratio: auto;
    height: 60px;
  }
  
  .status-options {
    flex-direction: column;
  }
}
</style>