<template>
  <header class="renters-header">
    <div class="header-container">
      <div class="logo-container" @click="goToHome">
        <SunIcon class="logo-icon" />
        <h1 class="logo-text">Rayito de Sol</h1>
      </div>
      
      <div class="desktop-nav">
        <nav class="main-nav">
          <a 
            v-for="item in navItems" 
            :key="item.path" 
            :href="item.path" 
            class="nav-link"
            :class="{ active: isActive(item.path) }"
          >
            <component :is="item.icon" class="nav-icon" />
            <span>{{ item.label }}</span>
          </a>
        </nav>
        
        <div class="user-menu">
          <button class="user-button" @click="toggleUserMenu">
            <UserIcon class="user-icon" />
            <span class="user-name">{{ userName }}</span>
            <ChevronDownIcon class="chevron-icon" :class="{ 'rotate': userMenuOpen }" />
          </button>
          
          <div class="dropdown-menu" v-if="userMenuOpen">
            <a href="/renters/profile" class="dropdown-item">
              <UserIcon class="dropdown-icon" />
              <span>Mi Perfil</span>
            </a>
            <a href="/renters/settings" class="dropdown-item">
              <SettingsIcon class="dropdown-icon" />
              <span>Configuración</span>
            </a>
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
      <a 
        v-for="item in navItems" 
        :key="item.path" 
        :href="item.path" 
        class="mobile-nav-link"
        :class="{ active: isActive(item.path) }"
        @click="mobileMenuOpen = false"
      >
        <component :is="item.icon" class="nav-icon" />
        <span>{{ item.label }}</span>
      </a>
      
      <div class="mobile-divider"></div>
      
      <a href="/renters/profile" class="mobile-nav-link" @click="mobileMenuOpen = false">
        <UserIcon class="nav-icon" />
        <span>Mi Perfil</span>
      </a>
      
      <a href="/renters/settings" class="mobile-nav-link" @click="mobileMenuOpen = false">
        <SettingsIcon class="nav-icon" />
        <span>Configuración</span>
      </a>
      
      <button @click="logout" class="mobile-nav-link logout">
        <LogOutIcon class="nav-icon" />
        <span>Cerrar Sesión</span>
      </button>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
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

const router = useRouter();
const route = useRoute();

// User state (would come from auth store in a real app)
const userName = ref('Usuario');
const userMenuOpen = ref(false);
const mobileMenuOpen = ref(false);

// Navigation items
const navItems = ref([
  { label: 'Inicio', path: '/renters/login', icon: HomeIcon },
  { label: 'Buscar', path: '/renters/login', icon: SearchIcon },
  { label: 'Mis Reservas', path: '/renters/login', icon: CalendarIcon },
  { label: 'Mensajes', path: '/renters/login', icon: MessageSquareIcon },
  { label: 'Favoritos', path: '/renters/login', icon: HeartIcon }
]);

// Check if a route is active
const isActive = (path) => {
  return route.path === path || route.path.startsWith(`${path}/`);
};

// Toggle user dropdown menu
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value;
  if (userMenuOpen.value) {
    mobileMenuOpen.value = false;
  }
};

// Toggle mobile menu
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
  if (mobileMenuOpen.value) {
    userMenuOpen.value = false;
  }
};

// Go to home page
const goToHome = () => {
  window.location.href = '/renters/login';
};

// Logout function
const logout = () => {
  // In a real app, this would call your auth service
  userMenuOpen.value = false;
  mobileMenuOpen.value = false;
  window.location.href = '/renters/login';
};

// Close menus when clicking outside
const closeMenus = (event) => {
  if (!event.target.closest('.user-menu') && !event.target.closest('.mobile-menu-button')) {
    userMenuOpen.value = false;
    mobileMenuOpen.value = false;
  }
};

// Add and remove event listeners
if (typeof window !== 'undefined') {
  window.addEventListener('click', closeMenus);
  
  // Clean up on component unmount
  onUnmounted(() => {
    window.removeEventListener('click', closeMenus);
  });
}
</script>

<style scoped>
.renters-header {
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.logo-container {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.logo-icon {
  width: 28px;
  height: 28px;
  color: var(--secondary-color, #feba02);
  margin-right: 0.5rem;
}

.logo-text {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-color, #003580);
  margin: 0;
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
  color: #666;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-link:hover, .nav-link.active {
  color: var(--primary-color, #003580);
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
  background: none;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  color: #666;
  transition: color 0.3s;
}

.user-button:hover {
  color: var(--primary-color, #003580);
}

.user-icon {
  width: 20px;
  height: 20px;
  margin-right: 0.5rem;
}

.user-name {
  margin-right: 0.5rem;
  font-weight: 500;
}

.chevron-icon {
  width: 16px;
  height: 16px;
  transition: transform 0.3s;
}

.chevron-icon.rotate {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  border-radius: 4px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  width: 200px;
  padding: 0.5rem 0;
  margin-top: 0.5rem;
  z-index: 10;
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  color: #666;
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
  color: var(--primary-color, #003580);
}

.dropdown-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.75rem;
}

.dropdown-divider {
  height: 1px;
  background-color: #eee;
  margin: 0.5rem 0;
}

.mobile-menu-button {
  display: none;
  background: none;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  color: #666;
}

.menu-icon {
  width: 24px;
  height: 24px;
}

.mobile-menu {
  display: none;
  flex-direction: column;
  padding: 1rem;
  background-color: white;
  border-top: 1px solid #eee;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  padding: 1rem;
  color: #666;
  text-decoration: none;
  transition: background-color 0.3s;
  border-radius: 4px;
}

.mobile-nav-link:hover, .mobile-nav-link.active {
  background-color: #f5f5f5;
  color: var(--primary-color, #003580);
}

.mobile-divider {
  height: 1px;
  background-color: #eee;
  margin: 0.5rem 0;
}

.logout {
  color: #e41c00;
  background: none;
  border: none;
  font-size: 1rem;
  text-align: left;
  width: 100%;
  cursor: pointer;
}

@media (max-width: 768px) {
  .header-container {
    padding: 1rem;
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
}
</style>
