<template>
  <header class="header">
    <div class="header-container">
      <div class="logo-container">
        <SunIcon class="logo-icon" />
        <router-link to="/" class="back-link">
          <span class="logo-text">Rayito de Sol</span>
        </router-link>
      </div>
      
      <nav class="nav-menu">
        <router-link to="/manage/dashboard" class="nav-link" :class="{ active: activeTab === 'dashboard' }" @click="changeTab('dashboard')">Dashboard</router-link>
        <router-link to="/manage/properties" class="nav-link" :class="{ active: activeTab === 'properties' }" @click="changeTab('properties')">Propiedades</router-link>
        <router-link to="/manage/bookings" class="nav-link" :class="{ active: activeTab === 'bookings' }" @click="changeTab('bookings')">Reservas</router-link>
        <router-link to="/manage/calendar" class="nav-link" :class="{ active: activeTab === 'calendar' }" @click="changeTab('calendar')">Calendario</router-link>
        <router-link to="/manage/messages" class="nav-link" :class="{ active: activeTab === 'messages' }" @click="changeTab('messages')">Mensajes</router-link>
      </nav>
      
      <div class="header-actions">
        <router-link to="/manage/help" class="help-button" @click="changeTab('/help')">
          <HelpCircleIcon class="help-icon" />
          <span class="help-text">Ayuda</span>
        </router-link>
        
        <div class="user-menu">
          <button class="user-button" @click="toggleUserMenu">
            <div v-if="user && user.avatar" class="user-avatar">
              <img :src="user.avatar" alt="User avatar" />
            </div>
            <div v-else class="user-avatar-placeholder">
              {{ user && user.username ? user.username.charAt(0).toUpperCase() : 'U' }}
            </div>
            <span class="user-name">{{ user && user.username ? user.username : 'Usuario' }}</span>
            <ChevronDownIcon class="user-icon" :class="{ 'rotate': userMenuOpen }" />
          </button>
          
          <div v-if="userMenuOpen" class="user-dropdown">
            <router-link to="/manage/settings" class="dropdown-item" @click.prevent="changeTab('settings')">
              <SettingsIcon class="dropdown-icon" />
              <span>Configuración</span>
            </router-link>
            <div class="dropdown-divider"></div>
            <button @click="logout" class="dropdown-item logout">
              <LogOutIcon class="dropdown-icon" />
              <span>Cerrar sesión</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useUserStore } from '../../../stores/user';
import { SunIcon, ChevronDownIcon, SettingsIcon, LogOutIcon, HelpCircleIcon } from 'lucide-vue-next';

const userStore = useUserStore();
const user = computed(() => userStore.user);

const props = defineProps({
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

const logout = () => {
  userStore.logout();
  window.location.href = '/login';
};
</script>

<style scoped>
.header {
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  padding: 1rem 0;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.15);
  position: relative;
  overflow: visible;
  z-index: 50; /* Increased z-index to ensure header stays above other content */
}

.header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(0, 113, 194, 0.4) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(0, 53, 128, 0.3) 0%, transparent 60%);
  z-index: 0;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  position: relative;
  z-index: 1;
}

.logo-container {
  display: flex;
  align-items: center;
  transition: transform 0.3s ease;
  cursor: pointer;
}

.logo-container:hover {
  transform: translateY(-2px);
}

.logo-icon {
  width: 28px;
  height: 28px;
  color: #feba02;
  filter: drop-shadow(0 0 8px rgba(254, 186, 2, 0.6));
  margin-right: 0.75rem;
  animation: pulse 3s infinite ease-in-out;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.back-link {
  text-decoration: none;
}

.nav-menu {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  transition: all 0.3s ease;
  position: relative;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 2px;
  background-color: #feba02;
  transform: translateX(-50%);
  transition: width 0.3s ease;
}

.nav-link:hover {
  color: white;
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-link:hover::after {
  width: 80%;
}

.nav-link.active {
  color: white;
  background-color: rgba(255, 255, 255, 0.15);
}

.nav-link.active::after {
  width: 80%;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.help-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.help-button:hover {
  color: white;
  background-color: rgba(255, 255, 255, 0.1);
}

.help-icon {
  width: 18px;
  height: 18px;
}

.help-text {
  font-weight: 500;
}

.user-menu {
  position: relative;
  z-index: 100; /* Higher z-index for the user menu */
}

.user-button {
  display: flex;
  align-items: center;
  background-color: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.user-button:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 0.75rem;
  border: 2px solid rgba(254, 186, 2, 0.6);
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
  background-color: #feba02;
  color: #003580;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-right: 0.75rem;
  border: 2px solid rgba(254, 186, 2, 0.6);
}

.user-name {
  margin-right: 0.5rem;
  font-weight: 500;
}

.user-icon {
  width: 16px;
  height: 16px;
  transition: transform 0.3s ease;
}

.user-icon.rotate {
  transform: rotate(180deg);
}

.user-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  width: 220px;
  overflow: hidden;
  z-index: 1000; /* Very high z-index to ensure it's above all other content */
  animation: fadeInDown 0.3s ease;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  color: #333;
  text-decoration: none;
  transition: background-color 0.3s;
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-size: 1rem;
}

.dropdown-item:hover {
  background-color: #f5f9ff;
}

.dropdown-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.75rem;
  color: #0071c2;
}

.dropdown-divider {
  height: 1px;
  background-color: #eee;
  margin: 0.25rem 0;
}

.dropdown-item.logout {
  color: #e41c00;
}

.dropdown-item.logout .dropdown-icon {
  color: #e41c00;
}

.dropdown-item.logout:hover {
  background-color: #fff2f0;
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
  
  .help-text {
    display: none;
  }
}
</style>