<template>
  <div class="dashboard-section">
    <div class="section-header">
      <h2 class="section-title">Panel de Control</h2>
      <div class="welcome-message">
        <p>
          Bienvenido,
          <span class="user-name">{{ userSummary?.name || 'Usuario' }}</span>
        </p>
        <p class="last-login">
          Último acceso: {{ userSummary?.lastLogin ? formatDate(userSummary.lastLogin) : '-' }}
        </p>
      </div>
    </div>

    <!-- Resumen del usuario obtenido vía API -->
    <div v-if="loadingUser" class="user-summary">Cargando resumen...</div>
    <div v-else-if="userError" class="user-summary error">Error: {{ userError }}</div>
    <div v-else class="user-summary">
      <ul>
        <li><strong>Email:</strong> {{ userSummary.email }}</li>
        <li><strong>Propiedades:</strong> {{ userSummary.totalProperties }}</li>
        <li><strong>Reservas totales:</strong> {{ userSummary.totalBookings }}</li>
        <li><strong>Ingresos del mes:</strong> {{ userSummary.monthlyRevenue }} €</li>
        <li><strong>Ocupación:</strong> {{ userSummary.occupancyRate }} %</li>
      </ul>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <HomeIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ userSummary?.totalProperties ?? '-' }}</p>
          <h3 class="stat-title">Propiedades</h3>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <CalendarIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ userSummary?.totalBookings ?? '-' }}</p>
          <h3 class="stat-title">Reservas</h3>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <TrendingUpIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ userSummary?.occupancyRate ?? '-' }}%</p>
          <h3 class="stat-title">Ocupación</h3>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <EuroIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ userSummary?.monthlyRevenue ?? '-' }}€</p>
          <h3 class="stat-title">Ingresos</h3>
        </div>
      </div>
    </div>
    
    <div class="dashboard-grid">
      <div class="dashboard-column">
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Próximas Reservas</h3>
            <button class="view-all-button" @click="navigateTo('bookings')">
              Ver todas
              <ChevronRightIcon class="button-icon" />
            </button>
          </div>
          
          <div v-if="upcomingBookings.length > 0" class="upcoming-bookings">
            <div v-for="booking in upcomingBookings" :key="booking.id" class="booking-item">
              <div class="booking-property">
                <img :src="getPropertyById(booking.property_id)?.image || '/img/placeholder.jpg'" />
                <h4>{{ getPropertyById(booking.property_id)?.name || 'Propiedad' }}</h4>
                <p class="booking-dates">{{ formatDate(booking.details?.check_in) }} - {{ formatDate(booking.details?.check_out) }}</p>
              </div>
              <div class="booking-guest">
                <UserIcon class="guest-icon" />
                <div>
                  <p class="guest-name">{{ booking.guestName || 'Invitado' }}</p>
                  <p class="guest-info">{{ booking.details?.guests || '-' }} huéspedes</p>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <CalendarOffIcon class="empty-icon" />
            <p>No hay reservas próximas</p>
            <button class="action-button" @click="navigateTo('calendar')">
              Ver calendario
            </button>
          </div>
        </div>
        
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Rendimiento de Propiedades</h3>
            <button class="view-all-button" @click="navigateTo('properties')">
              Ver todas
              <ChevronRightIcon class="button-icon" />
            </button>
          </div>
          
          <div class="property-performance">
            <div v-for="property in properties.slice(0,3)" :key="property.id" class="property-item">
              <div class="property-info">
                <img :src="property.image" alt="Property" class="property-thumbnail" />
                <div>
                  <h4>{{ property.name }}</h4>
                  <p class="property-location">{{ property.location }}</p>
                </div>
              </div>
              <div class="property-stats">
                <div class="property-stat">
                  <p class="stat-label">Ocupación</p>
                  <div class="progress-bar">
                    <div class="progress" :style="{ width: `${property.occupancy}%` }"></div>
                  </div>
                  <p class="stat-percentage">{{ property.occupancy }}%</p>
                </div>
                <div class="property-stat">
                  <p class="stat-label">Ingresos</p>
                  <p class="stat-amount">{{ property.revenue }}€</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="dashboard-column">
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Mensajes Recientes</h3>
            <button class="view-all-button" @click="navigateTo('messages')">
              Ver todos
              <ChevronRightIcon class="button-icon" />
            </button>
          </div>
          
          <div v-if="recentMessages.length > 0" class="messages-list">
            <div v-for="message in recentMessages" :key="message.id" class="message-item">
              <div class="message-sender">
                <UserIcon class="message-icon" />
                <div>
                  <h4>{{ message.sender }}</h4>
                  <p class="message-property">{{ getPropertyById(message.propertyId)?.name || 'Propiedad desconocida' }}</p>
                </div>
              </div>
              <p class="message-preview">{{ message.text.substring(0, 60) }}{{ message.text.length > 60 ? '...' : '' }}</p>
              <p class="message-time">{{ formatDateTime ? formatDateTime(message.time) : message.time }}</p>
            </div>
          </div>
          
          <div v-else class="empty-state">
            <MailIcon class="empty-icon" />
            <p>No hay mensajes nuevos</p>
          </div>
        </div>
        
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Actividad Reciente</h3>
          </div>
          
          <div class="activity-list">
            <div v-for="(activity, index) in recentActivity" :key="index" class="activity-item">
              <div class="activity-icon-wrapper" :class="activity.type">
                <component :is="getActivityIcon(activity.type)" class="activity-icon" />
              </div>
              <div class="activity-content">
                <p class="activity-text" v-html="activity.text"></p>
                <p class="activity-time">{{ activity.time }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="dashboard-card quick-actions">
          <div class="card-header">
            <h3>Acciones Rápidas</h3>
          </div>
          
          <div class="actions-grid">
            <button class="action-button" @click="navigateTo('properties')">
              <PlusIcon class="action-icon" />
              <span>Añadir Propiedad</span>
            </button>
            
            <button class="action-button" @click="navigateTo('calendar')">
              <CalendarIcon class="action-icon" />
              <span>Gestionar Calendario</span>
            </button>
            
            <button class="action-button" @click="navigateTo('messages')">
              <MessageSquareIcon class="action-icon" />
              <span>Enviar Mensaje</span>
            </button>
            
            <button class="action-button" @click="navigateTo('settings')">
              <SettingsIcon class="action-icon" />
              <span>Configuración</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import {
  HomeIcon, CalendarIcon, TrendingUpIcon, EuroIcon, UserIcon, CalendarOffIcon,
  MailIcon, MessageSquareIcon, SettingsIcon, PlusIcon, CheckIcon, AlertCircleIcon,
  BellIcon, ChevronRightIcon
} from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const router = useRouter()

// Estado local para el resumen de usuario y manejo de carga/error
const userSummary = ref(null)
const loadingUser = ref(true)
const userError = ref(null)

// Propiedades de ejemplo (puedes cargar desde API real si lo necesitas)
const properties = ref([]) // Array de propiedades [{ id, name, image, location, occupancy, revenue }]
const recentMessages = ref([]) // Array de mensajes recientes
const recentActivity = ref([]) // Array de actividad reciente

// Cargar datos del usuario y las propiedades al montar
onMounted(async () => {
  loadingUser.value = true
  userError.value = null
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.get('http://localhost:8000/api/user/summary', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    userSummary.value = res.data

    // Si tu backend devuelve las propiedades y mensajes recientes, asígnalos aquí
    // properties.value = res.data.properties || []
    // recentMessages.value = res.data.recentMessages || []
    // recentActivity.value = res.data.recentActivity || []

  } catch (err) {
    userError.value = err.response?.data?.message || err.message
  } finally {
    loadingUser.value = false
  }
})

// Computed para próximas reservas y mensajes recientes
const upcomingBookings = computed(() => userSummary.value?.upcomingBookings || [])
// Si tu backend devuelve las propiedades, usa userSummary.value.properties o properties.value
const getPropertyById = (id) => {
  return properties.value.find(p => p.id === id)
}

// Fechas
const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString()
}
const formatDateTime = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleString()
}

// Navegación
const navigateTo = (route) => {
  router.push(`/manage/${route}`)
}

// Icono de actividad
const getActivityIcon = (type) => {
  const icons = {
    'booking': CalendarIcon,
    'message': MessageSquareIcon,
    'alert': AlertCircleIcon,
    'update': BellIcon
  }
  return icons[type] || BellIcon
}

</script>

<style scoped>
.dashboard-section {
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

.dashboard-section::before {
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

.welcome-message {
  text-align: right;
}

.welcome-message p {
  margin: 0;
  color: #64748b;
}

.user-name {
  color: #0071c2;
  font-weight: 600;
}

.last-login {
  font-size: 0.9rem;
  color: #94a3b8;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  position: relative;
  z-index: 1;
}

.stat-card {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0));
  opacity: 0;
  transition: opacity 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 53, 128, 0.1);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-icon-wrapper {
  position: relative;
  width: 60px;
  height: 60px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon {
  width: 30px;
  height: 30px;
  color: #0071c2;
  position: relative;
  z-index: 1;
}

.stat-icon-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle, rgba(0, 113, 194, 0.2) 0%, rgba(0, 113, 194, 0) 70%);
  border-radius: 12px;
  opacity: 0;
  transition: opacity 0.3s, transform 0.3s;
}

.stat-card:hover .stat-icon-glow {
  opacity: 1;
  transform: scale(1.2);
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #003580;
  margin: 0 0 0.25rem;
}

.stat-title {
  font-size: 1rem;
  color: #64748b;
  margin: 0;
}

/* Dashboard Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 1.5rem;
  position: relative;
  z-index: 1;
}

.dashboard-column {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.dashboard-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.dashboard-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 53, 128, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.card-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.view-all-button {
  background: none;
  border: none;
  color: #0071c2;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  transition: color 0.3s ease;
}

.view-all-button:hover {
  color: #003580;
}

.view-all-button .button-icon {
  width: 16px;
  height: 16px;
}

/* Upcoming Bookings */
.upcoming-bookings {
  padding: 1.5rem;
}

.booking-item {
  display: flex;
  justify-content: space-between;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e6f0ff;
}

.booking-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
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
  margin: 0 0 0.25rem;
  color: #003580;
}

.booking-dates {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}

.booking-guest {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.guest-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.guest-name {
  font-size: 0.9rem;
  font-weight: 500;
  margin: 0 0 0.25rem;
  color: #1e293b;
}

.guest-info {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

/* Property Performance */
.property-performance {
  padding: 1.5rem;
}

.property-item {
  margin-bottom: 1.5rem;
}

.property-item:last-child {
  margin-bottom: 0;
}

.property-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.property-thumbnail {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
}

.property-info h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  color: #003580;
}

.property-location {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}

.property-stats {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.property-stat {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.stat-label {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
  width: 80px;
}

.progress-bar {
  flex: 1;
  height: 8px;
  background-color: #e6f0ff;
  border-radius: 4px;
  overflow: hidden;
}

.progress {
  height: 100%;
  background: linear-gradient(to right, #0071c2, #003580);
  border-radius: 4px;
}

.stat-percentage {
  font-size: 0.9rem;
  font-weight: 600;
  color: #003580;
  margin: 0;
  width: 40px;
  text-align: right;
}

.stat-amount {
  font-size: 0.9rem;
  font-weight: 600;
  color: #003580;
  margin: 0;
}

/* Messages List */
.messages-list {
  padding: 1.5rem;
}

.message-item {
  margin-bottom: 1.25rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #e6f0ff;
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

.message-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.message-sender h4 {
  font-size: 0.95rem;
  margin: 0 0 0.25rem;
  color: #1e293b;
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

/* Activity List */
.activity-list {
  padding: 1.5rem;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.activity-item:last-child {
  margin-bottom: 0;
}

.activity-icon-wrapper {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.activity-icon-wrapper.booking {
  background-color: #e6f0ff;
}

.activity-icon-wrapper.message {
  background-color: #e6f7ee;
}

.activity-icon-wrapper.alert {
  background-color: #fff2f0;
}

.activity-icon-wrapper.update {
  background-color: #fffbeb;
}

.activity-icon {
  width: 18px;
  height: 18px;
}

.activity-icon-wrapper.booking .activity-icon {
  color: #0071c2;
}

.activity-icon-wrapper.message .activity-icon {
  color: #00703c;
}

.activity-icon-wrapper.alert .activity-icon {
  color: #e41c00;
}

.activity-icon-wrapper.update .activity-icon {
  color: #b45309;
}

.activity-content {
  flex: 1;
}

.activity-text {
  font-size: 0.9rem;
  color: #334155;
  margin: 0 0 0.25rem;
}

.activity-time {
  font-size: 0.8rem;
  color: #94a3b8;
  margin: 0;
}

/* Quick Actions */
.quick-actions .actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  padding: 1.5rem;
}

.action-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
  border: 1px solid #e6f0ff;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.1);
  background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
}

.action-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
}

.action-button span {
  font-size: 0.9rem;
  font-weight: 500;
  color: #1e293b;
  text-align: center;
}

/* Empty State */
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
  color: #ccc;
  margin-bottom: 1rem;
}

.empty-state p {
  color: #64748b;
  margin-bottom: 1.5rem;
}

.empty-state .action-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  flex-direction: row;
}

.empty-state .action-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.2);
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .dashboard-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
  
  .stat-card {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .booking-item, .property-item {
    flex-direction: column;
    gap: 1rem;
  }
  
  .quick-actions .actions-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  }
}

@media (max-width: 576px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .property-stat {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .stat-label, .stat-percentage {
    width: auto;
  }
}
</style>