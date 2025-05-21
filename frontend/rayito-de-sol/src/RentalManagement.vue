<template>
  <div class="rental-management">
    <div class="portal-container">
      <div class="portal-inner">
        <!-- Logo -->
        <div class="logo">
          <div class="logo-icon-wrapper">
            <SunIcon class="logo-icon" />
            <div class="logo-icon-glow"></div>
          </div>
          <h1 class="logo-text">Rayito de Sol</h1>
        </div>

        <main class="main-content">
          <!-- Search and booking section -->
          <section v-if="activeTab === 'availability'" class="search-section">
            <div class="search-container">
              <h2 class="section-title">Reserva tu estancia en Rayito de Sol</h2>
              
              <div class="search-box">
                <div class="date-picker">
                  <label>Fecha de llegada</label>
                  <div class="input-wrapper">
                    <CalendarIcon class="input-icon" />
                    <input type="date" v-model="checkInDate" class="form-input" />
                    <div class="input-glow"></div>
                  </div>
                </div>
                
                <div class="date-picker">
                  <label>Fecha de salida</label>
                  <div class="input-wrapper">
                    <CalendarIcon class="input-icon" />
                    <input type="date" v-model="checkOutDate" class="form-input" />
                    <div class="input-glow"></div>
                  </div>
                </div>
                
                <div class="guests-picker">
                  <label>Huéspedes</label>
                  <div class="input-wrapper">
                    <UsersIcon class="input-icon" />
                    <select v-model="guests" class="form-input">
                      <option v-for="n in 6" :key="n" :value="n">{{ n }} {{ n === 1 ? 'huésped' : 'huéspedes' }}</option>
                    </select>
                    <div class="input-glow"></div>
                  </div>
                </div>
                
                <button class="search-button" @click="searchAvailability">
                  Buscar Disponibilidad
                  <div class="button-glow"></div>
                </button>
              </div>
            </div>
            
            <!-- Property details -->
            <div class="property-details">
              <div class="property-images">
                <img :src="backgroundImage" alt="Rayito de Sol Apartment" class="property-image" />
                <div class="image-gallery">
                  <img v-for="(img, index) in additionalImages" :key="index" :src="img" alt="Apartment view" class="gallery-image" />
                </div>
              </div>
              
              <div class="property-info">
                <h2 class="property-title">Apartamento Rayito de Sol</h2>
                <div class="property-location">
                  <MapPinIcon class="location-icon" />
                  <span>Playa de Calahonda, Mijas Costa, Málaga</span>
                </div>
                
                <div class="property-features">
                  <div class="feature">
                    <HomeIcon class="feature-icon" />
                    <span>Apartamento completo</span>
                  </div>
                  <div class="feature">
                    <BedIcon class="feature-icon" />
                    <span>2 dormitorios</span>
                  </div>
                  <div class="feature">
                    <UsersIcon class="feature-icon" />
                    <span>Hasta 6 huéspedes</span>
                  </div>
                  <div class="feature">
                    <WifiIcon class="feature-icon" />
                    <span>WiFi gratis</span>
                  </div>
                </div>
                
                <div class="property-description">
                  <h3>Descripción</h3>
                  <p>Disfrute de unas vacaciones inolvidables en este maravilloso apartamento con vistas al mar. Ubicado en primera línea de playa, Rayito de Sol ofrece todo lo que necesita para una estancia perfecta: cómodos dormitorios, cocina completamente equipada, y una terraza con vistas espectaculares al Mediterráneo.</p>
                </div>
                
                <div class="booking-summary">
                  <h3>Resumen de precios</h3>
                  <div class="price-details">
                    <div class="price-row">
                      <span>Precio por noche</span>
                      <span class="price">€120</span>
                    </div>
                    <div class="price-row">
                      <span>Limpieza</span>
                      <span class="price">€50</span>
                    </div>
                    <div class="price-row total">
                      <span>Total</span>
                      <span class="price">€{{ calculateTotal() }}</span>
                    </div>
                  </div>
                  
                  <button class="book-now-button" @click="bookNow">
                    Reservar ahora
                    <div class="button-glow"></div>
                  </button>
                </div>
              </div>
            </div>
          </section>
          
          <!-- My Bookings section -->
          <section v-if="activeTab === 'bookings'" class="bookings-section">
            <h2 class="section-title">Mis Reservas</h2>
            <div v-if="myBookings.length > 0" class="bookings-list">
              <div v-for="(booking, index) in myBookings" :key="index" class="booking-card">
                <div class="booking-header">
                  <h3>Reserva #{{ booking.id }}</h3>
                  <span :class="['booking-status', `status-${booking.status}`]">{{ booking.statusText }}</span>
                </div>
                <div class="booking-details">
                  <div class="booking-dates">
                    <div class="date-item">
                      <CalendarIcon class="date-icon" />
                      <div>
                        <span class="date-label">Llegada</span>
                        <span class="date-value">{{ booking.checkIn }}</span>
                      </div>
                    </div>
                    <div class="date-item">
                      <CalendarIcon class="date-icon" />
                      <div>
                        <span class="date-label">Salida</span>
                        <span class="date-value">{{ booking.checkOut }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="booking-guests">
                    <UsersIcon class="guests-icon" />
                    <span>{{ booking.guests }} huéspedes</span>
                  </div>
                  <div class="booking-price">
                    <span class="price-label">Total pagado</span>
                    <span class="price-value">€{{ booking.total }}</span>
                  </div>
                </div>
                <div class="booking-actions">
                  <button class="action-button view">Ver detalles</button>
                  <button v-if="booking.status === 'upcoming'" class="action-button cancel">Cancelar</button>
                </div>
              </div>
            </div>
            <div v-else class="no-bookings">
              <CalendarOffIcon class="no-bookings-icon" />
              <h3>No tienes reservas activas</h3>
              <p>Cuando realices una reserva, aparecerá aquí</p>
              <button class="book-now-button" @click="activeTab = 'availability'">
                Hacer una reserva
                <div class="button-glow"></div>
              </button>
            </div>
          </section>
          
          <!-- Calendar section -->
          <section v-if="activeTab === 'calendar'" class="calendar-section">
            <h2 class="section-title">Calendario de Disponibilidad</h2>
            <div class="calendar-container">
              <div class="calendar-header">
                <button class="calendar-nav" @click="previousMonth">
                  <ChevronLeftIcon />
                </button>
                <h3>{{ currentMonthName }} {{ currentYear }}</h3>
                <button class="calendar-nav" @click="nextMonth">
                  <ChevronRightIcon />
                </button>
              </div>
              <div class="calendar-grid">
                <div v-for="day in weekDays" :key="day" class="calendar-day header">{{ day }}</div>
                <div v-for="(day, index) in calendarDays" :key="index" 
                     :class="['calendar-day', { 
                       'empty': !day.date, 
                       'available': day.available, 
                       'unavailable': !day.available && day.date,
                       'today': day.isToday
                     }]">
                  <span v-if="day.date">{{ day.date }}</span>
                </div>
              </div>
              <div class="calendar-legend">
                <div class="legend-item">
                  <div class="legend-color available"></div>
                  <span>Disponible</span>
                </div>
                <div class="legend-item">
                  <div class="legend-color unavailable"></div>
                  <span>No disponible</span>
                </div>
              </div>
            </div>
          </section>
          
          <!-- Settings section -->
          <section v-if="activeTab === 'settings'" class="settings-section">
            <h2 class="section-title">Configuración</h2>
            <div class="settings-container">
              <div class="settings-card">
                <h3>Información de contacto</h3>
                <div class="form-group">
                  <label>Nombre</label>
                  <div class="input-wrapper">
                    <input type="text" v-model="settings.name" class="form-input" />
                    <div class="input-glow"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <div class="input-wrapper">
                    <input type="email" v-model="settings.email" class="form-input" />
                    <div class="input-glow"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Teléfono</label>
                  <div class="input-wrapper">
                    <input type="tel" v-model="settings.phone" class="form-input" />
                    <div class="input-glow"></div>
                  </div>
                </div>
                <button class="save-button">
                  Guardar cambios
                  <div class="button-glow"></div>
                </button>
              </div>
              
              <div class="settings-card">
                <h3>Preferencias de notificación</h3>
                <div class="form-group checkbox">
                  <input type="checkbox" id="email-notifications" v-model="settings.emailNotifications" class="custom-checkbox" />
                  <label for="email-notifications">Recibir notificaciones por email</label>
                </div>
                <div class="form-group checkbox">
                  <input type="checkbox" id="sms-notifications" v-model="settings.smsNotifications" class="custom-checkbox" />
                  <label for="sms-notifications">Recibir notificaciones por SMS</label>
                </div>
                <button class="save-button">
                  Guardar preferencias
                  <div class="button-glow"></div>
                </button>
              </div>
            </div>
          </section>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { 
  UserIcon, 
  CalendarIcon, 
  UsersIcon, 
  MapPinIcon, 
  HomeIcon, 
  BedIcon, 
  WifiIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  CalendarOffIcon,
  MailIcon,
  PhoneIcon,
  SunIcon
} from 'lucide-vue-next';

// Background image from the provided URL
const backgroundImage = 'https://sjc.microlink.io/-p-fwojbjDvRwpePN2Z_l13ojz0jiDJ0JuUgMOruCd9nTT0ju4uWBG4DHcKkOsBswI98iuf-0pgLjtPXh49xnQ.jpeg';

// Additional placeholder images
const additionalImages = [
  '/placeholder.svg?height=150&width=200',
  '/placeholder.svg?height=150&width=200',
  '/placeholder.svg?height=150&width=200',
];

// Active tab state
const activeTab = ref('availability');

// Booking form data
const checkInDate = ref('');
const checkOutDate = ref('');
const guests = ref(2);

// Settings data
const settings = ref({
  name: 'Juan Pérez',
  email: 'juan@example.com',
  phone: '+34 123 456 789',
  emailNotifications: true,
  smsNotifications: false
});

// Sample bookings data
const myBookings = ref([
  {
    id: '1234',
    checkIn: '15 Jun 2023',
    checkOut: '22 Jun 2023',
    guests: 3,
    total: 890,
    status: 'completed',
    statusText: 'Completada'
  },
  {
    id: '5678',
    checkIn: '10 Aug 2023',
    checkOut: '17 Aug 2023',
    guests: 4,
    total: 980,
    status: 'upcoming',
    statusText: 'Próxima'
  }
]);

// Calendar data
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());

const weekDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

const currentMonthName = computed(() => {
  const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
  return months[currentMonth.value];
});

const calendarDays = computed(() => {
  const days = [];
  const firstDay = new Date(currentYear.value, currentMonth.value, 1);
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
  
  let firstDayOfWeek = firstDay.getDay() - 1;
  if (firstDayOfWeek < 0) firstDayOfWeek = 6;
  
  for (let i = 0; i < firstDayOfWeek; i++) {
    days.push({ date: null });
  }
  
  const today = new Date();
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const isToday = 
      today.getDate() === i && 
      today.getMonth() === currentMonth.value && 
      today.getFullYear() === currentYear.value;
    
    const available = Math.random() > 0.3;
    
    days.push({ 
      date: i, 
      available, 
      isToday 
    });
  }
  
  return days;
});

// Methods
const calculateTotal = () => {
  if (!checkInDate.value || !checkOutDate.value) return 170;
  
  const start = new Date(checkInDate.value);
  const end = new Date(checkOutDate.value);
  const nights = Math.round((end - start) / (1000 * 60 * 60 * 24));
  
  return (nights > 0 ? nights * 120 : 0) + 50;
};

const searchAvailability = () => {
  console.log('Searching availability for:', {
    checkIn: checkInDate.value,
    checkOut: checkOutDate.value,
    guests: guests.value
  });
};

const bookNow = () => {
  console.log('Booking now with details:', {
    checkIn: checkInDate.value,
    checkOut: checkOutDate.value,
    guests: guests.value,
    total: calculateTotal()
  });
};

const previousMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
};

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
};
</script>

<style scoped>
.rental-management {
  min-height: 100vh;
  background-color: #f5f5f5;
  color: #1e293b;
}

.portal-container {
  min-height: 100vh;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%);
  position: relative;
  overflow: hidden;
}

.portal-container::before {
  content: '';
  position: absolute;
  width: 200%;
  height: 200%;
  top: -50%;
  left: -50%;
  background: radial-gradient(circle at center, rgba(59, 130, 246, 0.1) 0%, transparent 70%),
              radial-gradient(circle at 30% 70%, rgba(251, 191, 36, 0.1) 0%, transparent 60%);
  animation: rotate 30s linear infinite;
  z-index: 0;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.portal-inner {
  position: relative;
  z-index: 1;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

/* Logo styles */
.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 2rem;
  position: relative;
}

.logo-icon-wrapper {
  position: relative;
  margin-right: 1rem;
}

.logo-icon {
  width: 48px;
  height: 48px;
  color: #facc15;
  filter: drop-shadow(0 0 8px rgba(250, 204, 21, 0.5));
  position: relative;
  z-index: 1;
  animation: pulse 3s infinite ease-in-out;
}

.logo-icon-glow {
  position: absolute;
  width: 48px;
  height: 48px;
  top: 0;
  left: 0;
  background: radial-gradient(circle, rgba(250, 204, 21, 0.3) 0%, rgba(250, 204, 21, 0) 70%);
  border-radius: 50%;
  animation: glow 3s infinite ease-in-out;
  z-index: 0;
}

.logo-text {
  font-size: 2rem;
  font-weight: 700;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Main content styles */
.main-content {
  margin-top: 2rem;
}

.section-title {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #1e3a8a;
  text-align: center;
}

/* Search section styles */
.search-container {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 24px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.03),
    0 1px 3px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.search-box {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: flex-end;
}

.date-picker,
.guests-picker {
  flex: 1;
  min-width: 200px;
}

.date-picker label,
.guests-picker label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #1e293b;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #f59e0b;
  width: 20px;
  height: 20px;
  z-index: 1;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 12px;
  font-size: 1rem;
  background: rgba(255, 255, 255, 0.8);
  color: #1e293b;
  transition: all 0.3s ease;
}

.input-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 12px;
  pointer-events: none;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #f59e0b;
}

.form-input:focus + .input-glow {
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
}

.search-button,
.book-now-button {
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
  border: none;
  padding: 0.875rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  min-width: 200px;
}

.search-button::before,
.book-now-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transform: translateX(-100%);
  transition: transform 0s;
}

.search-button:hover::before,
.book-now-button:hover::before {
  transform: translateX(100%);
  transition: transform 0.6s ease;
}

.search-button:hover,
.book-now-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

/* Property details styles */
.property-details {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 24px;
  padding: 2rem;
  margin-top: 2rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.03),
    0 1px 3px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.property-image {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-radius: 16px;
  margin-bottom: 1rem;
}

.image-gallery {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
}

.gallery-image {
  width: 100px;
  height: 75px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.gallery-image:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.property-title {
  font-size: 1.8rem;
  color: #1e3a8a;
  margin-bottom: 1rem;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  margin-bottom: 1.5rem;
}

.location-icon {
  color: #f59e0b;
  width: 20px;
  height: 20px;
}

.property-features {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: rgba(245, 158, 11, 0.1);
  border-radius: 12px;
  color: #1e293b;
}

.feature-icon {
  color: #f59e0b;
  width: 20px;
  height: 20px;
}

.property-description {
  margin-bottom: 2rem;
}

.property-description h3 {
  color: #1e3a8a;
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.property-description p {
  color: #64748b;
  line-height: 1.6;
}

.booking-summary {
  background: rgba(245, 158, 11, 0.1);
  border-radius: 16px;
  padding: 1.5rem;
}

.booking-summary h3 {
  color: #1e3a8a;
  margin-bottom: 1rem;
}

.price-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.price-row {
  display: flex;
  justify-content: space-between;
  color: #64748b;
}

.price-row.total {
  border-top: 1px solid rgba(148, 163, 184, 0.2);
  padding-top: 0.75rem;
  margin-top: 0.5rem;
  color: #1e293b;
  font-weight: 600;
}

/* Bookings section styles */
.bookings-section {
  margin-top: 2rem;
}

.bookings-list {
  display: grid;
  gap: 1.5rem;
}

.booking-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.03),
    0 1px 3px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.6);
  transition: all 0.3s ease;
}

.booking-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

.booking-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.booking-header h3 {
  color: #1e3a8a;
  font-size: 1.2rem;
}

.booking-status {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
}

.status-completed {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.status-upcoming {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.booking-details {
  display: grid;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.booking-dates {
  display: flex;
  gap: 2rem;
}

.date-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.date-icon {
  color: #f59e0b;
  width: 20px;
  height: 20px;
}

.date-label {
  color: #64748b;
  font-size: 0.875rem;
}

.date-value {
  color: #1e293b;
  font-weight: 500;
}

.booking-guests {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #1e293b;
}

.guests-icon {
  color: #f59e0b;
  width: 20px;
  height: 20px;
}

.booking-price {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.price-label {
  color: #64748b;
}

.price-value {
  color: #1e293b;
  font-weight: 600;
}

.booking-actions {
  display: flex;
  gap: 1rem;
}

.action-button {
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-button.view {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
  border: none;
}

.action-button.view:hover {
  background: rgba(245, 158, 11, 0.2);
}

.action-button.cancel {
  background: white;
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.action-button.cancel:hover {
  background: rgba(239, 68, 68, 0.1);
}

.no-bookings {
  text-align: center;
  padding: 4rem 2rem;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 24px;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.03),
    0 1px 3px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.no-bookings-icon {
  color: #94a3b8;
  width: 64px;
  height: 64px;
  margin-bottom: 1.5rem;
}

.no-bookings h3 {
  color: #1e3a8a;
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.no-bookings p {
  color: #64748b;
  margin-bottom: 2rem;
}

/* Calendar section styles */
.calendar-section {
  margin-top: 2rem;
}

.calendar-container {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.03),
    0 1px 3px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.calendar-header h3 {
  color: #1e3a8a;
  font-size: 1.2rem;
}

.calendar-nav {
  background: none;
  border: none;
  color: #f59e0b;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.calendar-nav:hover {
  background: rgba(245, 158, 11, 0.1);
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.calendar-day {
  text-align: center;
  padding: 0.75rem;
  border-radius: 12px;
  font-weight: 500;
}

.calendar-day.header {
  color: #1e3a8a;
  font-weight: 600;
}

.calendar-day.available {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
  cursor: pointer;
}

.calendar-day.available:hover {
  background: rgba(245, 158, 11, 0.2);
}

.calendar-day.unavailable {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  text-decoration: line-through;
}

.calendar-day.today {
  border: 2px solid #f59e0b;
  font-weight: 600;
}

.calendar-legend {
  display: flex;
  justify-content: center;
  gap: 2rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.legend-color.available {
  background: rgba(245, 158, 11, 0.1);
}

.legend-color.unavailable {
  background: rgba(239, 68, 68, 0.1);
}

/* Settings section styles */
.settings-section {
  margin-top: 2rem;
}

.settings-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.settings-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.03),
    0 1px 3px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.settings-card h3 {
  color: #1e3a8a;
  margin-bottom: 1.5rem;
  font-size: 1.2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #1e293b;
  font-weight: 500;
}

.form-group.checkbox {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.custom-checkbox {
  appearance: none;
  width: 18px;
  height: 18px;
  border: 2px solid #f59e0b;
  border-radius: 4px;
  position: relative;
  cursor: pointer;
  transition: all 0.2s ease;
}

.custom-checkbox:checked {
  background-color: #f59e0b;
}

.custom-checkbox:checked::after {
  content: '✓';
  position: absolute;
  color: white;
  font-size: 12px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.save-button {
  width: 100%;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
  border: none;
  padding: 0.875rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.save-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

@keyframes glow {
  0%, 100% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.5);
    opacity: 0.8;
  }
}

/* Responsive styles */
@media (max-width: 768px) {
  .portal-container {
    padding: 1rem;
  }

  .search-box {
    flex-direction: column;
  }

  .property-details {
    grid-template-columns: 1fr;
  }

  .booking-dates {
    flex-direction: column;
    gap: 1rem;
  }

  .calendar-grid {
    font-size: 0.875rem;
  }

  .calendar-day {
    padding: 0.5rem;
  }
}

@media (max-width: 500px) {
  .logo-icon {
    width: 40px;
    height: 40px;
  }

  .logo-text {
    font-size: 1.75rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .search-container,
  .property-details,
  .booking-card,
  .calendar-container,
  .settings-card {
    padding: 1.5rem;
  }
}
</style>