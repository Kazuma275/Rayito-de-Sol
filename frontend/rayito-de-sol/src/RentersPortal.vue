<template>
  <div class="renters-portal">
    <!-- Header -->
    <RentersHeader :user="user" :activeTab="activeTab" @changeTab="changeTab" />

    <!-- Main Content -->
    <main class="main-content">
      <!-- Dashboard -->
      <!-- Dashboard para Inquilino -->
  <section v-if="activeTab === 'dashboard'" class="dashboard-section renters-dashboard">
    <h2 class="section-title">Panel de Control</h2>
    
    <!-- Stats Grid -->
    <div class="stats-grid renters-stats">
      <div class="stat-card renters-stat-card">
        <CalendarIcon class="stat-icon renters-stat-icon" />
        <div class="stat-content">
          <h3 class="stat-title">Reservas Activas</h3>
          <p class="stat-value">{{ activeBookings.length }}</p>
        </div>
      </div>
      
      <div class="stat-card renters-stat-card">
        <ClockIcon class="stat-icon renters-stat-icon" />
        <div class="stat-content">
          <h3 class="stat-title">Próxima Reserva</h3>
          <p class="stat-value">{{ nextBookingDate }}</p>
        </div>
      </div>
      
      <div class="stat-card renters-stat-card">
        <MessageSquareIcon class="stat-icon renters-stat-icon" />
        <div class="stat-content">
          <h3 class="stat-title">Mensajes Nuevos</h3>
          <p class="stat-value">{{ unreadMessages }}</p>
        </div>
      </div>
      
      <div class="stat-card renters-stat-card">
        <HeartIcon class="stat-icon renters-stat-icon" />
        <div class="stat-content">
          <h3 class="stat-title">Favoritos</h3>
          <p class="stat-value">{{ favorites.length }}</p>
        </div>
      </div>
    </div>
    
    <div class="dashboard-grid renters-dashboard-grid">
      <!-- Mis Reservas -->
      <div class="dashboard-card renters-dashboard-card">
        <div class="card-header">
          <h3>Mis Reservas</h3>
          <button class="view-all-button" @click="changeTab('bookings')">Ver todas</button>
        </div>
        <div v-if="activeBookings.length > 0" class="upcoming-bookings">
          <div v-for="booking in activeBookings.slice(0, 3)" :key="booking.id" class="booking-item renters-booking-item">
            <div class="booking-property">
              <img :src="booking.property.image" alt="Property" class="booking-image renters-booking-image" />
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
        <div v-else class="empty-state renters-empty-state">
          <CalendarOffIcon class="empty-icon renters-empty-icon" />
          <p>No tienes reservas activas</p>
          <button class="action-button search" @click="changeTab('search')">Buscar Alojamiento</button>
        </div>
      </div>
      
      <!-- Mensajes Recientes -->
      <div class="dashboard-card renters-dashboard-card">
        <div class="card-header">
          <h3>Mensajes Recientes</h3>
          <button class="view-all-button" @click="changeTab('messages')">Ver todos</button>
        </div>
        <div v-if="recentMessages.length > 0" class="messages-list">
          <div v-for="message in recentMessages" :key="message.id" class="message-item renters-message-item">
            <div class="message-sender">
              <UserIcon class="message-icon renters-message-icon" />
              <div>
                <h4>{{ message.sender }}</h4>
                <p class="message-property">{{ message.property.name }}</p>
              </div>
            </div>
            <p class="message-preview">{{ message.text.substring(0, 60) }}{{ message.text.length > 60 ? '...' : '' }}</p>
            <p class="message-time">{{ formatDateTime(message.createdAt) }}</p>
          </div>
        </div>
        <div v-else class="empty-state renters-empty-state">
          <MailIcon class="empty-icon renters-empty-icon" />
          <p>No hay mensajes nuevos</p>
        </div>
      </div>
    </div>
    
    <!-- Recomendados -->
    <div class="recommended-section renters-recommended-section">
      <h3 class="section-subtitle">Recomendados para ti</h3>
      <div class="properties-grid renters-properties-grid">
        <div v-for="property in recommendedProperties" :key="property.id" class="property-card renters-property-card">
          <div class="property-image-container">
            <img :src="property.image" alt="Property" class="property-image renters-property-image" />
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
            <div class="property-details">
              <div class="property-detail">
                <BedIcon class="detail-icon" />
                <span>{{ property.bedrooms }} dormitorios</span>
              </div>
              <div class="property-detail">
                <UsersIcon class="detail-icon" />
                <span>{{ property.capacity }} huéspedes</span>
              </div>
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
  </section>

      <!-- Property Detail -->
      <section v-if="activeTab === 'property-detail'" class="property-detail-section">
        <button class="back-button" @click="goBack">
          <ArrowLeftIcon class="back-icon" />
          Volver
        </button>
        
        <div v-if="selectedProperty" class="property-detail">
          <div class="property-gallery">
            <img :src="selectedProperty.image" alt="Property" class="main-image" />
            <div class="gallery-thumbnails">
              <img :src="selectedProperty.image" alt="Property" class="thumbnail active" />
              <div v-for="n in 3" :key="`thumb-${n}`" class="thumbnail placeholder"></div>
            </div>
          </div>
          
          <div class="property-info">
            <div class="property-header">
              <div>
                <h2 class="property-title">{{ selectedProperty.name }}</h2>
                <div class="property-location">
                  <MapPinIcon class="location-icon" />
                  <span>{{ selectedProperty.location }}</span>
                </div>
              </div>
              <button 
                class="favorite-button large" 
                :class="{ active: isFavorite(selectedProperty.id) }"
                @click="toggleFavorite(selectedProperty.id)"
              >
                <HeartIcon class="favorite-icon" />
                <span>{{ isFavorite(selectedProperty.id) ? 'Guardado' : 'Guardar' }}</span>
              </button>
            </div>
            
            <div class="property-highlights">
              <div class="highlight-item">
                <UsersIcon class="highlight-icon" />
                <div>
                  <span class="highlight-value">{{ selectedProperty.capacity }}</span>
                  <span class="highlight-label">huéspedes</span>
                </div>
              </div>
              <div class="highlight-item">
                <BedIcon class="highlight-icon" />
                <div>
                  <span class="highlight-value">{{ selectedProperty.bedrooms }}</span>
                  <span class="highlight-label">dormitorios</span>
                </div>
              </div>
              <div class="highlight-item">
                <HomeIcon class="highlight-icon" />
                <div>
                  <span class="highlight-value">{{ selectedProperty.size }}</span>
                  <span class="highlight-label">m²</span>
                </div>
              </div>
            </div>
            
            <div class="property-description">
              <h3>Descripción</h3>
              <p>{{ selectedProperty.description }}</p>
            </div>
            
            <div class="property-amenities-section">
              <h3>Servicios</h3>
              <div class="amenities-grid">
                <div v-for="amenityId in selectedProperty.amenities" :key="amenityId" class="amenity-item">
                  <CheckIcon class="amenity-icon" />
                  <span>{{ getAmenityName(amenityId) }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="booking-card">
            <div class="booking-price">
              <span class="price-value">€{{ selectedProperty.price }}</span>
              <span class="price-period">noche</span>
            </div>
            
            <div class="booking-form">
              <div class="booking-dates">
                <div class="date-field">
                  <label>Llegada</label>
                  <input 
                    type="date" 
                    v-model="bookingData.checkIn" 
                    class="date-input"
                    :min="today"
                  />
                </div>
                <div class="date-field">
                  <label>Salida</label>
                  <input 
                    type="date" 
                    v-model="bookingData.checkOut" 
                    class="date-input"
                    :min="bookingData.checkIn || today"
                  />
                </div>
              </div>
              
              <div class="guests-field">
                <label>Huéspedes</label>
                <div class="guests-selector">
                  <button 
                    class="guest-button" 
                    @click="bookingData.guests > 1 && bookingData.guests--"
                    :disabled="bookingData.guests <= 1"
                  >
                    <MinusIcon class="guest-icon" />
                  </button>
                  <span class="guests-count">{{ bookingData.guests }}</span>
                  <button 
                    class="guest-button" 
                    @click="bookingData.guests < selectedProperty.capacity && bookingData.guests++"
                    :disabled="bookingData.guests >= selectedProperty.capacity"
                  >
                    <PlusIcon class="guest-icon" />
                  </button>
                </div>
              </div>
              
              <div v-if="bookingData.checkIn && bookingData.checkOut" class="booking-summary">
                <div class="summary-item">
                  <span>€{{ selectedProperty.price }} x {{ calculateNights() }} noches</span>
                  <span>€{{ calculateSubtotal() }}</span>
                </div>
                <div class="summary-item">
                  <span>Tarifa de limpieza</span>
                  <span>€{{ cleaningFee }}</span>
                </div>
                <div class="summary-item">
                  <span>Tarifa de servicio</span>
                  <span>€{{ calculateServiceFee() }}</span>
                </div>
                <div class="summary-total">
                  <span>Total</span>
                  <span>€{{ calculateTotal() }}</span>
                </div>
              </div>
              
              <button 
                class="book-button" 
                @click="bookProperty"
                :disabled="!canBook"
              >
                Reservar
              </button>
              
              <button class="contact-button" @click="contactOwner">
                <MessageSquareIcon class="contact-icon" />
                Contactar con el propietario
              </button>
            </div>
          </div>
        </div>
      </section>

    <section v-if="activeTab === 'bookings'" class="bookings-section renters-bookings-section">
  <transition name="fade">
    <div v-if="isLoading" class="loading-solar-container">
      <div class="solar-spinner"></div>
      <div class="loading-text">Cargando reservas y propiedades…</div>
    </div>
    <div v-else>
      <div class="section-header">
        <h2 class="section-title">Mis Reservas</h2>
        <div class="header-actions">
          <button class="yellow-button export-button">
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
          </button>
        </div>
      </div>

      <div v-if="filteredBookings.length > 0" class="bookings-list">
        <div
          v-for="booking in filteredBookings"
          :key="booking.id"
          class="booking-card renters-booking-card"
        >
          <div class="booking-header">
            <div class="booking-property-info">
              <img
                v-if="booking.property.image"
                :src="booking.property.image"
                alt="Propiedad"
                class="booking-property-image"
              />
              <div>
                <h3>{{ booking.property.name }}</h3>
                <div class="booking-id">Reserva #{{ booking.id }}</div>
              </div>
            </div>
            <div class="booking-status" :class="booking.status">
              {{ getStatusText(booking.status) }}
            </div>
          </div>
          <div class="booking-details">
            <div class="booking-dates">
              <div class="date-item">
                <CalendarIcon class="date-icon" />
                <div>
                  <span class="date-label">Llegada</span>
                  <span class="date-value">{{ formatDate(booking.checkIn) }}</span>
                </div>
              </div>
              <div class="date-item">
                <CalendarIcon class="date-icon" />
                <div>
                  <span class="date-label">Salida</span>
                  <span class="date-value">{{ formatDate(booking.checkOut) }}</span>
                </div>
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
            <button class="yellow-button" @click="viewBookingDetails(booking.id)">Ver detalles</button>
            <button
              v-if="booking.status === 'confirmed' && canCancel(booking)"
              class="yellow-button"
              @click="cancelBooking(booking.id)"
            >
              Cancelar reserva
            </button>
            <button
              v-if="booking.status === 'confirmed'"
              class="yellow-button"
              @click="contactOwnerForBooking(booking.id)"
            >
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
        <p v-else>Cuando hagas reservas, aparecerán aquí</p>

        <div v-if="availableProperties.length > 0" class="available-properties-list">
          <h4>Propiedades disponibles para reservar</h4>
          <div class="properties-grid renters-properties-grid">
            <div v-for="property in availableProperties" :key="property.id" class="property-card renters-property-card">
              <div class="property-image-container">
                <img
                  v-if="property.image"
                  :src="property.image"
                  alt="Propiedad"
                  class="property-image"
                />
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
                </div>
                <div class="property-price">
                  <span class="price-value">€{{ property.price }}</span>
                  <span class="price-period">noche</span>
                </div>
                <button class="yellow-button view-property-button" @click="viewProperty(property.id)">Ver Detalles</button>
              </div>
            </div>
          </div>
        </div>
        <div v-else>
          <p>No hay propiedades disponibles para reservar en este momento.</p>
        </div>
        <button v-if="activeBookingFilter === 'all'" class="yellow-button search" @click="changeTab('search')">
          Buscar Alojamiento
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
              <img
                v-if="selectedBooking.property.image"
                :src="selectedBooking.property.image"
                alt="Propiedad"
                class="property-image"
              />
              <div class="property-info">
                <h4>{{ selectedBooking.property.name }}</h4>
                <p class="property-location">
                  <MapPinIcon class="info-icon" />
                  {{ selectedBooking.property.location }}
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
                <h5>Detalles</h5>
                <div class="info-content">
                  <UsersIcon class="info-icon" />
                  <div>
                    <p><strong>Huéspedes:</strong> {{ selectedBooking.guests }}</p>
                    <p><strong>Estado:</strong>
                      <span :class="['status-badge', selectedBooking.status]">{{ getStatusText(selectedBooking.status) }}</span>
                    </p>
                    <p><strong>Fecha de reserva:</strong> {{ formatDate(selectedBooking.bookingDate) }}</p>
                  </div>
                </div>
              </div>
              <div class="booking-info-item">
                <h5>Pago</h5>
                <div class="info-content">
                  <EuroIcon class="info-icon" />
                  <div>
                    <p><strong>Total:</strong> €{{ selectedBooking.total }}</p>
                    <p><strong>Método de pago:</strong> {{ selectedBooking.paymentMethod || 'No especificado' }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="booking-notes" v-if="selectedBooking.notes">
              <h5>Notas</h5>
              <p>{{ selectedBooking.notes }}</p>
            </div>
            <div class="booking-timeline" v-if="selectedBooking.history && selectedBooking.history.length">
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
              <button v-if="selectedBooking.status === 'confirmed' && canCancel(selectedBooking)" class="yellow-button" @click="cancelBooking(selectedBooking.id)">
                <XIcon class="action-icon" />
                Cancelar Reserva
              </button>
              <button class="yellow-button" @click="contactOwnerForBooking(selectedBooking.id)">
                <MessageSquareIcon class="action-icon" />
                Mensaje
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
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
                  <div class="conversation-property">{{ conversation.property.name }}</div>
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
                    <p class="conversation-property">{{ conversations[activeConversation].property.name }}</p>
                  </div>
                </div>
                <div v-if="conversations[activeConversation].bookingId" class="conversation-booking">
                  Reserva #{{ conversations[activeConversation].bookingId }}
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
      
      <!-- Profile -->
      <section v-if="activeTab === 'profile'" class="profile-section">
        <h2 class="section-title">Mi Perfil</h2>
        
        <div class="profile-container">
          <div class="profile-sidebar">
            <div v-for="(tab, index) in profileTabs" :key="index" 
                 class="profile-tab" 
                 :class="{ active: activeProfileTab === tab.id }"
                 @click="activeProfileTab = tab.id">
              <component :is="tab.icon" class="profile-icon" />
              <span>{{ tab.name }}</span>
            </div>
          </div>
          
          <div class="profile-content">
            <!-- Personal Information -->
            <div v-if="activeProfileTab === 'personal'" class="profile-panel">
              <h3 class="panel-title">Información Personal</h3>
              <form class="profile-form" @submit.prevent="saveProfile">
                <div class="profile-avatar">
                  <div class="avatar-placeholder">
                    <img v-if="previewImage" :src="previewImage" class="avatar-preview" />
                    <UserIcon v-else class="avatar-icon" />
                  </div>
                  <button type="button" class="change-avatar-button" @click="triggerFileInput">Cambiar foto</button>
                  <input
                    type="file"
                    ref="avatarInput"
                    accept="image/*"
                    @change="handleAvatarChange"
                    style="display: none"
                  />
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="profile-name">Nombre</label>
                    <input id="profile-name" type="text" v-model="profileData.name" class="form-input" />
                  </div>
                  
                  <div class="form-group">
                    <label for="profile-lastname">Apellidos</label>
                    <input id="profile-lastname" type="text" v-model="profileData.lastname" class="form-input" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="profile-email">Email</label>
                  <input id="profile-email" type="email" v-model="profileData.email" class="form-input" />
                </div>
                
                <div class="form-group">
                  <label for="profile-phone">Teléfono</label>
                  <input id="profile-phone" type="tel" v-model="profileData.phone" class="form-input" />
                </div>
                
                <div class="form-actions">
                  <button type="submit" class="save-button">Guardar cambios</button>
                </div>
              </form>
            </div>
            
            <!-- Payment Methods -->
            <div v-if="activeProfileTab === 'payment'" class="profile-panel">
              <h3 class="panel-title">Métodos de Pago</h3>
              
              <div class="payment-methods">
                <div v-if="paymentMethods.length > 0">
                  <div v-for="(payment, index) in paymentMethods" :key="index" class="payment-method">
                    <div class="payment-info">
                      <CreditCardIcon class="payment-icon" />
                      <div>
                        <h5>{{ payment.cardType }} terminada en {{ payment.last4 }}</h5>
                        <p>Expira {{ payment.expiryDate }}</p>
                      </div>
                    </div>
                    <div class="payment-actions">
                      <button class="edit-button" @click="editPayment(index)">Editar</button>
                      <button class="delete-button" @click="deletePayment(index)">Eliminar</button>
                    </div>
                  </div>
                </div>
                
                <div v-else class="empty-payment-methods">
                  <CreditCardIcon class="empty-icon" />
                  <p>No tienes métodos de pago guardados</p>
                </div>
                
                <button class="add-payment-button" @click="addPaymentMethod">
                  <PlusIcon class="add-icon" />
                  Añadir método de pago
                </button>
              </div>
            </div>
            
            <!-- Notifications -->
            <div v-if="activeProfileTab === 'notifications'" class="profile-panel">
              <h3 class="panel-title">Notificaciones</h3>
              
              <div class="notification-settings">
                <div class="notification-group">
                  <h4>Email</h4>
                  
                  <div class="notification-option">
                    <div>
                      <h5>Confirmación de reservas</h5>
                      <p>Recibe un email cuando tu reserva sea confirmada</p>
                    </div>
                    <label class="toggle">
                      <input type="checkbox" v-model="notificationSettings.bookingConfirmationEmail" />
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                  
                  <div class="notification-option">
                    <div>
                      <h5>Mensajes</h5>
                      <p>Recibe un email cuando recibas un nuevo mensaje</p>
                    </div>
                    <label class="toggle">
                      <input type="checkbox" v-model="notificationSettings.newMessageEmail" />
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                  
                  <div class="notification-option">
                    <div>
                      <h5>Ofertas especiales</h5>
                      <p>Recibe emails con ofertas y descuentos</p>
                    </div>
                    <label class="toggle">
                      <input type="checkbox" v-model="notificationSettings.specialOffersEmail" />
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                
                <div class="notification-group">
                  <h4>SMS</h4>
                  
                  <div class="notification-option">
                    <div>
                      <h5>Confirmación de reservas</h5>
                      <p>Recibe un SMS cuando tu reserva sea confirmada</p>
                    </div>
                    <label class="toggle">
                      <input type="checkbox" v-model="notificationSettings.bookingConfirmationSMS" />
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                
                <div class="form-actions">
                  <button class="save-button" @click="saveNotificationSettings">Guardar preferencias</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    
    <RentersFooter @changeTab="changeTab" />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import RentersHeader from './components/layout/RentersHeader.vue';
import RentersFooter from './components/layout/RentersFooter.vue';
import { 
  UserIcon, 
  CalendarIcon, 
  UsersIcon, 
  MapPinIcon, 
  HomeIcon, 
  BedIcon, 
  WifiIcon,
  ClockIcon,
  HeartIcon,
  HeartOffIcon,
  MessageSquareIcon,
  MailIcon,
  SearchIcon,
  SearchXIcon,
  EuroIcon,
  PlusIcon,
  MinusIcon,
  XIcon,
  CheckIcon,
  SlidersIcon,
  ArrowLeftIcon,
  SendIcon,
  CreditCardIcon,
  BellIcon,
  CalendarOffIcon
} from 'lucide-vue-next';

// Router
const router = useRouter();
const route = useRoute();

// User data
const user = ref({
  id: 1,
  name: 'Ana',
  lastname: 'García',
  email: 'ana.garcia@example.com',
  phone: '+34 612 345 678',
  avatar: null
});

// Tabs
const tabs = [
  { id: 'dashboard', name: 'Inicio', path: '/renters/dashboard' },
  { id: 'search', name: 'Buscar', path: '/renters/search' },
  { id: 'bookings', name: 'Reservas', path: '/renters/bookings' },
  { id: 'favorites', name: 'Favoritos', path: '/renters/favorites' },
  { id: 'messages', name: 'Mensajes', path: '/renters/messages' },
  { id: 'profile', name: 'Perfil', path: '/renters/profile' }
];

// Active tab state
const activeTab = ref('dashboard');

const viewProperty = (id) => {
  const property = properties.value.find(p => p.id === id);
  if (property) {
    selectedProperty.value = property;
    activeTab.value = 'property-detail';
    router.push(`/renters/property/${id}`);
  }
};

const availableProperties = computed(() => {
  const reservedIds = bookings.value.map(b => b.propertyId);
  return properties.value.filter(p => !reservedIds.includes(p.id));
});

// Initialize on mount
onMounted(async () => {
  updateActiveTabFromRoute();
  isLoading.value = true;
  await fetchProperties();
  await fetchBookings();
  isLoading.value = false;
});

// Change tab function
const changeTab = (tabId) => {
  activeTab.value = tabId;
  const tab = tabs.find(t => t.id === tabId);
  if (tab) {
    router.push(tab.path);
  }
};

// Properties data
const properties = ref([]);
const isLoading = ref(true);

// Update active tab based on route
const updateActiveTabFromRoute = () => {
  const path = route.path;
  
  if (path.includes('/renters/dashboard')) {
    activeTab.value = 'dashboard';
  } else if (path.includes('/renters/search')) {
    activeTab.value = 'search';
  } else if (path.includes('/renters/bookings')) {
    activeTab.value = 'bookings';
  } else if (path.includes('/renters/favorites')) {
    activeTab.value = 'favorites';
  } else if (path.includes('/renters/messages')) {
    activeTab.value = 'messages';
  } else if (path.includes('/renters/profile')) {
    activeTab.value = 'profile';
  } else if (path.includes('/renters/property/')) {
    activeTab.value = 'property-detail';
    const propertyId = parseInt(path.split('/').pop());
    if (propertyId) {
      viewProperty(propertyId);
    }
  }
};

// Mock properties data
import axios from 'axios';

const fetchProperties = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('http://localhost:8000/api/properties');
    properties.value = response.data.map(prop => {
      // Imagen: base64 o placeholder si es null
      let image = prop.image;
      if (!image) {
        image = '/placeholder.svg?height=300&width=500';
      }

      // Amenities: asegúrate de que siempre sea array
      let amenities = [];
      if (Array.isArray(prop.amenities)) {
        amenities = prop.amenities;
      } else if (typeof prop.amenities === "string" && prop.amenities.length > 0) {
        // Si tu backend envía amenities como string tipo JSON
        try {
          amenities = JSON.parse(prop.amenities);
        } catch (e) {
          // Si solo es texto separado por comas
          amenities = prop.amenities.split(',').map(a => a.trim());
        }
      }

      // Precio: asegúrate que siempre sea número
      let price = Number(prop.price);
      if (isNaN(price)) price = 0;

      return {
        id: prop.id,
        name: prop.name || 'Sin nombre',
        location: prop.location || '',
        bedrooms: Number(prop.bedrooms) || 0,
        capacity: Number(prop.capacity) || 0,
        price,
        image,
        description: prop.description || '',
        amenities,
        status: prop.status || '',
        statusText: prop.statusText || '',
        created_at: prop.created_at,
        updated_at: prop.updated_at,
      };
    });
  } catch (error) {
    console.error('Error cargando propiedades:', error);
    properties.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Amenities
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

const getAmenityName = (id) => {
  const amenity = amenities.find(a => a.id === id);
  return amenity ? amenity.name : '';
};

// Search functionality
const searchQuery = ref('');
const showAdvancedFilters = ref(false);
const filters = ref({
  checkIn: '',
  checkOut: '',
  guests: 2,
  maxPrice: 200,
  bedrooms: [],
  amenities: []
});

const today = computed(() => {
  const date = new Date();
  return date.toISOString().split('T')[0];
});

const toggleAdvancedFilters = () => {
  showAdvancedFilters.value = !showAdvancedFilters.value;
};

const clearFilters = () => {
  filters.value = {
    checkIn: '',
    checkOut: '',
    guests: 2,
    maxPrice: 200,
    bedrooms: [],
    amenities: []
  };
  searchQuery.value = '';
};

const searchProperties = () => {
  isLoading.value = true;
  
  // Simulate API call
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
};

const applyFilters = () => {
  searchProperties();
  showAdvancedFilters.value = false;
};

const filteredProperties = computed(() => {
  let result = [...properties.value];
  
  // Apply search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(property => 
      property.name.toLowerCase().includes(query) || 
      property.location.toLowerCase().includes(query)
    );
  }
  
  // Apply price filter
  if (filters.value.maxPrice) {
    result = result.filter(property => property.price <= filters.value.maxPrice);
  }
  
  // Apply guests filter
  if (filters.value.guests) {
    result = result.filter(property => property.capacity >= filters.value.guests);
  }
  
  // Apply bedrooms filter
  if (filters.value.bedrooms.length > 0) {
    result = result.filter(property => {
      // If 5+ is selected, include properties with 5 or more bedrooms
      if (filters.value.bedrooms.includes(5) && property.bedrooms >= 5) {
        return true;
      }
      return filters.value.bedrooms.includes(property.bedrooms);
    });
  }
  
  // Apply amenities filter
  if (filters.value.amenities.length > 0) {
    result = result.filter(property => 
      filters.value.amenities.every(amenity => property.amenities.includes(amenity))
    );
  }
  
  return result;
});

// Property detail
const selectedProperty = ref(null);
const bookingData = ref({
  checkIn: '',
  checkOut: '',
  guests: 2
});

const goBack = () => {
  router.back();
};

// Booking functionality
const cleaningFee = 30;
const serviceFeePercentage = 0.10;

const calculateNights = () => {
  if (!bookingData.value.checkIn || !bookingData.value.checkOut) return 0;
  
  const checkIn = new Date(bookingData.value.checkIn);
  const checkOut = new Date(bookingData.value.checkOut);
  const diffTime = checkOut.getTime() - checkIn.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays > 0 ? diffDays : 0;
};

const calculateSubtotal = () => {
  const nights = calculateNights();
  return nights * selectedProperty.value.price;
};

const calculateServiceFee = () => {
  const subtotal = calculateSubtotal();
  return Math.round(subtotal * serviceFeePercentage);
};

const calculateTotal = () => {
  const subtotal = calculateSubtotal();
  const serviceFee = calculateServiceFee();
  return subtotal + cleaningFee + serviceFee;
};

const canBook = computed(() => {
  return bookingData.value.checkIn && 
         bookingData.value.checkOut && 
         calculateNights() > 0 && 
         bookingData.value.guests > 0 && 
         bookingData.value.guests <= selectedProperty.value?.capacity;
});

const bookProperty = () => {
  if (!canBook.value) return;
  
  // Create new booking
  const newBooking = {
    id: `B${Date.now().toString().substring(9)}`,
    propertyId: selectedProperty.value.id,
    property: selectedProperty.value,
    checkIn: bookingData.value.checkIn,
    checkOut: bookingData.value.checkOut,
    guests: bookingData.value.guests,
    total: calculateTotal(),
    status: 'pending',
    createdAt: new Date().toISOString()
  };
  
  // Add to bookings
  bookings.value.push(newBooking);
  
  // Navigate to bookings
  alert('¡Reserva realizada con éxito! Pendiente de confirmación por el propietario.');
  changeTab('bookings');
};

const contactOwner = () => {
  // Create new conversation if it doesn't exist
  const existingConversation = conversations.value.findIndex(c => 
    c.property.id === selectedProperty.value.id
  );
  
  if (existingConversation === -1) {
    // Create new conversation
    conversations.value.push({
      id: Date.now(),
      name: `Propietario de ${selectedProperty.value.name}`,
      property: selectedProperty.value,
      bookingId: null,
      lastMessage: {
        text: '',
        time: 'Ahora'
      },
      messages: []
    });
    
    // Set as active conversation
    activeConversation.value = conversations.value.length - 1;
  } else {
    // Set existing conversation as active
    activeConversation.value = existingConversation;
  }
  
  // Navigate to messages
  changeTab('messages');
};

// Bookings
const bookings = ref([]);
const activeBookingFilter = ref('all');

const fetchBookings = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('http://localhost:8000/api/bookings');
    bookings.value = response.data.map(booking => ({
      ...booking,
      property: properties.value.find(p => p.id === booking.propertyId) || {
        name: 'Propiedad no encontrada',
        image: '/placeholder.svg?height=100&width=100',
        price: 0,
        bedrooms: 0,
        capacity: 0,
        location: '',
        description: '',
        amenities: []
      }
    }));
  } catch (error) {
    console.error('Error al cargar reservas:', error);
    bookings.value = [];
  } finally {
    isLoading.value = false;
  }
};

const bookingFilters = [
  { id: 'all', name: 'Todas' },
  { id: 'pending', name: 'Pendientes' },
  { id: 'confirmed', name: 'Confirmadas' },
  { id: 'completed', name: 'Completadas' },
  { id: 'cancelled', name: 'Canceladas' }
];

const filteredBookings = computed(() => {
  if (activeBookingFilter.value === 'all') return bookings.value;
  return bookings.value.filter(
    booking => booking.status === activeBookingFilter.value
  );
});

const getStatusText = (status) => {
  switch (status) {
    case 'pending': return 'Pendiente';
    case 'confirmed': return 'Confirmada';
    case 'completed': return 'Completada';
    case 'cancelled': return 'Cancelada';
    default: return status;
  }
};

const viewBookingDetails = (id) => {
  // Implementation for viewing booking details
  console.log('View booking details', id);
};

const canCancel = (booking) => {
  // Check if booking is within cancellation period (e.g., more than 48 hours before check-in)
  const checkIn = new Date(booking.checkIn);
  const now = new Date();
  const diffTime = checkIn.getTime() - now.getTime();
  const diffHours = diffTime / (1000 * 60 * 60);
  
  return diffHours > 48;
};

const cancelBooking = (id) => {
  const index = bookings.value.findIndex(b => b.id === id);
  if (index !== -1) {
    bookings.value[index].status = 'cancelled';
    alert('Reserva cancelada con éxito.');
  }
};

const contactOwnerForBooking = (bookingId) => {
  const booking = bookings.value.find(b => b.id === bookingId);
  if (!booking) return;
  
  // Check if conversation exists
  const existingConversation = conversations.value.findIndex(c => 
    c.bookingId === bookingId
  );
  
  if (existingConversation === -1) {
    // Create new conversation
    conversations.value.push({
      id: Date.now(),
      name: `Propietario de ${booking.property.name}`,
      property: booking.property,
      bookingId: booking.id,
      lastMessage: {
        text: '',
        time: 'Ahora'
      },
      messages: []
    });
    
    // Set as active conversation
    activeConversation.value = conversations.value.length - 1;
  } else {
    // Set existing conversation as active
    activeConversation.value = existingConversation;
  }
  
  // Navigate to messages
  changeTab('messages');
};

// Favorites
const favorites = ref([1, 3]); // IDs of favorite properties

const isFavorite = (id) => {
  return favorites.value.includes(id);
};

const toggleFavorite = (id) => {
  const index = favorites.value.indexOf(id);
  if (index === -1) {
    favorites.value.push(id);
  } else {
    favorites.value.splice(index, 1);
  }
};

const favoriteProperties = computed(() => {
  return properties.value.filter(property => favorites.value.includes(property.id));
});

// Messages
const conversations = ref([
  {
    id: 1,
    name: 'Carlos Rodríguez',
    property: {
      id: 1,
      name: 'Apartamento con vistas al mar'
    },
    bookingId: 'B1234',
    lastMessage: {
      text: 'Perfecto, gracias por la información. Nos vemos el día 15.',
      time: 'Hace 2 días'
    },
    messages: [
      {
        text: 'Hola, tengo algunas preguntas sobre mi reserva para el apartamento.',
        time: '10:30',
        sent: true
      },
      {
        text: 'Hola Ana, claro. ¿En qué puedo ayudarte?',
        time: '10:35',
        sent: false
      },
      {
        text: '¿Podría saber a qué hora puedo hacer el check-in?',
        time: '10:40',
        sent: true
      },
      {
        text: 'El check-in es a partir de las 15:00, pero si llegas antes puedes dejar el equipaje en recepción.',
        time: '10:45',
        sent: false
      },
      {
        text: 'Perfecto, gracias por la información. Nos vemos el día 15.',
        time: '10:50',
        sent: true
      }
    ]
  },
  {
    id: 2,
    name: 'María López',
    property: {
      id: 3,
      name: 'Villa con piscina privada'
    },
    bookingId: 'B5678',
    lastMessage: {
      text: 'Hola Ana, he recibido tu solicitud de reserva. ¿Tienes alguna pregunta antes de confirmarla?',
      time: 'Hace 1 día'
    },
    messages: [
      {
        text: 'Hola Ana, he recibido tu solicitud de reserva. ¿Tienes alguna pregunta antes de confirmarla?',
        time: 'Ayer',
        sent: false
      }
    ]
  }
]);

const activeConversation = ref(null);
const recentMessages = computed(() => {
  return conversations.value.map(c => ({
    id: c.id,
    sender: c.name,
    property: c.property,
    text: c.lastMessage.text,
    createdAt: new Date().toISOString() // Mock date
  })).slice(0, 3);
});

const unreadMessages = computed(() => {
  // Count conversations with unread messages
  return 1; // Mock value
});

// Profile
const profileTabs = [
  { id: 'personal', name: 'Información Personal', icon: UserIcon },
  { id: 'payment', name: 'Métodos de Pago', icon: CreditCardIcon },
  { id: 'notifications', name: 'Notificaciones', icon: BellIcon }
];

const activeProfileTab = ref('personal');
const avatarInput = ref(null);
const previewImage = ref(null);

const profileData = ref({
  name: user.value.name,
  lastname: user.value.lastname,
  email: user.value.email,
  phone: user.value.phone,
  avatar: user.value.avatar
});

const triggerFileInput = () => {
  avatarInput.value.click();
};

const handleAvatarChange = (event) => {
  const file = event.target.files[0];
  if (file && file.type.startsWith('image/')) {
    profileData.value.avatar = file;
    previewImage.value = URL.createObjectURL(file);
  }
};

const saveProfile = () => {
  // Update user data
  user.value = {
    ...user.value,
    name: profileData.value.name,
    lastname: profileData.value.lastname,
    email: profileData.value.email,
    phone: profileData.value.phone,
    avatar: profileData.value.avatar
  };
  
  alert('Perfil actualizado correctamente');
};

// Payment methods
const paymentMethods = ref([
  { cardType: 'Visa', last4: '4242', expiryDate: '12/2025' }
]);

const addPaymentMethod = () => {
  // Implementation for adding payment method
  console.log('Add payment method');
};

const editPayment = (index) => {
  // Implementation for editing payment method
  console.log('Edit payment method', index);
};

const deletePayment = (index) => {
  paymentMethods.value.splice(index, 1);
};

// Notification settings
const notificationSettings = ref({
  bookingConfirmationEmail: true,
  newMessageEmail: true,
  specialOffersEmail: false,
  bookingConfirmationSMS: false
});

const saveNotificationSettings = () => {
  alert('Preferencias de notificaciones guardadas correctamente');
};

// Dashboard data
const activeBookings = computed(() => {
  return bookings.value.filter(booking => 
    booking.status === 'confirmed' || booking.status === 'pending'
  );
});

const nextBookingDate = computed(() => {
  const upcoming = bookings.value
    .filter(booking => booking.status === 'confirmed' && new Date(booking.checkIn) > new Date())
    .sort((a, b) => new Date(a.checkIn) - new Date(b.checkIn));
  
  if (upcoming.length > 0) {
    return formatDate(upcoming[0].checkIn);
  }
  
  return 'No hay reservas próximas';
});

const recommendedProperties = computed(() => {
  // Return a few random properties as recommendations
  return properties.value
    .filter(p => p.id !== selectedProperty.value?.id)
    .sort(() => 0.5 - Math.random())
    .slice(0, 3);
});

// Utility functions
const formatDate = (dateString) => {
  const options = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  const today = new Date();
  
  if (date.toDateString() === today.toDateString()) {
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
  }
  
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);
  
  if (date.toDateString() === yesterday.toDateString()) {
    return 'Ayer';
  }
  
  return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
};


// Watch for route changes
watch(() => route.path, () => {
  updateActiveTabFromRoute();
}, { immediate: true });


</script>

<style scoped>
/* Global styles */
.renters-portal {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  color: #333;
  background-color: #f5f5f5;
  min-height: 100vh;
}
/* Bookings */

.renters-bookings-section {
  max-width: 1150px;
  margin: 0 auto;
  padding: 2rem 0 4rem 0;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2.5rem;
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  color: #f59e0b;
  margin: 0;
  letter-spacing: -0.5px;
}

.header-actions .export-button {
  background: linear-gradient(90deg, #fbbf24, #f59e0b);
  color: #fff;
  border: none;
  padding: 0.5rem 1.3rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 2px 12px 0 rgba(245, 158, 11, 0.08);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background 0.18s, box-shadow 0.18s;
}

.header-actions .export-button:hover {
  background: linear-gradient(90deg, #f59e0b, #fbbf24);
  box-shadow: 0 4px 16px 0 rgba(245, 158, 11, 0.16);
}

.bookings-filter {
  margin-bottom: 2rem;
}

.filter-tabs {
  display: flex;
  gap: 1.2rem;
  margin-bottom: 1.4rem;
}

.filter-tab {
  background: none;
  border: none;
  color: #a16207;
  font-weight: 600;
  font-size: 1.07rem;
  padding: 0.45rem 1.2rem;
  border-radius: 999px;
  transition: background 0.17s, color 0.17s;
  cursor: pointer;
  position: relative;
}

.filter-tab.active {
  background: linear-gradient(90deg, #fbbf24, #f59e0b);
  color: #fff;
  box-shadow: 0 2px 10px 0 rgba(245, 158, 11, 0.08);
}

.filter-count {
  margin-left: 0.5em;
  background: #fbbf24;
  color: #fff;
  border-radius: 50%;
  padding: 0.15em 0.5em;
  font-size: 0.85em;
  font-weight: 700;
  vertical-align: middle;
}

.bookings-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.renters-booking-card {
  background: #fffbe6;
  border-radius: 18px;
  box-shadow: 0 4px 16px 0 rgba(245, 158, 11, 0.09);
  padding: 1.65rem 2rem 1.5rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  border: 1.5px solid #fde68a;
}

.booking-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.booking-property-info {
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.booking-property-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 14px;
  border: 2.5px solid #fbbf24;
  background: #fff;
}

.booking-status {
  font-weight: bold;
  font-size: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 16px;
  background: #fef3c7;
  color: #d97706;
  text-transform: capitalize;
}

.booking-status.confirmed {
  background: #fbbf24;
  color: #fff;
}
.booking-status.pending {
  background: #fef9c3;
  color: #f59e0b;
}
.booking-status.cancelled {
  background: #f87171;
  color: #fff;
}
.booking-status.completed {
  background: #34d399;
  color: #fff;
}

.booking-details {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.booking-dates, .booking-guests, .booking-price {
  display: flex;
  align-items: center;
  gap: 0.8rem;
}

.date-icon, .guests-icon, .price-icon {
  color: #fbbf24;
  width: 1.4em;
  height: 1.4em;
}

.date-label, .guests-label, .price-label {
  font-size: 0.98rem;
  color: #a16207;
  font-weight: 600;
}
.date-value, .guests-value, .price-value {
  font-size: 1.03rem;
  font-weight: 700;
  color: #1e293b;
}

.booking-actions {
  display: flex;
  gap: 1.1rem;
  margin-top: 0.5rem;
}

.action-button {
  background: linear-gradient(90deg, #fbbf24 60%, #f59e0b 100%);
  color: #fff;
  border: none;
  padding: 0.48rem 1.2rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  transition: background 0.17s;
}
.action-button.cancel {
  background: linear-gradient(90deg, #f87171 75%, #fbbf24 100%);
}
.action-button.message {
  background: linear-gradient(90deg, #fbbf24 70%, #f59e0b 100%);
}
.action-button.view {
  background: linear-gradient(90deg, #a16207 75%, #fbbf24 100%);
}
.action-button:hover {
  opacity: 0.93;
}

.empty-bookings {
  text-align: center;
  margin: 4rem 0 2.5rem 0;
  color: #b45309;
}

.empty-icon {
  font-size: 3.5rem;
  color: #fbbf24;
  margin-bottom: 1rem;
}

.available-properties-list {
  margin-top: 2.5rem;
}

.properties-grid {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  justify-content: center;
}

.renters-property-card {
  background: #fffbe6;
  border-radius: 18px;
  box-shadow: 0 2px 8px 0 rgba(245, 158, 11, 0.13);
  padding: 1.2rem 1.5rem 1.2rem 1.5rem;
  min-width: 260px;
  max-width: 330px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  border: 1.5px solid #fde68a;
}

.property-image-container {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
}

.property-image {
  width: 90%;
  max-width: 230px;
  height: 130px;
  object-fit: cover;
  border-radius: 15px;
  border: 2px solid #fbbf24;
  background: #fff;
}

.property-content {
  width: 100%;
}

.property-name {
  font-size: 1.13rem;
  font-weight: 700;
  color: #a16207;
  margin-bottom: 0.4rem;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.99rem;
  color: #f59e0b;
  margin-bottom: 0.5rem;
}

.property-details {
  display: flex;
  gap: 1.1rem;
  font-size: 0.96rem;
  margin-bottom: 0.5rem;
}

.property-detail {
  display: flex;
  align-items: center;
  gap: 0.22rem;
  background: #fbbf24;
  color: #fff;
  border-radius: 7px;
  padding: 0.18rem 0.7rem;
  font-size: 0.97em;
}

.property-price {
  font-size: 1.14rem;
  font-weight: 700;
  color: #a16207;
  margin-bottom: 0.7rem;
}

.price-period {
  font-size: 0.93rem;
  font-weight: 400;
  color: #b45309;
  margin-left: 0.2rem;
}

.view-property-button {
  background: linear-gradient(90deg, #fde68a 50%, #fbbf24 100%);
  color: #a16207;
  border: none;
  border-radius: 8px;
  padding: 0.45rem 1.2rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  margin-top: 0.7rem;
  transition: background 0.17s;
}
.view-property-button:hover {
  background: linear-gradient(90deg, #fbbf24 60%, #fde68a 100%);
}

.booking-details-modal {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(245, 158, 11, 0.18);
  z-index: 200;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 2.5rem 0;
  overflow-y: auto;
}

.modal-content {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 12px 32px 0 rgba(245, 158, 11, 0.16);
  width: 95%;
  max-width: 570px;
  padding: 2.5rem 2.3rem 2rem 2.3rem;
  position: relative;
  animation: fadeInModal 0.19s cubic-bezier(.8,-0.01,.29,1.07);
}
@keyframes fadeInModal {
  from { transform: scale(0.93); opacity: 0.2; }
  to { transform: scale(1); opacity: 1; }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.close-button {
  background: none;
  border: none;
  cursor: pointer;
  color: #fbbf24;
  font-size: 1.5rem;
}

.close-icon {
  width: 1.7em;
  height: 1.7em;
}

.modal-body {
  margin-top: 1.5rem;
}

.booking-property-details {
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.booking-property-details .property-image {
  width: 70px;
  height: 70px;
  border-radius: 10px;
  border: 2px solid #fbbf24;
}

.property-info h4 {
  font-size: 1.18rem;
  font-weight: 700;
  color: #a16207;
  margin-bottom: 0.3rem;
}
.property-location {
  color: #f59e0b;
}

.booking-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.3rem;
  margin: 1.5rem 0;
}

.booking-info-item {
  background: #fffbe6;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.05);
  color: #a16207;
}
.booking-info-item h5 {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #f59e0b;
}
.info-content {
  display: flex;
  align-items: flex-start;
  gap: 0.9rem;
}
.info-icon {
  color: #fbbf24;
  font-size: 1.15em;
  margin-top: 0.1em;
}

.status-badge {
  padding: 0.12em 0.8em;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.98em;
  background: #fde68a;
  color: #a16207;
  margin-left: 0.5em;
}
.status-badge.confirmed {
  background: #fbbf24;
  color: #fff;
}
.status-badge.pending {
  background: #fef9c3;
  color: #f59e0b;
}
.status-badge.cancelled {
  background: #f87171;
  color: #fff;
}
.status-badge.completed {
  background: #34d399;
  color: #fff;
}

.booking-notes {
  background: #fef3c7;
  border-radius: 8px;
  padding: 0.8rem 1.2rem;
  margin-bottom: 1.1rem;
  color: #a16207;
}
.booking-notes h5 {
  color: #f59e0b;
  margin-bottom: 0.3rem;
}

.booking-timeline {
  margin-bottom: 1.2rem;
}
.timeline {
  margin-top: 0.6rem;
}
.timeline-item {
  display: flex;
  align-items: flex-start;
  gap: 0.8rem;
  margin-bottom: 0.8rem;
}
.timeline-icon {
  width: 2.1em;
  height: 2.1em;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fbbf24;
  color: #fff;
  border-radius: 50%;
  font-size: 1.15em;
}
.timeline-content {
  flex: 1;
}
.event-text {
  font-weight: 500;
  color: #a16207;
  margin-bottom: 0.1em;
}
.event-date {
  font-size: 0.93em;
  color: #b45309;
}

.modal-actions {
  display: flex;
  gap: 1.2rem;
  margin-top: 1.6rem;
  justify-content: flex-end;
}
.modal-actions .action-button {
  box-shadow: 0 2px 8px 0 rgba(245, 158, 11, 0.07);
}

@media (max-width: 900px) {
  .bookings-list, .properties-grid {
    flex-direction: column;
    gap: 1.3rem;
  }
  .renters-property-card, .renters-booking-card {
    max-width: 100%;
    min-width: 0;
    width: 100%;
  }
  .modal-content {
    padding: 1.3rem 0.7rem;
  }
  .booking-info-grid {
    grid-template-columns: 1fr;
    gap: 1.1rem;
  }
}

/* Booking */

/* Animación de entrada suave */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.45s cubic-bezier(.4,0,.2,1);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Loader solar animado */
.loading-solar-container {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  min-height: 340px; margin: 2.5rem 0;
}
.solar-spinner {
  width: 70px; height: 70px; border-radius: 50%;
  border: 7px solid #fde68a;
  border-top: 7px solid #fbbf24;
  border-right: 7px solid #f59e0b;
  animation: spin 1.2s linear infinite;
  margin-bottom: 1.4rem;
  position: relative;
}
.solar-spinner::after {
  content: '';
  position: absolute; left: 50%; top: 50%;
  width: 16px; height: 16px;
  background: #fffbe6;
  border-radius: 50%;
  box-shadow: 0 0 8px 2px #fde68a;
  transform: translate(-50%, -50%);
}
@keyframes spin {
  0% { transform: rotate(0deg);}
  100% { transform: rotate(360deg);}
}
.loading-text {
  color: #a16207; font-weight: 700; font-size: 1.13rem;
  text-shadow: 0 2px 12px #fde68a73;
}


/* Dashboard */

.dashboard-section.renters-dashboard {
  min-height: 100vh;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%);
  border-radius: 24px;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.04);
  margin: 0 auto;
  max-width: 1400px;
  position: relative;
  overflow: hidden;
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-align: center;
  margin-bottom: 2rem;
}

.stats-grid.renters-stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* Siempre dos columnas */
  grid-template-rows: repeat(2, 1fr);    /* Siempre dos filas */
  gap: 2rem;
  margin: 0 auto 2rem auto;
  max-width: 680px; /* Ajusta si quieres cards más grandes/pequeñas */
  background: #f6fbff;
  padding: 2.5rem 2rem 2rem 2rem;
  border-radius: 24px;
  box-shadow: 0 4px 24px rgba(245, 158, 11, 0.04);
}

.stat-card.renters-stat-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  padding: 1.5rem 1.25rem;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.08);
  display: flex;
  align-items: center;
  gap: 1.25rem;
  border: 1px solid rgba(245, 158, 11, 0.12);
  transition: box-shadow 0.3s;
  position: relative;
  overflow: hidden;
}

.stat-card.renters-stat-card:hover {
  box-shadow: 0 6px 24px rgba(245, 158, 11, 0.17);
}

.stat-icon.renters-stat-icon {
  width: 38px;
  height: 38px;
  color: #f59e0b;
  filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.2));
  flex-shrink: 0;
}

.stat-content {
  flex: 1;
}

.stat-title {
  font-size: 1.1rem;
  color: #b45309;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.stat-value {
  font-size: 2.1rem;
  font-weight: 700;
  color: #1e293b;
}

/* Dashboard Grid adaptado */

.renters-dashboard {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem 0;
}

.stats-grid.renters-stats {
  margin: 0 auto 2rem auto;
  max-width: 680px;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-template-rows: repeat(2, 1fr);
  gap: 2rem;
  background: #f6fbff;
  padding: 2.5rem 2rem 2rem 2rem;
  border-radius: 24px;
  box-shadow: 0 4px 24px rgba(245, 158, 11, 0.04);
}

.dashboard-grid.renters-dashboard-grid {
  margin: 0 auto 2.5rem auto;
  max-width: 900px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.dashboard-card.renters-dashboard-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.04);
  border: 1px solid #fff7e6;
  overflow: hidden;
  transition: box-shadow 0.3s;
  margin-bottom: 0;
}

.recommended-section.renters-recommended-section {
  margin: 0 auto 2.5rem auto;
  max-width: 1000px;
  padding: 2rem 0 2.5rem 0;
}


.dashboard-grid.renters-dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
}

.dashboard-card.renters-dashboard-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 18px;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.04);
  border: 1px solid rgba(245, 158, 11, 0.10);
  overflow: hidden;
  transition: box-shadow 0.3s;
  margin-bottom: 0;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  background: rgba(245, 158, 11, 0.06);
  border-bottom: 1px solid rgba(245, 158, 11, 0.13);
}

.card-header h3 {
  font-size: 1.1rem;
  color: #b45309;
  margin: 0;
  font-weight: 700;
}

.view-all-button {
  background: none;
  border: none;
  color: #f59e0b;
  font-size: 0.97rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0.3rem 0.8rem;
  border-radius: 8px;
  transition: background 0.2s, color 0.2s;
}

.view-all-button:hover {
  background: rgba(245, 158, 11, 0.11);
  color: #b45309;
}

/* Reservas */
.upcoming-bookings {
  padding: 1.2rem 1.5rem;
}

.booking-item.renters-booking-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.2rem;
  padding-bottom: 1.1rem;
  margin-bottom: 1.1rem;
  border-bottom: 1px solid #fde68a;
}

.booking-item.renters-booking-item:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.booking-property {
  display: flex;
  align-items: center;
  gap: 1.1rem;
}

.booking-image.renters-booking-image {
  width: 54px;
  height: 54px;
  border-radius: 10px;
  object-fit: cover;
}

.booking-property h4 {
  font-size: 1.08rem;
  color: #f59e0b;
  margin-bottom: 0.35rem;
  font-weight: 600;
}

.booking-dates {
  font-size: 0.93rem;
  color: #b45309;
}

.booking-status {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 500;
  background: #fffbe6;
  color: #b45309;
  border: 1px solid #fde68a;
}

.booking-status.approved {
  background: #e6f7ee;
  color: #047857;
  border-color: #bbf7d0;
}

.booking-status.pending {
  background: #fffbe6;
  color: #b45309;
  border-color: #fde68a;
}

.booking-status.cancelled {
  background: #fff2f0;
  color: #e41c00;
  border-color: #fecaca;
}

/* Empty State renter */
.empty-state.renters-empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2.5rem 1.5rem;
  text-align: center;
}

.empty-icon.renters-empty-icon {
  width: 42px;
  height: 42px;
  color: #fbbf24;
  margin-bottom: 1.1rem;
}

.empty-state.renters-empty-state p {
  color: #b45309;
  margin-bottom: 1.2rem;
}

.empty-state.renters-empty-state .action-button {
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #fff;
  border: none;
  padding: 0.7rem 1.5rem;
  border-radius: 9px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.07);
  transition: background 0.2s, color 0.2s;
}

.empty-state.renters-empty-state .action-button:hover {
  background: #fbbf24;
  color: #b45309;
}

/* Mensajes recientes */
.messages-list {
  padding: 1.1rem 1.5rem;
}

.message-item.renters-message-item {
  margin-bottom: 1.2rem;
  padding-bottom: 1.2rem;
  border-bottom: 1px solid #fde68a;
}

.message-item.renters-message-item:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.message-sender {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  margin-bottom: 0.4rem;
}

.message-icon.renters-message-icon {
  width: 20px;
  height: 20px;
  color: #f59e0b;
}

.message-sender h4 {
  font-size: 1.02rem;
  color: #b45309;
  margin: 0;
  margin-bottom: 0.23rem;
  font-weight: 600;
}

.message-property {
  font-size: 0.9rem;
  color: #b45309;
}

.message-preview {
  font-size: 0.97rem;
  color: #1e293b;
  margin: 0 0 0.4rem;
}

.message-time {
  font-size: 0.85rem;
  color: #fbbf24;
  text-align: right;
  margin: 0;
}

/* Recomendados para ti */
.recommended-section.renters-recommended-section {
  margin-top: 2.5rem;
}

.section-subtitle {
  font-size: 1.3rem;
  color: #f59e0b;
  font-weight: 700;
  margin-bottom: 1.3rem;
}

.properties-grid.renters-properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.property-card.renters-property-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.08);
  border: 1px solid rgba(245, 158, 11, 0.12);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: box-shadow 0.2s;
}

.property-card.renters-property-card:hover {
  box-shadow: 0 8px 24px rgba(245, 158, 11, 0.13);
}

.property-image-container {
  position: relative;
  width: 100%;
  height: 175px;
  overflow: hidden;
}

.property-image.renters-property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}

.favorite-button {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background: rgba(255,255,255,0.95);
  border: none;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(245, 158, 11, 0.09);
  cursor: pointer;
  transition: background 0.2s;
}

.favorite-button.active,
.favorite-button:hover {
  background: #fbbf24;
}

.favorite-icon {
  width: 20px;
  height: 20px;
  color: #f59e0b;
}

.favorite-button.active .favorite-icon,
.favorite-button:hover .favorite-icon {
  color: #fff;
}

.property-content {
  padding: 1.1rem 1rem 1.3rem 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}

.property-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: #b45309;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: #b45309;
  font-size: 0.95rem;
}

.location-icon {
  width: 16px;
  height: 16px;
  color: #fbbf24;
}

.property-details {
  display: flex;
  gap: 1.2rem;
  margin-bottom: 0.5rem;
}

.property-detail {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  color: #b45309;
  font-size: 0.93rem;
}

.detail-icon {
  width: 16px;
  height: 16px;
  color: #fbbf24;
}

.property-price {
  display: flex;
  align-items: baseline;
  gap: 0.4rem;
}

.price-value {
  font-size: 1.14rem;
  font-weight: 700;
  color: #f59e0b;
}

.price-period {
  font-size: 0.95rem;
  color: #b45309;
}

.yellow-button {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  padding: 0.48rem 1.2rem;
  cursor: pointer;
  box-shadow: 0 2px 8px 0 rgba(245, 158, 11, 0.08);
  display: flex;
  align-items: center;
  gap: 0.3rem;
  transition: background 0.18s, box-shadow 0.18s, transform 0.13s;
  min-width: 120px;
}

.yellow-button::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
  transform: translateX(-100%);
  transition: transform 0s;
  z-index: 1;
}

.yellow-button:hover::before {
  transform: translateX(100%);
  transition: transform 0.6s cubic-bezier(.4,0,.2,1);
}

.yellow-button:hover {
  background: linear-gradient(135deg, #f59e0b 0%, #fcd34d 100%);
  box-shadow: 0 4px 16px rgba(251, 191, 36, 0.12);
  transform: translateY(-2px);
}

.yellow-button:active {
  transform: translateY(1px);
}

.yellow-button .button-icon {
  font-size: 1.2em;
  color: #fbbf24;
  margin-right: 0.36em;
  z-index: 2;
}

.yellow-button:disabled,
.yellow-button[disabled] {
  opacity: 0.54;
  cursor: not-allowed;
  box-shadow: none;
  background: linear-gradient(135deg, #fde68a 0%, #fbbf24 100%);
}

.view-property-button {
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  padding: 0.65rem 1.2rem;
  margin-top: 0.7rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.view-property-button:hover {
  background: #fbbf24;
  color: #b45309;
}

@media (max-width: 900px) {
  .dashboard-grid.renters-dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .dashboard-section.renters-dashboard {
    padding: 1rem 0.25rem;
    border-radius: 0;
  }
  .dashboard-card.renters-dashboard-card {
    border-radius: 8px;
  }
  .properties-grid.renters-properties-grid {
    grid-template-columns: 1fr;
  }
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

.section-subtitle {
  font-size: 1.4rem;
  margin: 2rem 0 1rem;
  color: #003580;
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
  margin-bottom: 2rem;
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
}

.upcoming-bookings {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.booking-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
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

.booking-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.booking-status.confirmed {
  background-color: #e6f0ff;
  color: #0071c2;
}

.booking-status.pending {
  background-color: #fff8e6;
  color: #b27b00;
}

.booking-status.completed {
  background-color: #e6f7ee;
  color: #00703c;
}

.booking-status.cancelled {
  background-color: #fff2f0;
  color: #e41c00;
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

.action-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.action-button:hover {
  background-color: #005999;
}

.action-button.search {
  margin-top: 1rem;
}

/* Properties grid */
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
  transition: transform 0.3s, box-shadow 0.3s;
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.property-image-container {
  position: relative;
}

.property-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
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
  transition: transform 0.3s;
}

.favorite-button:hover {
  transform: scale(1.1);
}

.favorite-button.active .favorite-icon {
  fill: #e41c00;
  color: #e41c00;
}

.favorite-icon {
  width: 20px;
  height: 20px;
  color: #666;
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

.property-amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.property-amenity {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  color: #666;
}

.amenity-icon {
  width: 14px;
  height: 14px;
  color: #0071c2;
  margin-right: 0.25rem;
}

.property-amenity.more {
  color: #0071c2;
}

.property-price {
  margin-bottom: 1rem;
}

.price-value {
  font-size: 1.2rem;
  font-weight: 700;
  color: #003580;
}

.price-period {
  font-size: 0.9rem;
  color: #666;
}

.view-property-button {
  width: 100%;
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 0;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.view-property-button:hover {
  background-color: #005999;
}

/* Search styles */
.search-header {
  margin-bottom: 2rem;
}

.search-filters {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.search-bar {
  position: relative;
  margin-bottom: 1.5rem;
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
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.search-button {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
}

.filter-group {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: flex-end;
}

.filter-item {
  flex: 1;
  min-width: 200px;
}

.filter-item label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.date-picker {
  display: flex;
  align-items: center;
}

.date-input {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.date-separator {
  margin: 0 0.5rem;
}

.guests-selector {
  display: flex;
  align-items: center;
  border: 1px solid #ccc;
  border-radius: 4px;
  overflow: hidden;
}

.guest-button {
  background: none;
  border: none;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.guest-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.guest-icon {
  width: 16px;
  height: 16px;
  color: #0071c2;
}

.guests-count {
  flex: 1;
  text-align: center;
  font-weight: 500;
}

.price-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.price-range {
  flex: 1;
}

.price-value {
  font-weight: 500;
  min-width: 60px;
}

.filter-button {
  display: flex;
  align-items: center;
  background: none;
  border: 1px solid #ccc;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
}

.filter-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.advanced-filters {
  background-color: #f9f9f9;
  border-radius: 0 0 8px 8px;
  padding: 1.5rem;
  margin-top: -0.5rem;
  margin-bottom: 1.5rem;
  border-top: 1px solid #eee;
}

.filter-row {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-bottom: 1.5rem;
}

.filter-group h4 {
  font-size: 1rem;
  margin: 0 0 1rem;
}

.checkbox-group {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.filter-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.clear-button {
  background: none;
  border: 1px solid #ccc;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
}

.apply-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
}

.results-count {
  margin-bottom: 1rem;
  font-weight: 500;
}

.loading-properties {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 0;
}

.spinner {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
  animation: spin 1s linear infinite;
}

.spinner .path {
  stroke: #0071c2;
  stroke-linecap: round;
}

@keyframes spin {
  100% { transform: rotate(360deg); }
}

.loading-text {
  font-size: 1.1rem;
  color: #003580;
  font-weight: 500;
}

.empty-search {
  text-align: center;
  padding: 3rem 0;
}

/* Property detail styles */
.property-detail-section {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.back-button {
  display: flex;
  align-items: center;
  background: none;
  border: none;
  color: #0071c2;
  font-weight: 500;
  cursor: pointer;
  margin-bottom: 1.5rem;
  padding: 0;
}

.back-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

.property-detail {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.property-gallery {
  grid-column: 1 / 2;
}

.main-image {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.gallery-thumbnails {
  display: flex;
  gap: 1rem;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 4px;
  object-fit: cover;
  cursor: pointer;
  opacity: 0.7;
  transition: opacity 0.3s;
}

.thumbnail.active {
  opacity: 1;
  border: 2px solid #0071c2;
}

.thumbnail.placeholder {
  background-color: #f5f5f5;
}

.property-info {
  grid-column: 1 / 2;
}

.property-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.property-title {
  font-size: 1.8rem;
  margin: 0 0 0.5rem;
  color: #003580;
}

.favorite-button.large {
  display: flex;
  align-items: center;
  background: none;
  border: 1px solid #ccc;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
}

.favorite-button.large .favorite-icon {
  margin-right: 0.5rem;
}

.favorite-button.large.active {
  color: #e41c00;
  border-color: #e41c00;
}

.property-highlights {
  display: flex;
  gap: 2rem;
  margin-bottom: 1.5rem;
}

.highlight-item {
  display: flex;
  align-items: center;
}

.highlight-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-right: 0.75rem;
}

.highlight-value {
  display: block;
  font-size: 1.2rem;
  font-weight: 700;
  color: #003580;
}

.highlight-label {
  display: block;
  font-size: 0.9rem;
  color: #666;
}

.property-description {
  margin-bottom: 1.5rem;
}

.property-description h3 {
  font-size: 1.2rem;
  margin: 0 0 1rem;
  color: #003580;
}

.property-amenities-section {
  margin-bottom: 1.5rem;
}

.property-amenities-section h3 {
  font-size: 1.2rem;
  margin: 0 0 1rem;
  color: #003580;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.amenity-item {
  display: flex;
  align-items: center;
}

.booking-card {
  grid-column: 2 / 3;
  grid-row: 1 / 3;
  background-color: #f9f9f9;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  align-self: start;
  position: sticky;
  top: 2rem;
}

.booking-price {
  margin-bottom: 1.5rem;
  text-align: center;
}

.booking-price .price-value {
  font-size: 1.8rem;
}

.booking-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.booking-dates {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.date-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.date-field label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.guests-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.guests-field label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.booking-summary {
  border-top: 1px solid #ddd;
  padding-top: 1rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  font-weight: 700;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #ddd;
}

.book-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 0;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.book-button:hover {
  background-color: #005999;
}

.book-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.contact-button {
  display: flex;
  align-items: center;
  justify-content: center;
  background: none;
  border: 1px solid #0071c2;
  color: #0071c2;
  padding: 0.75rem 0;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
}

.contact-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

/* Bookings styles */
.bookings-filter {
  margin-bottom: 1.5rem;
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

.date-item, .booking-guests, .booking-price {
  display: flex;
  align-items: center;
}

.date-icon, .guests-icon, .price-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.date-label, .guests-label, .price-label {
  display: block;
  font-size: 0.8rem;
  color: #666;
}

.date-value, .guests-value, .price-value {
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

.action-button.cancel {
  background-color: #fff2f0;
  color: #e41c00;
  border: 1px solid #e41c00;
}

.action-button.cancel:hover {
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

.action-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.empty-bookings, .empty-favorites {
  text-align: center;
  padding: 3rem 0;
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

/* Profile styles */
.profile-container {
  display: flex;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.profile-sidebar {
  width: 250px;
  border-right: 1px solid #eee;
  padding: 1.5rem 0;
}

.profile-tab {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.profile-tab:hover, .profile-tab.active {
  background-color: #f5f5f5;
}

.profile-tab.active {
  border-left: 3px solid #0071c2;
}

.profile-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.75rem;
}

.profile-content {
  flex: 1;
  padding: 1.5rem;
}

.profile-panel {
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
  overflow: hidden;
}

.avatar-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-icon {
  width: 40px;
  height: 40px;
  color: #0071c2;
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

.form-row {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin-bottom: 1.5rem;
}

.form-group label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.form-input {
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
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
}

.save-button:hover {
  background-color: #005999;
}

.payment-methods {
  margin-bottom: 2rem;
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

.empty-payment-methods {
  text-align: center;
  padding: 2rem 0;
  margin-bottom: 1rem;
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

/* Responsive adjustments */
@media (max-width: 992px) {
  .property-detail {
    grid-template-columns: 1fr;
  }
  
  .booking-card {
    grid-column: 1 / 2;
    grid-row: auto;
    position: static;
    margin-top: 2rem;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .filter-group {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-item {
    width: 100%;
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
    grid-template-columns: 1fr;
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
  
  .profile-container {
    flex-direction: column;
  }
  
  .profile-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid #eee;
  }
}

@media (max-width: 576px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .property-highlights {
    flex-direction: column;
    gap: 1rem;
  }
  
  .form-row {
    flex-direction: column;
    gap: 0;
  }
  
  .booking-dates {
    grid-template-columns: 1fr;
  }
}
</style>