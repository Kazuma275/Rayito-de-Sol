<template>
  <div class="property-management">
    <!-- Header -->

    <Header :user="user" :activeTab="activeTab" @changeTab="changeTab" />

    <!-- Main Content -->
    <main class="main-content">
      <!-- Dashboard -->
      <section v-if="activeTab === 'dashboard'" class="dashboard-section">
        <h2 class="section-title">Panel de Control</h2>
        
        <div class="stats-grid">
          <div class="stat-card">
            <HomeIcon class="stat-icon" />
            <div class="stat-content">
              <h3 class="stat-title">Propiedades</h3>
              <p class="stat-value">{{ properties.length }}</p>
            </div>
          </div>
          
          <div class="stat-card">
            <CalendarIcon class="stat-icon" />
            <div class="stat-content">
              <h3 class="stat-title">Reservas Activas</h3>
              <p class="stat-value">{{ activeBookings }}</p>
            </div>
          </div>
          
            <div class="stat-card">
              <TrendingUpIcon class="stat-icon" />
              <div class="stat-content">
                <h3 class="stat-title">Tasa de Ocupación</h3>
                <p class="stat-value">{{ occupancyRate }}%</p> <!-- Dinámico -->
              </div>
            </div>
          
          <div class="stat-card">
            <EuroIcon class="stat-icon" />
            <div class="stat-content">
              <h3 class="stat-title">Ingresos (30 días)</h3>
              <p class="stat-value">€{{ monthlyRevenue }}</p>
            </div>
          </div>
        </div>
        
        <div class="dashboard-grid">
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Próximas Reservas</h3>
              <router-link to="/manage/bookings" class="view-all-button">Ver todas</router-link>
            </div>
            <div v-if="upcomingBookings.length > 0" class="upcoming-bookings">
              <div v-for="(booking, index) in upcomingBookings" :key="index" class="booking-item">
                <div class="booking-property">
                  <img :src="getPropertyById(booking.propertyId).image" alt="Property" class="booking-image" />
                  <div>
                    <h4>{{ getPropertyById(booking.propertyId).name }}</h4>
                    <p class="booking-dates">{{ booking.checkIn }} - {{ booking.checkOut }}</p>
                  </div>
                </div>
                <div class="booking-guest">
                  <UserIcon class="guest-icon" />
                  <div>
                    <p class="guest-name">{{ booking.guestName }}</p>
                    <p class="guest-info">{{ booking.guests }} huéspedes</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="empty-state">
              <CalendarOffIcon class="empty-icon" />
              <p>No hay reservas próximas</p>
            </div>
          </div>
          
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Mensajes Recientes</h3>
              <router-link to="/manage/messages" class="view-all-button">Ver todos</router-link>
            </div>
            <div v-if="messages.length > 0" class="messages-list">
              <div v-for="(message, index) in messages" :key="index" class="message-item">
                <div class="message-sender">
                  <UserIcon class="message-icon" />
                  <div>
                    <h4>{{ message.sender }}</h4>
                    <p class="message-property">{{ getPropertyById(message.propertyId).name }}</p>
                  </div>
                </div>
                <p class="message-preview">{{ message.text.substring(0, 60) }}{{ message.text.length > 60 ? '...' : '' }}</p>
                <p class="message-time">{{ message.time }}</p>
              </div>
            </div>
            <div v-else class="empty-state">
              <MailIcon class="empty-icon" />
              <p>No hay mensajes nuevos</p>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Properties -->
      <section v-if="activeTab === 'properties'" class="properties-section">
        <div class="section-header">
          <h2 class="section-title">Mis Propiedades</h2>
          <button class="add-button" @click="showAddPropertyModal = true">
            <PlusIcon class="add-icon" />
            Añadir Propiedad
          </button>
        </div>
        
        <div v-if="properties.length > 0" class="properties-grid">
          <div v-for="(property, index) in properties" :key="index" class="property-card">
            <div class="property-image-container">
              <img :src="property.image" alt="Property" class="property-image" />
              <div class="property-status" :class="property.status">
                {{ property.statusText }}
              </div>
            </div>
            <div class="property-content">
              <h3 class="property-name">{{ property.name }}</h3>
              <div class="property-location">
                <MapPinIcon class="location-icon" />
                <span>{{ property.location }}</span>
              </div>
              <div class="property-details">
                <div class="property-detail">
                  <BedIcon class="detail-icon" />
                  <span>{{ property.bedrooms }} dormitorios</span>
                </div>
                <div class="property-detail">
                  <UsersIcon class="detail-icon" />
                  <span>{{ property.capacity }} huéspedes</span>
                </div>
                <div class="property-detail">
                  <EuroIcon class="detail-icon" />
                  <span>€{{ property.price }}/noche</span>
                </div>
              </div>
            </div>
            <div class="property-actions">
              <button class="action-button edit" @click="editProperty(property.id)">
                <EditIcon class="action-icon" />
                Editar
              </button>
              <button class="action-button calendar" @click="viewCalendar(property.id)">
                <CalendarIcon class="action-icon" />
                Calendario
              </button>
            </div>
          </div>
        </div>
        
        <div v-else class="empty-properties">
          <HomeIcon class="empty-icon" />
          <h3>No tienes propiedades registradas</h3>
          <p>Añade tu primera propiedad para empezar a recibir reservas</p>
          <button class="add-property-button" @click="showAddPropertyModal = true">
            Añadir Propiedad
          </button>
        </div>
        
        <!-- Add Property Modal -->
        <div v-if="showAddPropertyModal" class="modal-overlay" @click="showAddPropertyModal = false">
          <div class="modal-content" @click.stop>
            <div class="modal-header">
              <h3>Añadir Nueva Propiedad</h3>
              <button class="close-button" @click="showAddPropertyModal = false">
                <XIcon />
              </button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="addProperty" class="property-form">
                <div class="form-group">
                  <label for="property-name">Nombre de la propiedad</label>
                  <input id="property-name" v-model="newProperty.name" type="text" class="form-input" required />
                </div>
                
                <div class="form-group">
                  <label for="property-location">Ubicación</label>
                  <input id="property-location" v-model="newProperty.location" type="text" class="form-input" required />
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="property-bedrooms">Dormitorios</label>
                    <input id="property-bedrooms" v-model="newProperty.bedrooms" type="number" min="1" class="form-input" required />
                  </div>
                  
                  <div class="form-group">
                    <label for="property-capacity">Capacidad (huéspedes)</label>
                    <input id="property-capacity" v-model="newProperty.capacity" type="number" min="1" class="form-input" required />
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="property-price">Precio por noche (€)</label>
                  <input id="property-price" v-model="newProperty.price" type="number" min="1" class="form-input" required />
                </div>
                
                <div class="form-group">
                  <label for="property-description">Descripción</label>
                  <textarea id="property-description" v-model="newProperty.description" class="form-textarea" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                  <label>Fotos</label>
                  <div class="photo-upload" @click="$refs.imageInput.click()">
                    <div class="upload-placeholder" v-if="!newProperty.image">
                      <UploadIcon class="upload-icon" />
                      <span>Subir foto</span>
                    </div>
                    <img v-else :src="newProperty.image" class="uploaded-image-preview" />
                    <input
                      ref="imageInput"
                      type="file"
                      accept="image/*"
                      @change="handleImageUpload"
                      style="display: none"
                    />
                  </div>
                </div>
                
                <div class="form-group">
                  <label>Servicios</label>
                  <div class="amenities-grid">
                    <div v-for="(amenity, index) in amenities" :key="index" class="amenity-checkbox">
                      <input :id="`amenity-${index}`" type="checkbox" v-model="newProperty.amenities" :value="amenity.id" />
                      <label :for="`amenity-${index}`">{{ amenity.name }}</label>
                    </div>
                  </div>
                </div>
                
                <div class="form-actions">
                  <button type="button" class="cancel-button" @click="showAddPropertyModal = false">Cancelar</button>
                  <button type="submit" class="submit-button">Guardar Propiedad</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Bookings -->
      <section v-if="activeTab === 'bookings'" class="bookings-section">
        <h2 class="section-title">Reservas</h2>
        
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
            </button>
          </div>
          
          <div class="filter-property">
            <label>Filtrar por propiedad:</label>
            <select v-model="bookingPropertyFilter" class="property-select">
              <option value="all">Todas las propiedades</option>
              <option v-for="property in properties" :key="property.id" :value="property.id">
                {{ property.name }}
              </option>
            </select>
          </div>
        </div>
        
        <div v-if="filteredBookings.length > 0" class="bookings-list">
          <div v-for="(booking, index) in filteredBookings" :key="index" class="booking-card">
            <div class="booking-header">
              <div class="booking-property-info">
                <img :src="getPropertyById(booking.propertyId).image" alt="Property" class="booking-property-image" />
                <div>
                  <h3>{{ getPropertyById(booking.propertyId).name }}</h3>
                  <div class="booking-id">Reserva #{{ booking.id }}</div>
                </div>
              </div>
              <div class="booking-status" :class="booking.status">
                {{ booking.statusText }}
              </div>
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
              <button class="action-button view">Ver detalles</button>
              <button v-if="booking.status === 'pending'" class="action-button accept">Aceptar</button>
              <button v-if="booking.status === 'pending'" class="action-button reject">Rechazar</button>
              <button v-if="booking.status === 'confirmed'" class="action-button message">
                <MessageSquareIcon class="action-icon" />
                Mensaje
              </button>
            </div>
          </div>
        </div>
        
        <div v-else class="empty-bookings">
          <CalendarOffIcon class="empty-icon" />
          <h3>No hay reservas {{ activeBookingFilter === 'all' ? '' : 'en este estado' }}</h3>
          <p v-if="activeBookingFilter !== 'all'">Prueba a seleccionar otro filtro</p>
        </div>
      </section>
      
      <!-- Calendar - VERSIÓN MEJORADA -->
      <section v-if="activeTab === 'calendar'" class="calendar-section">
        <div class="section-header">
          <h2 class="section-title">Calendario de Disponibilidad</h2>
          <div class="property-selector">
            <select v-model="calendarPropertyId" class="property-select">
              <option v-for="property in properties" :key="property.id" :value="property.id">
                {{ property.name }}
              </option>
            </select>
            <ChevronDownIcon class="select-icon" />
          </div>
        </div>
        
        <div class="calendar-navigation">
          <button class="nav-button" @click="previousMonth">
            <ChevronLeftIcon />
          </button>
          <h3 class="current-month">{{ currentMonthName }} {{ currentYear }}</h3>
          <button class="nav-button" @click="nextMonth">
            <ChevronRightIcon />
          </button>
        </div>
        
        <transition name="calendar-fade" mode="out-in">
          <div :key="currentMonthKey" class="calendar-container">
            <div class="weekdays-header">
              <div v-for="day in weekDays" :key="day" class="weekday">{{ day }}</div>
            </div>
            <div class="calendar-grid">
              <div 
                v-for="(day, index) in calendarDays" 
                :key="index" 
                :class="[
                  'calendar-day', 
                  { 
                    'empty': !day.date, 
                    'available': day.available && day.date,
                    'unavailable': !day.available && day.date,
                    'booked': day.booked && day.date,
                    'today': day.isToday,
                    'in-selection': isInSelection(day)
                  }
                ]"
                @click="day.date && handleDayClick(day)"
                @mouseenter="day.date && handleDayHover(day)"
              >
                <span v-if="day.date" class="day-number">{{ day.date }}</span>
                <transition name="status-fade">
                  <div v-if="day.date" class="day-status">
                    <span v-if="day.booked" class="status booked">Reservado</span>
                    <span v-else-if="!day.available" class="status unavailable">No disponible</span>
                    <span v-else class="status available">Disponible</span>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </transition>
        
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
        </div>
        
        <div class="bulk-actions">
          <div class="bulk-header">
            <h4>Acciones en bloque</h4>
            <button v-if="isSelecting" class="cancel-selection" @click="cancelSelection">
              <XIcon class="icon" />
              Cancelar selección
            </button>
          </div>
          
          <div v-if="!isSelecting" class="selection-prompt">
            <p>Haz clic en una fecha para comenzar a seleccionar un rango</p>
          </div>
          
          <div v-else class="selection-info">
            <div class="date-range-display">
              <CalendarIcon class="icon" />
              <span>{{ formatDateRange(selectionStart, selectionEnd) }}</span>
            </div>
            
            <div class="action-buttons">
              <button class="action-button available" @click="applyBulkAction(true)">
                <CheckIcon class="icon" />
                Marcar como disponible
              </button>
              <button class="action-button unavailable" @click="applyBulkAction(false)">
                <XIcon class="icon" />
                Marcar como no disponible
              </button>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Messages -->
      <section v-if="activeTab === 'messages'" class="messages-section">
        <h2 class="section-title">Mensajes</h2>
        
        <div class="messages-container">
          <div class="messages-sidebar">
            <div class="messages-search">
              <SearchIcon class="search-icon" />
              <input type="text" placeholder="Buscar mensajes..." class="search-input" />
            </div>
            
            <div class="conversation-list">
              <div v-for="(conversation, index) in conversations" :key="index" 
                   class="conversation-item" 
                   :class="{ active: activeConversation === index }"
                   @click="activeConversation = index">
                <div class="conversation-avatar">
                  <UserIcon class="avatar-icon" />
                </div>
                <div class="conversation-info">
                  <div class="conversation-header">
                    <h4>{{ conversation.name }}</h4>
                    <span class="conversation-time">{{ conversation.lastMessage.time }}</span>
                  </div>
                  <p class="conversation-preview">{{ conversation.lastMessage.text.substring(0, 40) }}{{ conversation.lastMessage.text.length > 40 ? '...' : '' }}</p>
                  <div class="conversation-property">{{ getPropertyById(conversation.propertyId).name }}</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="messages-content">
            <div v-if="activeConversation !== null" class="conversation-view">
              <div class="conversation-header">
                <div class="conversation-user">
                  <UserIcon class="user-icon" />
                  <div>
                    <h3>{{ conversations[activeConversation].name }}</h3>
                    <p class="conversation-booking">Reserva #{{ conversations[activeConversation].bookingId }}</p>
                  </div>
                </div>
                <div class="conversation-property">
                  {{ getPropertyById(conversations[activeConversation].propertyId).name }}
                </div>
              </div>
              
              <div class="message-list">
                <div v-for="(message, index) in conversations[activeConversation].messages" :key="index" 
                     class="message-bubble" 
                     :class="{ 'message-sent': message.sent, 'message-received': !message.sent }">
                  <div class="message-content">{{ message.text }}</div>
                  <div class="message-time">{{ message.time }}</div>
                </div>
              </div>
              
              <div class="message-input">
                <textarea placeholder="Escribe un mensaje..." class="input-textarea" rows="3"></textarea>
                <button class="send-button">
                  <SendIcon class="send-icon" />
                  Enviar
                </button>
              </div>
            </div>
            
            <div v-else class="empty-conversation">
              <MessageSquareIcon class="empty-icon" />
              <h3>Selecciona una conversación</h3>
              <p>Elige una conversación de la lista para ver los mensajes</p>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Settings -->
      <section v-if="activeTab === 'settings'" class="settings-section">
        <h2 class="section-title">Configuración</h2>
        
        <div class="settings-container">
          <div class="settings-sidebar">
            <div v-for="(setting, index) in settingsTabs" :key="index" 
                 class="settings-tab" 
                 :class="{ active: activeSettingsTab === setting.id }"
                 @click="activeSettingsTab = setting.id">
              <component :is="setting.icon" class="settings-icon" />
              <span>{{ setting.name }}</span>
            </div>
          </div>
          
          <div class="settings-content">
            <!-- Profile Settings -->
            <div v-if="activeSettingsTab === 'profile'" class="settings-panel">
              <h3 class="panel-title">Perfil</h3>
              
              <form class="settings-form">
                <div class="profile-avatar">
                  <div class="avatar-placeholder">
                    <UserIcon class="avatar-icon" />
                  </div>
                  <button class="change-avatar-button">Cambiar foto</button>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="profile-name">Nombre</label>
                    <input id="profile-name" type="text" v-model="settings.profile.name" class="form-input" />
                  </div>
                  
                  <div class="form-group">
                    <label for="profile-lastname">Apellidos</label>
                    <input id="profile-lastname" type="text" v-model="settings.profile.lastname" class="form-input" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="profile-email">Email</label>
                  <input id="profile-email" type="email" v-model="settings.profile.email" class="form-input" />
                </div>
                
                <div class="form-group">
                  <label for="profile-phone">Teléfono</label>
                  <input id="profile-phone" type="tel" v-model="settings.profile.phone" class="form-input" />
                </div>
                
                <div class="form-actions">
                  <button type="submit" class="save-button">Guardar cambios</button>
                </div>
              </form>
            </div>
            
            <!-- Notifications Settings -->
            <div v-if="activeSettingsTab === 'notifications'" class="settings-panel">
              <h3 class="panel-title">Notificaciones</h3>
              
              <div class="notification-settings">
                <div class="notification-group">
                  <h4>Email</h4>
                  
                  <div class="notification-option">
                    <div>
                      <h5>Nuevas reservas</h5>
                      <p>Recibe un email cuando recibas una nueva reserva</p>
                    </div>
                    <label class="toggle">
                      <input type="checkbox" v-model="settings.notifications.newBookingEmail" />
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                  
                  <div class="notification-option">
                    <div>
                      <h5>Mensajes</h5>
                      <p>Recibe un email cuando recibas un nuevo mensaje</p>
                    </div>
                    <label class="toggle">
                      <input type="checkbox" v-model="settings.notifications.newMessageEmail" />
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                
                <div class="notification-group">
                  <h4>SMS</h4>
                  
                  <div class="notification-option">
                    <div>
                      <h5>Nuevas reservas</h5>
                      <p>Recibe un SMS cuando recibas una nueva reserva</p>
                    </div>
                    <label class="toggle">
                      <input type="checkbox" v-model="settings.notifications.newBookingSMS" />
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                
                <div class="form-actions">
                  <button class="save-button">Guardar preferencias</button>
                </div>
              </div>
            </div>
            
            <!-- Payment Settings -->
            <div v-if="activeSettingsTab === 'payment'" class="settings-panel">
              <h3 class="panel-title">Pagos</h3>
              
              <div class="payment-methods">
                <h4>Métodos de pago</h4>
                
                <div class="payment-method">
                  <div class="payment-info">
                    <CreditCardIcon class="payment-icon" />
                    <div>
                      <h5>Visa terminada en 4242</h5>
                      <p>Expira 12/2025</p>
                    </div>
                  </div>
                  <div class="payment-actions">
                    <button class="edit-button">Editar</button>
                    <button class="delete-button">Eliminar</button>
                  </div>
                </div>
                
                <button class="add-payment-button">
                  <PlusIcon class="add-icon" />
                  Añadir método de pago
                </button>
              </div>
              
              <div class="bank-account">
                <h4>Cuenta bancaria</h4>
                
                <div class="form-group">
                  <label for="bank-name">Nombre del banco</label>
                  <input id="bank-name" type="text" v-model="settings.payment.bankName" class="form-input" />
                </div>
                
                <div class="form-group">
                  <label for="account-holder">Titular de la cuenta</label>
                  <input id="account-holder" type="text" v-model="settings.payment.accountHolder" class="form-input" />
                </div>
                
                <div class="form-group">
                  <label for="iban">IBAN</label>
                  <input id="iban" type="text" v-model="settings.payment.iban" class="form-input" />
                </div>
                
                <div class="form-actions">
                  <button class="save-button">Guardar información bancaria</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    
    <Footer @changeTab="changeTab" />
    
  </div>
</template>

<script setup>
import Header from './components/layout/Header.vue';
import Footer from './components/layout/Footer.vue';
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
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
  TrendingUpIcon,
  EuroIcon,
  PlusIcon,
  XIcon,
  EditIcon,
  UploadIcon,
  MessageSquareIcon,
  SearchIcon,
  SendIcon,
  CreditCardIcon,
  BellIcon,
  CheckIcon,
  ChevronDownIcon
} from 'lucide-vue-next';

// Get router and route
const router = useRouter();
const route = useRoute();

// Tabs with paths for router
const tabs = [
  { id: 'dashboard', name: 'Panel', path: '/manage/dashboard' },
  { id: 'properties', name: 'Propiedades', path: '/manage/properties' },
  { id: 'bookings', name: 'Reservas', path: '/manage/bookings' },
  { id: 'calendar', name: 'Calendario', path: '/manage/calendar' },
  { id: 'messages', name: 'Mensajes', path: '/manage/messages' },
  { id: 'settings', name: 'Configuración', path: '/manage/settings' }
];

// Active tab state
const activeTab = ref('dashboard');

// Update active tab based on route
const updateActiveTabFromRoute = () => {
  const path = route.path;
  
  if (path.includes('/manage/dashboard')) {
    activeTab.value = 'dashboard';
  } else if (path.includes('/manage/properties')) {
    activeTab.value = 'properties';
  } else if (path.includes('/manage/bookings')) {
    activeTab.value = 'bookings';
  } else if (path.includes('/manage/calendar')) {
    activeTab.value = 'calendar';
  } else if (path.includes('/manage/messages')) {
    activeTab.value = 'messages';
  } else if (path.includes('/manage/settings')) {
    activeTab.value = 'settings';
  } else if (path.includes('/manage/help')) {
    activeTab.value = 'help';
  }
};

// Watch for route changes
watch(() => route.path, () => {
  updateActiveTabFromRoute();
}, { immediate: true });

// Initialize on mount
onMounted(() => {
  updateActiveTabFromRoute();
});

// Properties data
const properties = ref([
  {
    id: 1,
    name: 'Apartamento Vista al Mar',
    location: 'Playa de Calahonda, Mijas Costa',
    bedrooms: 2,
    capacity: 4,
    price: 120,
    image: '/placeholder.svg?height=300&width=400',
    description: 'Hermoso apartamento con vistas al mar Mediterráneo.',
    status: 'active',
    statusText: 'Activo',
    amenities: ['wifi', 'pool', 'parking', 'ac'],
  },
  {
    id: 2,
    name: 'Ático con Terraza',
    location: 'Puerto Banús, Marbella',
    bedrooms: 3,
    capacity: 6,
    price: 180,
    image: '/placeholder.svg?height=300&width=400',
    description: 'Lujoso ático con amplia terraza y vistas panorámicas.',
    status: 'inactive',
    statusText: 'Inactivo',
    amenities: ['wifi', 'pool', 'parking', 'ac', 'gym']
  }
]);

// Bookings data
const allBookings = ref([
  {
    id: 'B1234',
    propertyId: 1,
    guestName: 'María García',
    guests: 3,
    checkIn: '15 Jun 2023',
    checkOut: '22 Jun 2023',
    total: 890,
    status: 'completed',
    statusText: 'Completada'
  },
  {
    id: 'B5678',
    propertyId: 1,
    guestName: 'Juan Pérez',
    guests: 2,
    checkIn: '10 Aug 2023',
    checkOut: '17 Aug 2023',
    total: 980,
    status: 'confirmed',
    statusText: 'Confirmada'
  },
  {
    id: 'B9012',
    propertyId: 2,
    guestName: 'Ana Martínez',
    guests: 4,
    checkIn: '5 Sep 2023',
    checkOut: '12 Sep 2023',
    total: 1260,
    status: 'pending',
    statusText: 'Pendiente'
  }
]);

// Dashboard stats
const activeBookings = computed(() => {
  return allBookings.value.filter(booking => booking.status === 'confirmed').length;
});

const occupancyRate = computed(() => {
  return 75; // This would be calculated based on actual data
});

const monthlyRevenue = computed(() => {
  return 2130; // This would be calculated based on actual data
});

const upcomingBookings = computed(() => {
  return allBookings.value
    .filter(booking => booking.status === 'confirmed')
    .slice(0, 3);
});

// Messages data
const messages = ref([
  {
    sender: 'Juan Pérez',
    propertyId: 1,
    text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
    time: 'Hace 2 horas'
  },
  {
    sender: 'Ana Martínez',
    propertyId: 2,
    text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
    time: 'Hace 1 día'
  }
]);

// Property functions
const getPropertyById = (id) => {
  return properties.value.find(property => property.id === id) || { name: 'Propiedad no encontrada', image: '/placeholder.svg?height=100&width=100' };
};

// Add property modal
const showAddPropertyModal = ref(false);
  const newProperty = ref({
    id: null,
    name: '',
    location: '',
    bedrooms: 1,
    capacity: 1,
    price: 0,
    image: null,
    description: '',
    amenities: [],
    status: 'active',
    statusText: 'Activo',
  });

  const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        newProperty.value.image = e.target.result; // Base64 string
      };
      reader.readAsDataURL(file);
    }
  };

    const addProperty = () => {
    newProperty.value.id = Date.now(); // ID único simple
    properties.value.push({ ...newProperty.value });
    // Resetear
    newProperty.value = {
      id: null,
      name: '',
      location: '',
      bedrooms: 1,
      capacity: 1,
      price: 0,
      image: null,
      description: '',
      amenities: [],
      status: 'active',
      statusText: 'Activo',
    };
    showAddPropertyModal.value = false;
  };

const amenities = [
  { id: 'wifi', name: 'WiFi' },
  { id: 'pool', name: 'Piscina' },
  { id: 'parking', name: 'Aparcamiento' },
  { id: 'ac', name: 'Aire acondicionado' },
  { id: 'gym', name: 'Gimnasio' },
  { id: 'kitchen', name: 'Cocina equipada' },
  { id: 'tv', name: 'TV' },
  { id: 'washer', name: 'Lavadora' }
];

const editProperty = (id) => {
  console.log('Edit property', id);
  // This would open the edit modal with the property data
};

const viewCalendar = (id) => {
  calendarPropertyId.value = id;
  router.push('/manage/calendar');
};

// Bookings filters
const bookingFilters = [
  { id: 'all', name: 'Todas' },
  { id: 'pending', name: 'Pendientes' },
  { id: 'confirmed', name: 'Confirmadas' },
  { id: 'completed', name: 'Completadas' },
  { id: 'cancelled', name: 'Canceladas' }
];

const activeBookingFilter = ref('all');
const bookingPropertyFilter = ref('all');

const filteredBookings = computed(() => {
  let filtered = allBookings.value;
  
  if (activeBookingFilter.value !== 'all') {
    filtered = filtered.filter(booking => booking.status === activeBookingFilter.value);
  }
  
  if (bookingPropertyFilter.value !== 'all') {
    filtered = filtered.filter(booking => booking.propertyId === parseInt(bookingPropertyFilter.value));
  }
  
  return filtered;
});

// Calendar - VERSIÓN MEJORADA CON ACTUALIZACIÓN DE DISPONIBILIDAD

const calendarPropertyId = ref(properties.value.length > 0 ? properties.value[0].id : null);
const currentDate = ref(new Date());
const selectionStart = ref(null);
const selectionEnd = ref(null);
const isSelecting = ref(false);
const hoverDate = ref(null);

// Datos computados
const currentYear = computed(() => currentDate.value.getFullYear());
const currentMonth = computed(() => currentDate.value.getMonth());
const currentMonthName = computed(() => {
  return new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(currentDate.value);
});
const currentMonthKey = computed(() => `${currentYear.value}-${currentMonth.value}`);
const weekDays = computed(() => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']);

// Días del calendario (ahora es un ref)
const calendarDays = ref([]);

const generateCalendarDays = () => {
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
    const date = new Date(currentYear.value, currentMonth.value, i);
    days.push({
      date: i,
      fullDate: date,
      available: Math.random() > 0.3, // Simulación
      booked: Math.random() < 0.2, // Simulación
      isToday: today.getDate() === i &&
               today.getMonth() === currentMonth.value &&
               today.getFullYear() === currentYear.value
    });
  }

  calendarDays.value = days;
};

// Inicializar al cargar
generateCalendarDays();

// Navegación entre meses
const nextMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
  generateCalendarDays();
  cancelSelection();
};

const previousMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
  generateCalendarDays();
  cancelSelection();
};

// Selección de días
const handleDayClick = (day) => {
  if (!day.date) return;

  if (!isSelecting.value) {
    selectionStart.value = new Date(currentYear.value, currentMonth.value, day.date);
    selectionEnd.value = new Date(currentYear.value, currentMonth.value, day.date);
    isSelecting.value = true;
  } else {
    const clickedDate = new Date(currentYear.value, currentMonth.value, day.date);

    if (clickedDate < selectionStart.value) {
      selectionEnd.value = selectionStart.value;
      selectionStart.value = clickedDate;
    } else {
      selectionEnd.value = clickedDate;
    }
  }
};

const handleDayHover = (day) => {
  if (!isSelecting.value || selectionEnd.value || !day.date) return;
  hoverDate.value = new Date(currentYear.value, currentMonth.value, day.date);
};

const isInSelection = (day) => {
  if (!day.date || !isSelecting.value || !selectionStart.value) return false;

  const dayDate = new Date(currentYear.value, currentMonth.value, day.date);
  const endDate = hoverDate.value || selectionEnd.value;

  return dayDate >= selectionStart.value && dayDate <= endDate;
};

const cancelSelection = () => {
  isSelecting.value = false;
  selectionStart.value = null;
  selectionEnd.value = null;
  hoverDate.value = null;
};

// Actualizar disponibilidad en el rango seleccionado
const applyBulkAction = (makeAvailable) => {
  if (!selectionStart.value || !selectionEnd.value) return;

  const start = selectionStart.value;
  const end = selectionEnd.value;

  calendarDays.value = calendarDays.value.map(day => {
    if (!day.fullDate) return day;

    const date = new Date(day.fullDate);
    if (date >= start && date <= end) {
      return {
        ...day,
        available: makeAvailable
      };
    }
    return day;
  });

  console.log(`Estableciendo disponibilidad a ${makeAvailable} desde ${start} hasta ${end}`);
  cancelSelection();
};

const formatDateRange = (start, end) => {
  if (!start || !end) return '';

  const formatDate = (date) => {
    return new Intl.DateTimeFormat('es-ES', {
      day: 'numeric',
      month: 'short'
    }).format(date);
  };

  return `${formatDate(start)} - ${formatDate(end)}`;
};

// Messages
const conversations = ref([
  {
    id: 1,
    name: 'Juan Pérez',
    propertyId: 1,
    bookingId: 'B5678',
    lastMessage: {
      text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
      time: 'Hace 2 horas'
    },
    messages: [
      {
        text: 'Hola, tengo una reserva para la próxima semana.',
        time: '10:30',
        sent: false
      },
      {
        text: 'Hola Juan, sí, veo tu reserva del 10 al 17 de agosto.',
        time: '10:35',
        sent: true
      },
      {
        text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
        time: '10:40',
        sent: false
      }
    ]
  },
  {
    id: 2,
    name: 'Ana Martínez',
    propertyId: 2,
    bookingId: 'B9012',
    lastMessage: {
      text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
      time: 'Hace 1 día'
    },
    messages: [
      {
        text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
        time: 'Ayer',
        sent: false
      }
    ]
  }
]);

const activeConversation = ref(null);

// Settings
const settingsTabs = [
  { id: 'profile', name: 'Perfil', icon: UserIcon },
  { id: 'notifications', name: 'Notificaciones', icon: BellIcon },
  { id: 'payment', name: 'Pagos', icon: CreditCardIcon }
];

const activeSettingsTab = ref('profile');

const settings = ref({
  profile: {
    name: 'Carlos',
    lastname: 'Rodríguez',
    email: 'carlos@example.com',
    phone: '+34 612 345 678'
  },
  notifications: {
    newBookingEmail: true,
    newMessageEmail: true,
    newBookingSMS: false
  },
  payment: {
    bankName: 'Banco Santander',
    accountHolder: 'Carlos Rodríguez',
    iban: 'ES91 2100 0418 4502 0005 1332'
  }
});

// Función para cambiar de tab
const changeTab = (tabId) => {
  activeTab.value = tabId;
  const tab = tabs.find(t => t.id === tabId);
  if (tab) {
    router.push(tab.path);
  }
};
</script>

<style scoped>
/* Global styles */
.property-management {
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

.logo {
  display: flex;
  flex-direction: column;
}

.logo h1 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
}

.logo-subtitle {
  font-size: 0.8rem;
  opacity: 0.8;
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
  text-decoration: none;
}

.nav-button:hover, .nav-button.active {
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
  padding: 2rem 0rem;
}

.section-title {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #003580;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

/* Dashboard styles */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 40px;
  height: 40px;
  color: #0071c2;
  margin-right: 1rem;
}

.stat-title {
  font-size: 0.9rem;
  color: #666;
  margin: 0 0 0.5rem;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #003580;
  margin: 0;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
}

.dashboard-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
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
  cursor: pointer;
  text-decoration: none;
}

.upcoming-bookings {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.booking-item {
  display: flex;
  justify-content: space-between;
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.booking-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.booking-property {
  display: flex;
  align-items: center;
}

.booking-image {
  width: 50px;
  height: 50px;
  border-radius: 4px;
  object-fit: cover;
  margin-right: 0.75rem;
}

.booking-property h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.booking-dates {
  font-size: 0.8rem;
  color: #666;
}

.booking-guest {
  display: flex;
  align-items: center;
}

.guest-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.guest-name {
  font-size: 0.9rem;
  font-weight: 500;
  margin: 0 0 0.25rem;
}

.guest-info {
  font-size: 0.8rem;
  color: #666;
  margin: 0;
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message-item {
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.message-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.message-sender {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.message-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.message-sender h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.message-property {
  font-size: 0.8rem;
  color: #666;
}

.message-preview {
  font-size: 0.9rem;
  margin: 0 0 0.5rem;
}

.message-time {
  font-size: 0.8rem;
  color: #666;
  text-align: right;
  margin: 0;
}

.empty-state {
  text-align: center;
  padding: 2rem 0;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #ccc;
  margin-bottom: 1rem;
}

/* Properties styles */
.add-button {
  display: flex;
  align-items: center;
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-button:hover {
  background-color: #005999;
}

.add-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.property-card {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.property-image-container {
  position: relative;
}

.property-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.property-status {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  background-color: white;
}

.property-status.active {
  color: #00703c;
}

.property-status.inactive {
  color: #e41c00;
}

.property-content {
  padding: 1.5rem;
}

.property-name {
  font-size: 1.2rem;
  margin: 0 0 0.5rem;
  color: #003580;
}

.property-location {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  color: #666;
}

.location-icon {
  width: 16px;
  height: 16px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.property-details {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.property-detail {
  display: flex;
  align-items: center;
  background-color: #f5f5f5;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.9rem;
}

.detail-icon {
  width: 14px;
  height: 14px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.property-actions {
  display: flex;
  border-top: 1px solid #eee;
}

.action-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s;
  font-weight: 500;
}

.action-button:hover {
  background-color: #f5f5f5;
}

.action-button.edit {
  color: #0071c2;
}

.action-button.calendar {
  color: #00703c;
  border-left: 1px solid #eee;
}

.action-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.empty-properties {
  text-align: center;
  padding: 3rem 0;
}

.empty-properties .empty-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1rem;
}

.empty-properties h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.empty-properties p {
  color: #666;
  margin-bottom: 1.5rem;
}

.add-property-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-property-button:hover {
  background-color: #005999;
}

/* Modal styles */
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
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.3s;
}

.close-button:hover {
  background-color: #f5f5f5;
}

.modal-body {
  padding: 1.5rem;
}

/* Form styles */
.property-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.form-group label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.form-input, .form-textarea {
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
}

.form-textarea {
  resize: vertical;
}

.photo-upload {
  display: flex;
  gap: 1rem;
}

.upload-placeholder {
  width: 100px;
  height: 100px;
  border: 2px dashed #ccc;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: border-color 0.3s;
}

.upload-placeholder:hover {
  border-color: #0071c2;
}

.upload-icon {
  width: 24px;
  height: 24px;
  color: #666;
  margin-bottom: 0.5rem;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.75rem;
}

.amenity-checkbox {
  display: flex;
  align-items: center;
}

.amenity-checkbox input {
  margin-right: 0.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-button {
  background-color: white;
  color: #666;
  border: 1px solid #ccc;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cancel-button:hover {
  background-color: #f5f5f5;
}

.submit-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #005999;
}

/* Bookings styles */
.bookings-filter {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.filter-tabs {
  display: flex;
  border: 1px solid #ccc;
  border-radius: 4px;
  overflow: hidden;
}

.filter-tab {
  background: none;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
  font-size: 0.9rem;
}

.filter-tab:not(:last-child) {
  border-right: 1px solid #ccc;
}

.filter-tab.active {
  background-color: #0071c2;
  color: white;
}

.filter-property {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.property-select {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.9rem;
}

.bookings-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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

.empty-bookings {
  text-align: center;
  padding: 3rem 0;
}

/* Calendar styles - VERSIÓN MEJORADA */
.calendar-section {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  padding: 24px;
  max-width: 800px;
  margin: 0 auto;
}

.property-selector {
  position: relative;
  width: 200px;
}

.property-select {
  width: 100%;
  padding: 10px 16px;
  border-radius: 8px;
  border: 1px solid #e0e7ee;
  background-color: #f8fafc;
  font-size: 0.9rem;
  color: #2b3a4a;
  appearance: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.property-select:hover {
  border-color: #0071c2;
}

.property-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.2);
}

.select-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #64748b;
  pointer-events: none;
}

.calendar-navigation {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.current-month {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2b3a4a;
  text-transform: capitalize;
  margin: 0;
}

.nav-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background-color: #f1f5f9;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
}

.nav-button:hover {
  background-color: #e2e8f0;
  color: #0071c2;
}

.calendar-container {
  margin-bottom: 24px;
}

.weekdays-header {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  margin-bottom: 8px;
}

.weekday {
  text-align: center;
  font-weight: 600;
  font-size: 0.85rem;
  color: #64748b;
  padding: 8px 0;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
}

.calendar-day {
  position: relative;
  height: 70px;
  border-radius: 8px;
  padding: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.calendar-day.empty {
  cursor: default;
}

.calendar-day:not(.empty):hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.day-number {
  font-weight: 600;
  font-size: 0.9rem;
  margin-bottom: 4px;
}

.day-status {
  font-size: 0.7rem;
  text-align: center;
}

.calendar-day.available {
  background-color: #e6f7ee;
  color: #0f766e;
}

.calendar-day.unavailable {
  background-color: #fef2f2;
  color: #b91c1c;
}

.calendar-day.booked {
  background-color: #eff6ff;
  color: #1e40af;
}

.calendar-day.today {
  border: 2px solid #0071c2;
}

.calendar-day.in-selection {
  background-color: #0071c2;
  color: white;
}

.calendar-day.in-selection .status {
  color: rgba(255, 255, 255, 0.9);
}

.calendar-legend {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
  justify-content: center;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  color: #475569;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.legend-color.available {
  background-color: #e6f7ee;
  border: 1px solid #0f766e;
}

.legend-color.unavailable {
  background-color: #fef2f2;
  border: 1px solid #b91c1c;
}

.legend-color.booked {
  background-color: #eff6ff;
  border: 1px solid #1e40af;
}

.bulk-actions {
  background-color: #f8fafc;
  border-radius: 8px;
  padding: 16px;
}

.bulk-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.bulk-header h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #2b3a4a;
  margin: 0;
}

.cancel-selection {
  display: flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  color: #ef4444;
  font-size: 0.85rem;
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.cancel-selection:hover {
  background-color: #fee2e2;
}

.selection-prompt {
  text-align: center;
  color: #64748b;
  font-size: 0.9rem;
  padding: 16px 0;
}

.selection-info {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.date-range-display {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  background-color: white;
  border-radius: 6px;
  border: 1px solid #e0e7ee;
  color: #2b3a4a;
  font-weight: 500;
}

.action-buttons {
  display: flex;
  gap: 12px;
}

.action-button {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  border-radius: 6px;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-button.available {
  background-color: #0071c2;
  color: white;
}

.action-button.available:hover {
  background-color: #005a9c;
}

.action-button.unavailable {
  background-color: #f1f5f9;
  color: #475569;
}

.action-button.unavailable:hover {
  background-color: #e2e8f0;
}

.icon {
  width: 16px;
  height: 16px;
}

/* Animaciones */
.calendar-fade-enter-active,
.calendar-fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.calendar-fade-enter-from,
.calendar-fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.status-fade-enter-active,
.status-fade-leave-active {
  transition: opacity 0.2s ease;
}

.status-fade-enter-from,
.status-fade-leave-to {
  opacity: 0;
}

/* Messages styles */
.messages-container {
  display: flex;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  height: 600px;
}

.messages-sidebar {
  width: 300px;
  border-right: 1px solid #eee;
  display: flex;
  flex-direction: column;
}

.messages-search {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1.5rem;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #666;
}

.search-input {
  width: 100%;
  padding: 0.5rem 0.5rem 0.5rem 2rem;
  border: 1px solid #ccc;
  border-radius: 20px;
  font-size: 0.9rem;
}

.conversation-list {
  flex: 1;
  overflow-y: auto;
}

.conversation-item {
  display: flex;
  padding: 1rem;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.3s;
}

.conversation-item:hover, .conversation-item.active {
  background-color: #f5f5f5;
}

.conversation-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
}

.avatar-icon {
  width: 20px;
  height: 20px;
  color: #0071c2;
}

.conversation-info {
  flex: 1;
  min-width: 0;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.25rem;
}

.conversation-header h4 {
  font-size: 1rem;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conversation-time {
  font-size: 0.8rem;
  color: #666;
}

.conversation-preview {
  font-size: 0.9rem;
  margin: 0 0 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #666;
}

.conversation-property {
  font-size: 0.8rem;
  color: #0071c2;
}

.messages-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.conversation-view {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.conversation-header {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.conversation-user {
  display: flex;
  align-items: center;
}

.user-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-right: 0.75rem;
}

.conversation-user h3 {
  font-size: 1.1rem;
  margin: 0 0 0.25rem;
}

.conversation-booking {
  font-size: 0.8rem;
  color: #666;
  margin: 0;
}

.conversation-property {
  font-size: 0.9rem;
  color: #0071c2;
}

.message-list {
  flex: 1;
  padding: 1rem;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message-bubble {
  max-width: 70%;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  position: relative;
}

.message-sent {
  align-self: flex-end;
  background-color: #e6f0ff;
  color: #0071c2;
}

.message-received {
  align-self: flex-start;
  background-color: #f5f5f5;
  color: #333;
}

.message-content {
  margin-bottom: 0.5rem;
}

.message-time {
  font-size: 0.8rem;
  color: #666;
  text-align: right;
}

.message-input {
  padding: 1rem;
  border-top: 1px solid #eee;
  display: flex;
  gap: 1rem;
}

.input-textarea {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.9rem;
  font-family: inherit;
  resize: none;
}

.send-button {
  display: flex;
  align-items: center;
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.send-button:hover {
  background-color: #005999;
}

.send-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.empty-conversation {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}

/* Settings styles */
.settings-container {
  display: flex;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.settings-sidebar {
  width: 250px;
  border-right: 1px solid #eee;
  padding: 1.5rem 0;
}

.settings-tab {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.settings-tab:hover, .settings-tab.active {
  background-color: #f5f5f5;
}

.settings-tab.active {
  border-left: 3px solid #0071c2;
}

.settings-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.75rem;
}

.settings-content {
  flex: 1;
  padding: 1.5rem;
}

.settings-panel {
  max-width: 600px;
}

.panel-title {
  font-size: 1.2rem;
  color: #003580;
  margin: 0 0 1.5rem;
}

.profile-avatar {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
}

.avatar-placeholder {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1.5rem;
}

.change-avatar-button {
  background-color: #f5f5f5;
  color: #333;
  border: 1px solid #ccc;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.change-avatar-button:hover {
  background-color: #eee;
}

.notification-settings {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.notification-group {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.notification-group h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 0.5rem;
}

.notification-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-option h5 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.notification-option p {
  font-size: 0.9rem;
  color: #666;
  margin: 0;
}

.toggle {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

.toggle input:checked + .toggle-slider {
  background-color: #0071c2;
}

.toggle input:checked + .toggle-slider:before {
  transform: translateX(26px);
}

.payment-methods {
  margin-bottom: 2rem;
}

.payment-methods h4, .bank-account h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.payment-method {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border: 1px solid #eee;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.payment-info {
  display: flex;
  align-items: center;
}

.payment-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-right: 1rem;
}

.payment-info h5 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.payment-info p {
  font-size: 0.9rem;
  color: #666;
  margin: 0;
}

.payment-actions {
  display: flex;
  gap: 0.5rem;
}

.edit-button, .delete-button {
  background: none;
  border: none;
  font-size: 0.9rem;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.edit-button {
  color: #0071c2;
}

.edit-button:hover {
  background-color: #e6f0ff;
}

.delete-button {
  color: #e41c00;
}

.delete-button:hover {
  background-color: #fff2f0;
}

.add-payment-button {
  display: flex;
  align-items: center;
  background: none;
  border: 1px dashed #ccc;
  width: 100%;
  padding: 0.75rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: border-color 0.3s;
  justify-content: center;
}

.add-payment-button:hover {
  border-color: #0071c2;
}

.add-payment-button .add-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
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
  
  .bookings-filter {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .filter-tabs {
    width: 100%;
    overflow-x: auto;
  }
  
  .messages-container {
    flex-direction: column;
    height: auto;
  }
  
  .messages-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid #eee;
  }
  
  .settings-container {
    flex-direction: column;
  }
  
  .settings-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid #eee;
  }
}

@media (max-width: 576px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .properties-grid {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
  
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
  
  /* Responsive calendar */
  .calendar-section {
    padding: 16px;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .property-selector {
    width: 100%;
  }
  
  .calendar-day {
    height: 60px;
    padding: 4px;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>
