<template>
  <div class="renters-portal">
    <!-- Header -->
    <Header :user="user" :activeTab="activeTab" @changeTab="changeTab" />

    <!-- Main Content -->
    <main class="main-content">
      <!-- Dashboard -->
      <section v-if="activeTab === 'dashboard'" class="dashboard-section">
        <h2 class="section-title">Panel de Control</h2>
        
        <div class="stats-grid">
          <div class="stat-card">
            <CalendarIcon class="stat-icon" />
            <div class="stat-content">
              <h3 class="stat-title">Reservas Activas</h3>
              <p class="stat-value">{{ activeBookings.length }}</p>
            </div>
          </div>
          
          <div class="stat-card">
            <ClockIcon class="stat-icon" />
            <div class="stat-content">
              <h3 class="stat-title">Próxima Reserva</h3>
              <p class="stat-value">{{ nextBookingDate }}</p>
            </div>
          </div>
          
          <div class="stat-card">
            <MessageSquareIcon class="stat-icon" />
            <div class="stat-content">
              <h3 class="stat-title">Mensajes Nuevos</h3>
              <p class="stat-value">{{ unreadMessages }}</p>
            </div>
          </div>
          
          <div class="stat-card">
            <HeartIcon class="stat-icon" />
            <div class="stat-content">
              <h3 class="stat-title">Favoritos</h3>
              <p class="stat-value">{{ favorites.length }}</p>
            </div>
          </div>
        </div>
        
        <div class="dashboard-grid">
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Mis Reservas</h3>
              <button class="view-all-button" @click="changeTab('bookings')">Ver todas</button>
            </div>
            <div v-if="activeBookings.length > 0" class="upcoming-bookings">
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
              <button class="action-button search" @click="changeTab('search')">Buscar Alojamiento</button>
            </div>
          </div>
          
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Mensajes Recientes</h3>
              <button class="view-all-button" @click="changeTab('messages')">Ver todos</button>
            </div>
            <div v-if="recentMessages.length > 0" class="messages-list">
              <div v-for="message in recentMessages" :key="message.id" class="message-item">
                <div class="message-sender">
                  <UserIcon class="message-icon" />
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
          <h3 class="section-subtitle">Recomendados para ti</h3>
          <div class="properties-grid">
            <div v-for="property in recommendedProperties" :key="property.id" class="property-card">
              <div class="property-image-container">
                <img :src="property.image" alt="Property" class="property-image" />
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

      <!-- Search -->
      <section v-if="activeTab === 'search'" class="search-section">
        <div class="search-header">
          <h2 class="section-title">Buscar Alojamiento</h2>
          <div class="search-filters">
            <div class="search-bar">
              <SearchIcon class="search-icon" />
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Buscar por ubicación o nombre..." 
                class="search-input"
                @keyup.enter="searchProperties"
              />
              <button class="search-button" @click="searchProperties">Buscar</button>
            </div>
            
            <div class="filter-group">
              <div class="filter-item">
                <label>Fechas</label>
                <div class="date-picker">
                  <input 
                    type="date" 
                    v-model="filters.checkIn" 
                    class="date-input"
                    :min="today"
                  />
                  <span class="date-separator">-</span>
                  <input 
                    type="date" 
                    v-model="filters.checkOut" 
                    class="date-input"
                    :min="filters.checkIn || today"
                  />
                </div>
              </div>
              
              <div class="filter-item">
                <label>Huéspedes</label>
                <div class="guests-selector">
                  <button 
                    class="guest-button" 
                    @click="filters.guests > 1 && filters.guests--"
                    :disabled="filters.guests <= 1"
                  >
                    <MinusIcon class="guest-icon" />
                  </button>
                  <span class="guests-count">{{ filters.guests }}</span>
                  <button 
                    class="guest-button" 
                    @click="filters.guests++"
                  >
                    <PlusIcon class="guest-icon" />
                  </button>
                </div>
              </div>
              
              <div class="filter-item">
                <label>Precio máximo</label>
                <div class="price-selector">
                  <input 
                    type="range" 
                    v-model="filters.maxPrice" 
                    min="0" 
                    max="500" 
                    step="10" 
                    class="price-range"
                  />
                  <span class="price-value">€{{ filters.maxPrice }}</span>
                </div>
              </div>
              
              <button class="filter-button" @click="toggleAdvancedFilters">
                <SlidersIcon class="filter-icon" />
                Más filtros
              </button>
            </div>
          </div>
        </div>
        
        <div v-if="showAdvancedFilters" class="advanced-filters">
          <div class="filter-row">
            <div class="filter-group">
              <h4>Dormitorios</h4>
              <div class="checkbox-group">
                <label class="checkbox-label" v-for="n in 5" :key="`bed-${n}`">
                  <input 
                    type="checkbox" 
                    :value="n" 
                    v-model="filters.bedrooms"
                  />
                  {{ n === 5 ? '5+' : n }}
                </label>
              </div>
            </div>
            
            <div class="filter-group">
              <h4>Servicios</h4>
              <div class="checkbox-group">
                <label class="checkbox-label" v-for="amenity in amenities" :key="amenity.id">
                  <input 
                    type="checkbox" 
                    :value="amenity.id" 
                    v-model="filters.amenities"
                  />
                  {{ amenity.name }}
                </label>
              </div>
            </div>
          </div>
          
          <div class="filter-actions">
            <button class="clear-button" @click="clearFilters">Limpiar filtros</button>
            <button class="apply-button" @click="applyFilters">Aplicar filtros</button>
          </div>
        </div>
        
        <div v-if="isLoading" class="loading-properties">
          <svg class="spinner" viewBox="0 0 50 50">
            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"/>
          </svg>
          <span class="loading-text">Buscando propiedades...</span>
        </div>
        
        <div v-else-if="filteredProperties.length > 0" class="search-results">
          <p class="results-count">{{ filteredProperties.length }} propiedades encontradas</p>
          
          <div class="properties-grid">
            <div v-for="property in filteredProperties" :key="property.id" class="property-card">
              <div class="property-image-container">
                <img :src="property.image" alt="Property" class="property-image" />
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
                <div class="property-amenities">
                  <div v-for="amenityId in property.amenities.slice(0, 3)" :key="amenityId" class="property-amenity">
                    <CheckIcon class="amenity-icon" />
                    <span>{{ getAmenityName(amenityId) }}</span>
                  </div>
                  <div v-if="property.amenities.length > 3" class="property-amenity more">
                    <span>+{{ property.amenities.length - 3 }} más</span>
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
        
        <div v-else class="empty-search">
          <SearchXIcon class="empty-icon" />
          <h3>No se encontraron propiedades</h3>
          <p>Intenta ajustar los filtros de búsqueda</p>
          <button class="clear-button" @click="clearFilters">Limpiar filtros</button>
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

      <!-- Bookings -->
      <section v-if="activeTab === 'bookings'" class="bookings-section">
        <h2 class="section-title">Mis Reservas</h2>
        
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
          <div v-for="booking in filteredBookings" :key="booking.id" class="booking-card">
            <div class="booking-header">
              <div class="booking-property-info">
                <img :src="booking.property.image" alt="Property" class="booking-property-image" />
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
              <button class="action-button view" @click="viewBookingDetails(booking.id)">Ver detalles</button>
              <button 
                v-if="booking.status === 'confirmed' && canCancel(booking)" 
                class="action-button cancel"
                @click="cancelBooking(booking.id)"
              >
                Cancelar reserva
              </button>
              <button 
                v-if="booking.status === 'confirmed'" 
                class="action-button message"
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
          <button v-if="activeBookingFilter === 'all'" class="action-button search" @click="changeTab('search')">
            Buscar Alojamiento
          </button>
        </div>
      </section>
      
      <!-- Favorites -->
      <section v-if="activeTab === 'favorites'" class="favorites-section">
        <h2 class="section-title">Mis Favoritos</h2>
        
        <div v-if="favoriteProperties.length > 0" class="properties-grid">
          <div v-for="property in favoriteProperties" :key="property.id" class="property-card">
            <div class="property-image-container">
              <img :src="property.image" alt="Property" class="property-image" />
              <button 
                class="favorite-button active" 
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
        
        <div v-else class="empty-favorites">
          <HeartOffIcon class="empty-icon" />
          <h3>No tienes propiedades guardadas</h3>
          <p>Guarda tus propiedades favoritas para encontrarlas fácilmente</p>
          <button class="action-button search" @click="changeTab('search')">Buscar Alojamiento</button>
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
    
    <Footer @changeTab="changeTab" />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Header from './components/layout/Header.vue';
import Footer from './components/layout/Footer.vue';
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

// Watch for route changes
watch(() => route.path, () => {
  updateActiveTabFromRoute();
}, { immediate: true });

// Initialize on mount
onMounted(() => {
  updateActiveTabFromRoute();
  fetchProperties();
  fetchBookings();
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
const isLoading = ref(false);

// Mock properties data
const fetchProperties = async () => {
  isLoading.value = true;
  
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  properties.value = [
    {
      id: 1,
      name: 'Apartamento con vistas al mar',
      location: 'Málaga, España',
      bedrooms: 2,
      capacity: 4,
      price: 85,
      size: 75,
      image: '/placeholder.svg?height=300&width=500',
      description: 'Hermoso apartamento con vistas panorámicas al mar Mediterráneo. Completamente renovado, con dos dormitorios, cocina equipada y amplio balcón. A solo 5 minutos a pie de la playa y cerca de restaurantes y tiendas.',
      amenities: ['wifi', 'pool', 'ac', 'kitchen', 'tv', 'washer']
    },
    {
      id: 2,
      name: 'Ático en el centro histórico',
      location: 'Sevilla, España',
      bedrooms: 1,
      capacity: 2,
      price: 65,
      size: 60,
      image: '/placeholder.svg?height=300&width=500',
      description: 'Acogedor ático en pleno centro histórico de Sevilla. Terraza privada con vistas a la Giralda. Ideal para parejas que quieran disfrutar de la ciudad a pie.',
      amenities: ['wifi', 'ac', 'kitchen', 'tv']
    },
    {
      id: 3,
      name: 'Villa con piscina privada',
      location: 'Marbella, España',
      bedrooms: 3,
      capacity: 6,
      price: 150,
      size: 120,
      image: '/placeholder.svg?height=300&width=500',
      description: 'Espectacular villa con piscina privada y jardín. Tres dormitorios, amplio salón y cocina totalmente equipada. Perfecta para familias o grupos de amigos.',
      amenities: ['wifi', 'pool', 'parking', 'ac', 'kitchen', 'tv', 'washer']
    },
    {
      id: 4,
      name: 'Apartamento cerca de la playa',
      location: 'Valencia, España',
      bedrooms: 2,
      capacity: 4,
      price: 75,
      size: 70,
      image: '/placeholder.svg?height=300&width=500',
      description: 'Moderno apartamento a solo 200 metros de la playa. Dos dormitorios, salón luminoso y cocina equipada. Ideal para disfrutar de la playa y la ciudad.',
      amenities: ['wifi', 'ac', 'kitchen', 'tv', 'washer']
    },
    {
      id: 5,
      name: 'Estudio en zona céntrica',
      location: 'Madrid, España',
      bedrooms: 1,
      capacity: 2,
      price: 60,
      size: 45,
      image: '/placeholder.svg?height=300&width=500',
      description: 'Estudio moderno y funcional en pleno centro de Madrid. Perfecto para viajeros de negocios o turistas que quieran estar cerca de los principales puntos de interés.',
      amenities: ['wifi', 'ac', 'kitchen', 'tv']
    },
    {
      id: 6,
      name: 'Casa rural con jardín',
      location: 'Granada, España',
      bedrooms: 3,
      capacity: 5,
      price: 95,
      size: 110,
      image: '/placeholder.svg?height=300&width=500',
      description: 'Encantadora casa rural con jardín y barbacoa. Vistas a Sierra Nevada. Ideal para familias que quieran disfrutar de la naturaleza sin renunciar a la cercanía de la ciudad.',
      amenities: ['wifi', 'parking', 'kitchen', 'tv', 'washer']
    }
  ];
  
  isLoading.value = false;
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

const viewProperty = (id) => {
  const property = properties.value.find(p => p.id === id);
  if (property) {
    selectedProperty.value = property;
    activeTab.value = 'property-detail';
    router.push(`/renters/property/${id}`);
  }
};

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

// Mock bookings data
const fetchBookings = async () => {
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  bookings.value = [
    {
      id: 'B1234',
      propertyId: 1,
      property: null, // Will be populated
      checkIn: '2023-08-15',
      checkOut: '2023-08-20',
      guests: 2,
      total: 455,
      status: 'confirmed',
      createdAt: '2023-07-20T10:30:00Z'
    },
    {
      id: 'B5678',
      propertyId: 3,
      property: null, // Will be populated
      checkIn: '2023-09-10',
      checkOut: '2023-09-17',
      guests: 4,
      total: 1080,
      status: 'pending',
      createdAt: '2023-07-25T14:45:00Z'
    },
    {
      id: 'B9012',
      propertyId: 2,
      property: null, // Will be populated
      checkIn: '2023-06-05',
      checkOut: '2023-06-10',
      guests: 2,
      total: 355,
      status: 'completed',
      createdAt: '2023-05-15T09:20:00Z'
    }
  ];
  
  // Populate property data
  bookings.value.forEach(booking => {
    booking.property = properties.value.find(p => p.id === booking.propertyId) || {
      name: 'Propiedad no encontrada',
      image: '/placeholder.svg?height=100&width=100'
    };
  });
};

const bookingFilters = [
  { id: 'all', name: 'Todas' },
  { id: 'pending', name: 'Pendientes' },
  { id: 'confirmed', name: 'Confirmadas' },
  { id: 'completed', name: 'Completadas' },
  { id: 'cancelled', name: 'Canceladas' }
];

const filteredBookings = computed(() => {
  if (activeBookingFilter.value === 'all') {
    return bookings.value;
  }
  return bookings.value.filter(booking => booking.status === activeBookingFilter.value);
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
</script>

<style scoped>
/* Global styles */
.renters-portal {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  color: #333;
  background-color: #f5f5f5;
  min-height: 100vh;
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