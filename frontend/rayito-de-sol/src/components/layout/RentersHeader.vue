<template>
  <header class="renters-header">
    <div class="header-container">
      <div class="logo-container" @click="goToHome">
        <div class="logo-icon-wrapper">
          <SunIcon class="logo-icon" />
          <div class="logo-icon-glow"></div>
        </div>
        <h1 class="logo-text">Rayito de Sol</h1>
      </div>
      
      <div class="desktop-nav">
        <nav class="main-nav">
          <router-link 
            v-for="item in navItems" 
            :key="item.path" 
            :to="item.path" 
            class="nav-link"
            :class="{ active: isActive(item.path) }"
          >
            <component :is="item.icon" class="nav-icon" />
            <span>{{ item.label }}</span>
          </router-link>
        </nav>
        
        <div class="user-menu">
          <button class="user-button" @click="toggleUserMenu">
            <div v-if="user && user.avatar" class="profile-pic">
              <img :src="user.avatar" alt="Foto de perfil" />
            </div>
            <div v-else class="profile-pic-placeholder">
              {{ user && user.username ? user.username.charAt(0).toUpperCase() : 'U' }}
            </div>
            <span class="user-name">{{ user && user.username ? user.username : 'Usuario' }}</span>
            <ChevronDownIcon class="chevron-icon" :class="{ 'rotate': userMenuOpen }" />
          </button>
          
          <div class="dropdown-menu" v-if="userMenuOpen">
            <router-link to="/renters/profile" class="dropdown-item" @click="closeMenus">
              <SettingsIcon class="dropdown-icon" />
              <span>Configuración</span>
            </router-link>
            <div class="dropdown-divider"></div>
            <button @click="logout" class="dropdown-item">
              <LogOutIcon class="dropdown-icon" />
              <span>Cerrar Sesión</span>
            </button>
          </div>
        </div>
      </div>
      
      <button class="mobile-menu-button" @click="toggleMobileMenu">
        <MenuIcon v-if="!mobileMenuOpen" class="menu-icon" />
        <XIcon v-else class="menu-icon" />
      </button>
    </div>
    
    <div class="mobile-menu" v-if="mobileMenuOpen">
      <router-link 
        v-for="item in navItems" 
        :key="item.path" 
        :to="item.path" 
        class="mobile-nav-link"
        :class="{ active: isActive(item.path) }"
        @click="mobileMenuOpen = false"
      >
        <component :is="item.icon" class="nav-icon" />
        <span>{{ item.label }}</span>
      </router-link>
      
      <div class="mobile-divider"></div>
      
      <router-link to="/renters/profile" class="mobile-nav-link" @click="mobileMenuOpen = false">
        <UserIcon class="nav-icon" />
        <span>Mi Perfil</span>
      </router-link>
      
      <router-link to="/renters/settings" class="mobile-nav-link" @click="mobileMenuOpen = false">
        <SettingsIcon class="nav-icon" />
        <span>Configuración</span>
      </router-link>
      
      <button @click="logout" class="mobile-nav-link logout">
        <LogOutIcon class="nav-icon" />
        <span>Cerrar Sesión</span>
      </button>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { 
  SunIcon, 
  HomeIcon, 
  SearchIcon, 
  CalendarIcon, 
  MessageSquareIcon, 
  HeartIcon,
  UserIcon, 
  SettingsIcon, 
  LogOutIcon,
  MenuIcon,
  XIcon,
  ChevronDownIcon
} from 'lucide-vue-next';

import { useUserStore } from '@/stores/userStore';

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();
const user = computed(() => userStore.user);

const userMenuOpen = ref(false);
const mobileMenuOpen = ref(false);

const navItems = ref([
  { label: 'Inicio', path: '/renters/dashboard', icon: HomeIcon },
  { label: 'Buscar', path: '/renters/search', icon: SearchIcon },
  { label: 'Mis Reservas', path: '/renters/bookings', icon: CalendarIcon },
  { label: 'Mensajes', path: '/renters/messages', icon: MessageSquareIcon },
  { label: 'Favoritos', path: '/renters/favorites', icon: HeartIcon }
]);

const isActive = (path) => {
  return route.path === path || route.path.startsWith(`${path}/`);
};

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value;
  if (userMenuOpen.value) {
    mobileMenuOpen.value = false;
  }
};

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
  if (mobileMenuOpen.value) {
    userMenuOpen.value = false;
  }
};

const goToHome = () => {
  router.push('/portal/renters/dashboard');
};

const logout = () => {
  userStore.logout();
  router.replace('/');
};

const closeMenus = () => {
  userMenuOpen.value = false;
  mobileMenuOpen.value = false;
};

onMounted(() => {
  userStore.hydrate?.();
  window.addEventListener('click', (event) => {
    if (!event.target.closest('.user-menu') && !event.target.closest('.mobile-menu-button')) {
      closeMenus();
    }
  });
  window.addEventListener('storage', (event) => {
    if (event.key === 'auth_user' || event.key === 'auth_token') {
      userStore.hydrate?.();
    }
  });
});
</script>

<style scoped>
.renters-header {
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
  position: sticky;
  top: 0;
  z-index: 100;
  padding: 1rem 0;
}

.profile-pic {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 0.5rem;
  background: #fbbf24;
  border: 2px solid #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}
.profile-pic img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.profile-pic-placeholder {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #fde68a;
  color: #f59e0b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
  margin-right: 0.5rem;
  border: 2px solid #fff;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.logo-container {
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.logo-container:hover {
  transform: translateY(-2px);
}

.logo-icon-wrapper {
  position: relative;
  margin-right: 1rem;
}

.logo-icon {
  width: 32px;
  height: 32px;
  color: #ffffff;
  filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.7));
  position: relative;
  z-index: 1;
  animation: pulse 3s infinite ease-in-out;
}

.logo-icon-glow {
  position: absolute;
  width: 32px;
  height: 32px;
  top: 0;
  left: 0;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 70%);
  border-radius: 50%;
  animation: glow 3s infinite ease-in-out;
  z-index: 0;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.desktop-nav {
  display: flex;
  align-items: center;
}

.main-nav {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  display: flex;
  align-items: center;
  color: rgba(0, 0, 0, 0.7);
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
}

.nav-link:hover, .nav-link.active {
  color: #1e293b;
  background-color: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.nav-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

.user-menu {
  position: relative;
  margin-left: 2rem;
}

.user-button {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.3);
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  color: #1e293b;
  transition: all 0.3s ease;
}

.user-button:hover {
  background: rgba(255, 255, 255, 0.4);
  transform: translateY(-2px);
}

.user-icon {
  width: 20px;
  height: 20px;
  margin-right: 0.5rem;
  color: #1e293b;
}

.user-name {
  margin-right: 0.5rem;
  font-weight: 500;
}

.chevron-icon {
  width: 16px;
  height: 16px;
  transition: transform 0.3s;
  color: #1e293b;
}

.chevron-icon.rotate {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  width: 220px;
  padding: 0.5rem 0;
  margin-top: 0.5rem;
  z-index: 10;
  transform: translateY(10px);
  opacity: 0;
  animation: fadeInUp 0.3s ease forwards;
  overflow: hidden;
}

@keyframes fadeInUp {
  to {
    transform: translateY(0);
    opacity: 1;
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
  background-color: #f5f5f5;
  color: #f59e0b;
}

.dropdown-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.75rem;
  color: #f59e0b;
}

.dropdown-divider {
  height: 1px;
  background-color: #eee;
  margin: 0.5rem 0;
}

.mobile-menu-button {
  display: none;
  background: rgba(255, 255, 255, 0.3);
  border: none;
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  color: #1e293b;
  transition: all 0.3s ease;
}

.mobile-menu-button:hover {
  background: rgba(255, 255, 255, 0.4);
}

.menu-icon {
  width: 24px;
  height: 24px;
  color: #1e293b;
}

.mobile-menu {
  display: none;
  flex-direction: column;
  padding: 1rem;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  padding: 1rem;
  color: rgba(0, 0, 0, 0.7);
  text-decoration: none;
  transition: all 0.3s ease;
  border-radius: 8px;
}

.mobile-nav-link:hover, .mobile-nav-link.active {
  background-color: rgba(255, 255, 255, 0.3);
  color: #1e293b;
}

.mobile-divider {
  height: 1px;
  background-color: rgba(255, 255, 255, 0.3);
  margin: 0.5rem 0;
}

.logout {
  color: #7c2d12;
  background: none;
  border: none;
  font-size: 1rem;
  text-align: left;
  width: 100%;
  cursor: pointer;
}

.logout:hover {
  background-color: rgba(255, 255, 255, 0.3);
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

@media (max-width: 768px) {
  .header-container {
    padding: 0.75rem 1rem;
  }
  
  .desktop-nav {
    display: none;
  }
  
  .mobile-menu-button {
    display: block;
  }
  
  .mobile-menu {
    display: flex;
  }
  
  .logo-text {
    font-size: 1.25rem;
  }
}

@media (max-width: 480px) {
  .logo-text {
    display: none;
  }
  
  .logo-icon {
    width: 28px;
    height: 28px;
  }
}
</style>