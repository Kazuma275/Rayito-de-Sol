<template>
<header class="header">
  <div class="header-container">
    <div class="logo">
      <SunIcon class="logo-icon" />
      
      <!-- Aquí usamos router-link para volver al main -->
      <router-link to="/" class="back-link no-underline">
        <span class="logo-text">Rayito de Sol</span>
      </router-link>

    </div>
    
    <nav class="nav-menu">
      <!-- Aquí usamos router-link para navegación interna -->
      <router-link to="/manage/dashboard" class="nav-link" :class="{ active: activeTab === 'dashboard' }" @click="changeTab('dashboard')">Dashboard</router-link>
      <router-link to="/manage/properties" class="nav-link" :class="{ active: activeTab === 'properties' }" @click="changeTab('properties')">Propiedades</router-link>
      <router-link to="/manage/bookings" class="nav-link" :class="{ active: activeTab === 'bookings' }" @click="changeTab('bookings')">Reservas</router-link>
      <router-link to="/manage/calendar" class="nav-link" :class="{ active: activeTab === 'calendar' }" @click="changeTab('calendar')">Calendario</router-link>
      <router-link to="/manage/messages" class="nav-link" :class="{ active: activeTab === 'messages' }" @click="changeTab('messages')">Mensajes</router-link>
    </nav>
    
    <div class="header-actions">
      <!-- Cambiamos la ruta de ayuda a /manage/help -->
      <router-link to="/manage/help" class="help-button" @click="changeTab('/help')">
        <HelpCircleIcon class="help-icon" />
        <span class="help-link">Ayuda</span>
      </router-link>

      <div class="user-menu">
        <button class="user-button" @click="toggleUserMenu">
          <div v-if="user && user.avatar" class="user-avatar">
            <img :src="user.avatar" alt="User avatar" />
          </div>
          <div v-else class="user-avatar-placeholder">
            {{ user && user.name ? user.name.charAt(0) : 'U' }}
          </div>
          <span class="user-name">{{ user && user.name ? user.name : 'Usuario' }}</span>
          <ChevronDownIcon class="user-icon" />
        </button>
        
        <div v-if="userMenuOpen" class="user-dropdown">
          <router-link to="/manage/settings" href="#" class="dropdown-item" @click.prevent="changeTab('settings')">
            <SettingsIcon class="dropdown-icon" />
            Configuración
          </router-link>
          <router-link to="/" href="#" class="dropdown-item">
            <LogOutIcon class="dropdown-icon" />
            Cerrar sesión
          </router-link>
        </div>
      </div>
    </div>
  </div>
</header>
</template>


<script setup>
import { ref } from 'vue';
import { SunIcon, ChevronDownIcon, SettingsIcon, LogOutIcon, HelpCircleIcon } from 'lucide-vue-next';

const props = defineProps({
user: {
  type: Object,
  default: () => ({
    name: 'Usuario',
    email: 'usuario@example.com',
    avatar: null
  })
},
activeTab: {
  type: String,
  default: 'dashboard'
}
});

const emit = defineEmits(['changeTab']);

const userMenuOpen = ref(false);

const toggleUserMenu = () => {
userMenuOpen.value = !userMenuOpen.value;
};

const changeTab = (tab) => {
emit('changeTab', tab);
if (userMenuOpen.value) {
  userMenuOpen.value = false;
}
};
</script>

<style scoped>
.header {
background-color: var(--primary-color);
color: white;
padding: 1rem 0;
}

.header-container {
display: flex;
align-items: center;
justify-content: space-between;
max-width: 1280px; /* Increased from previous value */
margin: 0 auto;
padding: 0 2rem;
}

.logo {
display: flex;
align-items: center;
}

.logo-icon {
width: 24px;
height: 24px;
color: var(--secondary-color);
margin-right: 0.5rem;
}

.logo-text {
font-size: 1.5rem;
font-weight: 700;
color: white;
}

.no-underline {
text-decoration: none;
}

.nav-menu {
display: flex;
gap: 1.5rem;
}

.nav-link {
color: white;
text-decoration: none;
font-weight: 500;
transition: color 0.3s;
}

.nav-link:hover, .nav-link.active {
color: var(--secondary-color);
}

.header-actions {
display: flex;
align-items: center;
gap: 1.5rem;
}

.help-button {
display: flex;
align-items: center;
background: none;
border: none;
color: white;
cursor: pointer;
}

.help-icon {
width: 18px;
height: 18px;
margin-right: 0.5rem;
}

.help-link {
color: white;
text-decoration: none;
}

.user-menu {
position: relative;
}

.user-button {
display: flex;
align-items: center;
background: none;
border: none;
color: white;
cursor: pointer;
}

.user-avatar {
width: 32px;
height: 32px;
border-radius: 50%;
overflow: hidden;
margin-right: 0.75rem;
}

.user-avatar img {
width: 100%;
height: 100%;
object-fit: cover;
}

.user-avatar-placeholder {
width: 32px;
height: 32px;
border-radius: 50%;
background-color: var(--secondary-color);
color: var(--primary-color);
display: flex;
align-items: center;
justify-content: center;
font-weight: 600;
margin-right: 0.75rem;
}

.user-name {
margin-right: 0.5rem;
}

.user-icon {
width: 16px;
height: 16px;
}

.user-dropdown {
position: absolute;
top: 100%;
right: 0;
margin-top: 0.5rem;
background-color: white;
border-radius: 4px;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
width: 200px;
z-index: 10;
}

.dropdown-item {
display: flex;
align-items: center;
padding: 0.75rem 1rem;
color: var(--text-color);
text-decoration: none;
transition: background-color 0.3s;
}

.dropdown-item:hover {
background-color: var(--light-gray);
}

.dropdown-icon {
width: 16px;
height: 16px;
margin-right: 0.75rem;
}

@media (max-width: 992px) {
.nav-menu {
  display: none;
}
}

@media (max-width: 576px) {
.header-container {
  padding: 0 1rem;
}

.logo-text {
  display: none;
}

.user-name {
  display: none;
}
}
</style>

