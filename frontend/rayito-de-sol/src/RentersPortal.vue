<template>
  <div class="renters-portal">
    <RentersHeader v-if="isAuthenticated && !isLoginPage" />
    
    <main class="main-content">
      <!-- Dashboard -->
      <section v-if="activeTab === 'dashboard'" class="dashboard-section">
        <div class="welcome-banner">
          <div class="welcome-content">
            <h1 class="welcome-title">Bienvenido, {{ userName }}</h1>
            <p class="welcome-subtitle">Encuentra tu próximo hogar vacacional perfecto</p>
          </div>
          <div class="quick-search">
            <div class="search-input">
              <MapPinIcon class="search-icon" />
              <input type="text" placeholder="¿A dónde quieres ir?" class="destination-input" />
            </div>
            <button class="search-button">
              <SearchIcon class="button-icon" />
              <span>Buscar</span>
            </button>
          </div>
        </div>
        
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <CalendarIcon class="stat-icon" />
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ activeBookings.length || 0 }}</p>
              <h3 class="stat-title">Reservas Activas</h3>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <ClockIcon class="stat-icon" />
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ nextBookingDate || 'No hay' }}</p>
              <h3 class="stat-title">Próxima Reserva</h3>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <MessageSquareIcon class="stat-icon" />
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ unreadMessages || 0 }}</p>
              <h3 class="stat-title">Mensajes Nuevos</h3>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <HeartIcon class="stat-icon" />
            </div>
            <div class="stat-content">
              <p class="stat-value">{{ favorites.length || 0 }}</p>
              <h3 class="stat-title">Favoritos</h3>
            </div>
          </div>
        </div>
        
        <div class="dashboard-grid">
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Mis Reservas</h3>
              <button class="view-all-button" @click="changeTab('bookings')">
                Ver todas
                <ChevronRightIcon class="button-icon" />
              </button>
            </div>
            <div v-if="activeBookings && activeBookings.length > 0" class="upcoming-bookings">
              <div v-for="booking in activeBookings.slice(0, 3)" :key="booking.id" class="booking-item">
                <div class="booking-property">
                  <img :src="booking.property.image" alt="Property" class="booking-image" />
                  <div>
                    <h4>{{ booking.property.name }}</h4>
                    <p class="booking-dates">
                      {{ formatDate(booking.checkIn) }} - {{ formatDate(booking.checkOut) }}
                    </p>
                  </div>
                </div>
                <div class="booking-status" :class="booking.status">
                  {{ getStatusText(booking.status) }}
                </div>
              </div>
            </div>
            <div v-else class="empty-state">
              <CalendarOffIcon class="empty-icon" />
              <p>No tienes reservas activas</p>
              <button class="action-button" @click="changeTab('search')">
                <SearchIcon class="action-icon" />
                Buscar Alojamiento
              </button>
            </div>
          </div>
          
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Mensajes Recientes</h3>
              <button class="view-all-button" @click="changeTab('messages')">
                Ver todos
                <ChevronRightIcon class="button-icon" />
              </button>
            </div>
            <div v-if="recentMessages && recentMessages.length > 0" class="messages-list">
              <div v-for="message in recentMessages" :key="message.id" class="message-item">
                <div class="message-sender">
                  <div class="sender-avatar">
                    <UserIcon class="avatar-icon" />
                  </div>
                  <div>
                    <h4>{{ message.sender }}</h4>
                    <p class="message-property">{{ message.property.name }}</p>
                  </div>
                </div>
                <p class="message-preview">{{ message.text.substring(0, 60) }}{{ message.text.length > 60 ? '...' : '' }}</p>
                <p class="message-time">{{ formatDateTime(message.createdAt) }}</p>
              </div>
            </div>
            <div v-else class="empty-state">
              <MailIcon class="empty-icon" />
              <p>No hay mensajes nuevos</p>
            </div>
          </div>
        </div>
        
        <div class="recommended-section">
          <div class="section-header">
            <h3 class="section-title">Recomendados para ti</h3>
            <button class="view-all-button" @click="changeTab('search')">
              Ver más
              <ChevronRightIcon class="button-icon" />
            </button>
          </div>
          
          <div class="properties-grid">
            <div v-for="property in recommendedProperties" :key="property.id" class="property-card">
              <div class="property-image-container">
                <img :src="property.image" alt="Property" class="property-image" />
                <div class="property-badge">Destacado</div>
                <button 
                  class="favorite-button" 
                  :class="{ active: isFavorite(property.id) }"
                  @click.stop="toggleFavorite(property.id)"
                >
                  <HeartIcon class="favorite-icon" />
                </button>
              </div>
              <div class="property-content">
                <h3 class="property-name">{{ property.name }}</h3>
                <div class="property-location">
                  <MapPinIcon class="location-icon" />
                  <span>{{ property.location }}</span>
                </div>
                <div class="property-features">
                  <div class="feature">
                    <BedIcon class="feature-icon" />
                    <span>{{ property.bedrooms }} {{ property.bedrooms === 1 ? 'habitación' : 'habitaciones' }}</span>
                  </div>
                  <div class="feature">
                    <UsersIcon class="feature-icon" />
                    <span>{{ property.capacity }} {{ property.capacity === 1 ? 'huésped' : 'huéspedes' }}</span>
                  </div>
                </div>
                <div class="property-rating">
                  <StarIcon class="rating-icon" />
                  <span class="rating-value">{{ property.rating || '4.8' }}</span>
                  <span class="rating-count">({{ property.reviewCount || '24' }} reseñas)</span>
                </div>
                <div class="property-price">
                  <span class="price-value">€{{ property.price }}</span>
                  <span class="price-period">noche</span>
                </div>
                <button class="view-property-button" @click="viewProperty(property.id)">Ver Detalles</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="travel-inspiration">
          <h3 class="section-title">Inspiración para tu próximo viaje</h3>
          <div class="inspiration-grid">
            <div class="inspiration-card beach">
              <div class="inspiration-content">
                <h4>Destinos de Playa</h4>
                <p>Descubre los mejores apartamentos frente al mar</p>
                <button class="inspiration-button" @click="exploreCategory('beach')">Explorar</button>
              </div>
            </div>
            <div class="inspiration-card mountain">
              <div class="inspiration-content">
                <h4>Escapadas a la Montaña</h4>
                <p>Relájate en la naturaleza con vistas impresionantes</p>
                <button class="inspiration-button" @click="exploreCategory('mountain')">Explorar</button>
              </div>
            </div>
            <div class="inspiration-card city">
              <div class="inspiration-content">
                <h4>Experiencias Urbanas</h4>
                <p>Vive la emoción de las ciudades más vibrantes</p>
                <button class="inspiration-button" @click="exploreCategory('city')">Explorar</button>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <router-view />
    </main>
    
    <RentersFooter 
      v-if="isAuthenticated && !isLoginPage" 
      :activeTab="activeTab"
      @changeTab="handleTabChange"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import RentersHeader from '@/components/layout/RentersHeader.vue';
import RentersFooter from '@/components/layout/RentersFooter.vue';
import { 
  CalendarIcon, 
  ClockIcon, 
  MessageSquareIcon, 
  HeartIcon, 
  MapPinIcon, 
  SearchIcon,
  UserIcon, 
  MailIcon, 
  BedIcon, 
  UsersIcon, 
  StarIcon,
  ChevronRightIcon,
  CalendarOffIcon
} from 'lucide-vue-next';

// Active tab for the footer navigation
const activeTab = ref('dashboard');

// Get current route
const route = useRoute();

// Check if user is on login page
const isLoginPage = computed(() => {
  return route.path === '/renters/login' || route.path === '/renters/register';
});

// In a real app, this would come from your auth store
const isAuthenticated = computed(() => {
  // For demo purposes, consider user authenticated if not on login/register page
  return !isLoginPage.value || localStorage.getItem('renters_auth') === 'true';
});

// User name (would come from auth store in real app)
const userName = ref('Carlos');

// Sample data for the dashboard
const activeBookings = ref([
  {
    id: 'B1234',
    property: {
      id: 1,
      name: 'Apartamento Vista Mar',
      image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80'
    },
    checkIn: '2023-08-15',
    checkOut: '2023-08-22',
    status: 'confirmed'
  }
]);

const nextBookingDate = computed(() => {
  if (activeBookings.value.length === 0) return null;
  return formatDate(activeBookings.value[0].checkIn);
});

const unreadMessages = ref(2);

const favorites = ref([1, 3]);

const recentMessages = ref([
  {
    id: 'M1',
    sender: 'Ana (Propietaria)',
    property: { name: 'Apartamento Vista Mar' },
    text: 'Hola Carlos, espero que estés bien. Quería confirmar tu hora de llegada para el próximo viernes.',
    createdAt: '2023-08-10T14:30:00'
  }
]);

const recommendedProperties = ref([
  {
    id: 1,
    name: 'Apartamento Vista Mar',
    location: 'Calahonda, Málaga',
    bedrooms: 2,
    capacity: 4,
    price: 120,
    rating: 4.9,
    reviewCount: 28,
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80'
  },
  {
    id: 2,
    name: 'Villa con Piscina',
    location: 'Marbella, Málaga',
    bedrooms: 3,
    capacity: 6,
    price: 180,
    rating: 4.7,
    reviewCount: 15,
    image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80'
  },
  {
    id: 3,
    name: 'Ático de Lujo',
    location: 'Fuengirola, Málaga',
    bedrooms: 2,
    capacity: 4,
    price: 150,
    rating: 4.8,
    reviewCount: 32,
    image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80'
  }
]);

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return '';
  const date = new Date(dateTimeString);
  const now = new Date();
  const diffMs = now - date;
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) {
    return 'Hoy';
  } else if (diffDays === 1) {
    return 'Ayer';
  } else if (diffDays < 7) {
    return `Hace ${diffDays} días`;
  } else {
    return formatDate(dateTimeString);
  }
};

const getStatusText = (status) => {
  const statusMap = {
    'pending': 'Pendiente',
    'confirmed': 'Confirmada',
    'completed': 'Completada',
    'cancelled': 'Cancelada'
  };
  return statusMap[status] || status;
};

const isFavorite = (propertyId) => {
  return favorites.value.includes(propertyId);
};

const toggleFavorite = (propertyId) => {
  const index = favorites.value.indexOf(propertyId);
  if (index === -1) {
    favorites.value.push(propertyId);
  } else {
    favorites.value.splice(index, 1);
  }
};

const viewProperty = (propertyId) => {
  console.log(`Viewing property ${propertyId}`);
  // In a real app, you would navigate to the property details page
};

const changeTab = (tab) => {
  activeTab.value = tab;
};

const handleTabChange = (tabId) => {
  activeTab.value = tabId;
};

const exploreCategory = (category) => {
  console.log(`Exploring ${category} category`);
  // In a real app, you would navigate to the search page with the category filter
};
</script>

<style scoped>
.renters-portal {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #f5f5f5;
}

.main-content {
  flex: 1;
  padding: 0 0 2rem;
}

.dashboard-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Welcome Banner */
.welcome-banner {
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  border-radius: 16px;
  padding: 2rem;
  margin: 1.5rem 0 2rem;
  color: #1e293b;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.welcome-banner::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(251, 191, 36, 0.4) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(245, 158, 11, 0.3) 0%, transparent 60%);
  z-index: 0;
}

.welcome-content {
  position: relative;
  z-index: 1;
  margin-bottom: 1.5rem;
}

.welcome-title {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #1e293b;
}

.welcome-subtitle {
  font-size: 1.1rem;
  color: #334155;
  max-width: 600px;
}

.quick-search {
  display: flex;
  gap: 1rem;
  max-width: 600px;
  position: relative;
  z-index: 1;
}

.search-input {
  flex: 1;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #f59e0b;
  width: 20px;
  height: 20px;
}

.destination-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.destination-input:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3), 0 2px 4px rgba(0, 0, 0, 0.05);
}

.search-button {
  background-color: #1e293b;
  color: white;
  border: none;
  padding: 0 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-button:hover {
  background-color: #0f172a;
  transform: translateY(-1px);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.stat-icon-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon {
  width: 24px;
  height: 24px;
  color: white;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.stat-title {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
}

/* Dashboard Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.dashboard-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

.card-header h3 {
  font-size: 1.1rem;
  color: #1e293b;
  margin: 0;
}

.view-all-button {
  background: none;
  border: none;
  color: #f59e0b;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  transition: color 0.3s ease;
}

.view-all-button:hover {
  color: #d97706;
}

.view-all-button .button-icon {
  width: 16px;
  height: 16px;
}

.upcoming-bookings {
  padding: 1.5rem;
}

.booking-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.booking-item:last-child {
  padding-bottom: 0;
  margin-bottom: 0;
  border-bottom: none;
}

.booking-property {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.booking-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
}

.booking-property h4 {
  font-size: 1rem;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.booking-dates {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}

.booking-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

.booking-status.confirmed {
  background-color: #ecfdf5;
  color: #047857;
}

.booking-status.pending {
  background-color: #fffbeb;
  color: #b45309;
}

.booking-status.completed {
  background-color: #f1f5f9;
  color: #475569;
}

.booking-status.cancelled {
  background-color: #fef2f2;
  color: #b91c1c;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1.5rem;
  text-align: center;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #cbd5e1;
  margin-bottom: 1rem;
}

.empty-state p {
  color: #64748b;
  margin-bottom: 1.5rem;
}

.action-button {
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.action-icon {
  width: 18px;
  height: 18px;
}

.messages-list {
  padding: 1.5rem;
}

.message-item {
  margin-bottom: 1.25rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #f1f5f9;
}

.message-item:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.message-sender {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
}

.sender-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-icon {
  width: 20px;
  height: 20px;
  color: #64748b;
}

.message-sender h4 {
  font-size: 0.95rem;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.message-property {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

.message-preview {
  font-size: 0.9rem;
  color: #334155;
  margin: 0 0 0.5rem;
}

.message-time {
  font-size: 0.8rem;
  color: #94a3b8;
  text-align: right;
  margin: 0;
}

/* Recommended Section */
.recommended-section {
  margin-bottom: 2.5rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.3rem;
  color: #1e293b;
  margin: 0;
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.property-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.property-image-container {
  position: relative;
  height: 200px;
}

.property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.property-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.favorite-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: white;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.favorite-button:hover {
  transform: scale(1.1);
}

.favorite-button.active .favorite-icon {
  fill: #ef4444;
  color: #ef4444;
}

.favorite-icon {
  width: 20px;
  height: 20px;
  color: #94a3b8;
}

.property-content {
  padding: 1.5rem;
}

.property-name {
  font-size: 1.1rem;
  color: #1e293b;
  margin: 0 0 0.5rem;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  color: #64748b;
}

.location-icon {
  width: 16px;
  height: 16px;
  color: #f59e0b;
}

.property-features {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #64748b;
}

.feature-icon {
  width: 16px;
  height: 16px;
  color: #f59e0b;
}

.property-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.rating-icon {
  width: 16px;
  height: 16px;
  color: #f59e0b;
}

.rating-value {
  font-weight: 600;
  color: #1e293b;
}

.rating-count {
  font-size: 0.85rem;
  color: #64748b;
}

.property-price {
  margin-bottom: 1rem;
}

.price-value {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.price-period {
  font-size: 0.9rem;
  color: #64748b;
}

.view-property-button {
  width: 100%;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
  border: none;
  padding: 0.75rem 0;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.view-property-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

/* Travel Inspiration */
.travel-inspiration {
  margin-bottom: 2rem;
}

.inspiration-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.inspiration-card {
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  cursor: pointer;
}

.inspiration-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 60%);
  z-index: 1;
}

.inspiration-card.beach {
  background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80') center/cover no-repeat;
}

.inspiration-card.mountain {
  background: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80') center/cover no-repeat;
}

.inspiration-card.city {
  background: url('https://images.unsplash.com/photo-1514565131-fce0801e5785?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2532&q=80') center/cover no-repeat;
}

.inspiration-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 1.5rem;
  z-index: 2;
  color: white;
}

.inspiration-content h4 {
  font-size: 1.2rem;
  margin: 0 0 0.5rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.inspiration-content p {
  font-size: 0.9rem;
  margin: 0 0 1rem;
  opacity: 0.9;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.inspiration-button {
  background-color: white;
  color: #1e293b;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.inspiration-button:hover {
  background-color: #f59e0b;
  color: white;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .welcome-banner {
    padding: 1.5rem;
  }
  
  .welcome-title {
    font-size: 1.5rem;
  }
  
  .quick-search {
    flex-direction: column;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .properties-grid {
    grid-template-columns: 1fr;
  }
  
  .inspiration-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .property-features {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>