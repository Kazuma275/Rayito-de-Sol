<template>
  <div class="rental-management">
    <main class="main-content">
      <!-- Search and booking section -->
      <section v-if="activeTab === 'availability'" class="search-section">
        <div class="search-container">
          <h2 class="section-title">Reserva tu estancia en Rayito de Sol</h2>
          
          <div class="search-box">
            <div class="date-picker">
              <label>Fecha de llegada</label>
              <div class="input-with-icon">
                <CalendarIcon class="input-icon" />
                <input type="date" v-model="checkInDate" class="date-input" />
              </div>
            </div>
            
            <div class="date-picker">
              <label>Fecha de salida</label>
              <div class="input-with-icon">
                <CalendarIcon class="input-icon" />
                <input type="date" v-model="checkOutDate" class="date-input" />
              </div>
            </div>
            
            <div class="guests-picker">
              <label>Huéspedes</label>
              <div class="input-with-icon">
                <UsersIcon class="input-icon" />
                <select v-model="guests" class="guests-input">
                  <option v-for="n in 6" :key="n" :value="n">{{ n }} {{ n === 1 ? 'huésped' : 'huéspedes' }}</option>
                </select>
              </div>
            </div>
            
            <button class="search-button" @click="searchAvailability">
              Buscar Disponibilidad
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
          <button class="book-now-button" @click="activeTab = 'availability'">Hacer una reserva</button>
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
              <input type="text" v-model="settings.name" class="form-input" />
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" v-model="settings.email" class="form-input" />
            </div>
            <div class="form-group">
              <label>Teléfono</label>
              <input type="tel" v-model="settings.phone" class="form-input" />
            </div>
            <button class="save-button">Guardar cambios</button>
          </div>
          
          <div class="settings-card">
            <h3>Preferencias de notificación</h3>
            <div class="form-group checkbox">
              <input type="checkbox" id="email-notifications" v-model="settings.emailNotifications" />
              <label for="email-notifications">Recibir notificaciones por email</label>
            </div>
            <div class="form-group checkbox">
              <input type="checkbox" id="sms-notifications" v-model="settings.smsNotifications" />
              <label for="sms-notifications">Recibir notificaciones por SMS</label>
            </div>
            <button class="save-button">Guardar preferencias</button>
          </div>
        </div>
      </section>
    </main>
    
    
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
  PhoneIcon
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
  
  // Get the day of the week for the first day (0 = Sunday, 1 = Monday, etc.)
  let firstDayOfWeek = firstDay.getDay() - 1;
  if (firstDayOfWeek < 0) firstDayOfWeek = 6; // Adjust for Monday as first day
  
  // Add empty days for the beginning of the month
  for (let i = 0; i < firstDayOfWeek; i++) {
    days.push({ date: null });
  }
  
  // Add days of the month
  const today = new Date();
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const isToday = 
      today.getDate() === i && 
      today.getMonth() === currentMonth.value && 
      today.getFullYear() === currentYear.value;
    
    // Randomly determine availability for demo purposes
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
/* Global styles */
.rental-management {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  color: #333;
  background-color: #f5f5f5;
  min-height: 100vh;
}

/* Header styles */
.header {
  background-color: #003580;
  color: white;
  padding: 1rem 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo h1 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
}

.nav-menu {
  display: flex;
}

.nav-button {
  background: none;
  border: none;
  color: white;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background-color 0.3s;
  border-radius: 4px;
}

.nav-button:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.user-button {
  display: flex;
  align-items: center;
  background-color: #0071c2;
  border: none;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.user-button:hover {
  background-color: #005999;
}

.user-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

/* Main content styles */
.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.section-title {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #003580;
}

/* Search section styles */
.search-container {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.search-box {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: flex-end;
}

.date-picker, .guests-picker {
  flex: 1;
  min-width: 200px;
}

.date-picker label, .guests-picker label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.input-with-icon {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #0071c2;
  width: 18px;
  height: 18px;
}

.date-input, .guests-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.search-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  min-width: 200px;
}

.search-button:hover {
  background-color: #005999;
}

/* Property details styles */
.property-details {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-top: 2rem;
}

.property-images {
  flex: 1;
  min-width: 300px;
}

.property-image {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-radius: 8px;
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
  border-radius: 4px;
  cursor: pointer;
  transition: opacity 0.3s;
}

.gallery-image:hover {
  opacity: 0.8;
}

.property-info {
  flex: 1;
  min-width: 300px;
}

.property-title {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.property-location {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
  color: #666;
}

.location-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
  color: #0071c2;
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
  background-color: #f5f5f5;
  padding: 0.5rem 1rem;
  border-radius: 4px;
}

.feature-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
  color: #0071c2;
}

.property-description {
  margin-bottom: 1.5rem;
}

.property-description h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.property-description p {
  line-height: 1.6;
  color: #666;
}

.booking-summary {
  background-color: #f5f5f5;
  padding: 1.5rem;
  border-radius: 8px;
}

.booking-summary h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.price-details {
  margin-top: 1rem;
}

.price-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.price-row.total {
  margin-top: 1rem;
  padding-top: 0.5rem;
  border-top: 1px solid #ddd;
  font-weight: 600;
}

.price {
  font-weight: 500;
}

.book-now-button {
  width: 100%;
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
}

.book-now-button:hover {
  background-color: #005999;
}

/* Bookings section styles */
.bookings-section {
  padding: 1rem 0;
}

.bookings-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

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
  margin-bottom: 1rem;
}

.booking-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.booking-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-completed {
  background-color: #e6f7ee;
  color: #00703c;
}

.status-upcoming {
  background-color: #e6f0ff;
  color: #0071c2;
}

.booking-details {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 1rem;
}

.booking-dates {
  display: flex;
  gap: 1.5rem;
}

.date-item {
  display: flex;
  align-items: center;
}

.date-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
  color: #0071c2;
}

.date-label {
  display: block;
  font-size: 0.8rem;
  color: #666;
}

.date-value {
  font-weight: 500;
}

.booking-guests, .booking-price {
  display: flex;
  align-items: center;
}

.guests-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
  color: #0071c2;
}

.price-label {
  margin-right: 0.5rem;
  color: #666;
}

.price-value {
  font-weight: 600;
  color: #003580;
}

.booking-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.action-button {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
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
  background-color: #fff;
  color: #e41c00;
  border: 1px solid #e41c00;
}

.action-button.cancel:hover {
  background-color: #fff2f0;
}

.no-bookings {
  text-align: center;
  padding: 3rem 0;
}

.no-bookings-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1rem;
}

.no-bookings h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.no-bookings p {
  color: #666;
  margin-bottom: 1.5rem;
}

/* Calendar section styles */
.calendar-section {
  padding: 1rem 0;
}

.calendar-container {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.calendar-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.calendar-nav {
  background: none;
  border: none;
  color: #0071c2;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s;
}

.calendar-nav:hover {
  background-color: #f5f5f5;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.calendar-day {
  text-align: center;
  padding: 0.75rem;
  border-radius: 4px;
}

.calendar-day.header {
  font-weight: 600;
  color: #003580;
}

.calendar-day.empty {
  background: none;
}

.calendar-day.available {
  background-color: #e6f7ee;
  color: #00703c;
  cursor: pointer;
}

.calendar-day.unavailable {
  background-color: #fff2f0;
  color: #e41c00;
  text-decoration: line-through;
}

.calendar-day.today {
  border: 2px solid #0071c2;
  font-weight: 600;
}

.calendar-legend {
  display: flex;
  gap: 1.5rem;
  margin-top: 1.5rem;
  justify-content: center;
}

.legend-item {
  display: flex;
  align-items: center;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
  margin-right: 0.5rem;
}

.legend-color.available {
  background-color: #e6f7ee;
}

.legend-color.unavailable {
  background-color: #fff2f0;
}

/* Settings section styles */
.settings-section {
  padding: 1rem 0;
}

.settings-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.settings-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.settings-card h3 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
  color: #003580;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group.checkbox {
  display: flex;
  align-items: center;
}

.form-group.checkbox label {
  margin-bottom: 0;
  margin-left: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.save-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
}

.save-button:hover {
  background-color: #005999;
}

/* Footer styles */
.footer {
  background-color: #003580;
  color: white;
  padding: 3rem 0 1rem;
  margin-top: 3rem;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.footer-section h3 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.footer-section p {
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.footer-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section ul li {
  margin-bottom: 0.5rem;
}

.footer-section ul li a {
  color: white;
  text-decoration: none;
  transition: opacity 0.3s;
}

.footer-section ul li a:hover {
  opacity: 0.8;
}

.copyright {
  max-width: 1200px;
  margin: 2rem auto 0;
  padding: 1rem;
  text-align: center;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  font-size: 0.9rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    gap: 1rem;
  }
  
  .nav-menu {
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .search-box {
    flex-direction: column;
  }
  
  .property-details {
    flex-direction: column;
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

