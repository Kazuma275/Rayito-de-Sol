<template>
  <footer class="portal-footer">
    <div class="footer-container">
      <div class="footer-nav">
        <a 
          v-for="tab in navigationTabs" 
          :key="tab.id" 
          :href="tab.path" 
          class="footer-tab" 
          :class="{ active: activeTab === tab.id }"
        >
          <component :is="tab.icon" class="tab-icon" />
          <span class="tab-text">{{ tab.name }}</span>
        </a>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { 
  HomeIcon, 
  SearchIcon, 
  CalendarIcon, 
  HeartIcon, 
  MessageSquareIcon 
} from 'lucide-vue-next';
import { ref } from 'vue';

// Navigation tabs for mobile footer - ACTUALIZADO para apuntar al nuevo portal
const navigationTabs = [
  { id: 'dashboard', name: 'Inicio', path: '/renters/dashboard', icon: HomeIcon },
  { id: 'search', name: 'Buscar', path: '/renters/search', icon: SearchIcon },
  { id: 'bookings', name: 'Reservas', path: '/renters/bookings', icon: CalendarIcon },
  { id: 'favorites', name: 'Favoritos', path: '/renters/favorites', icon: HeartIcon },
  { id: 'messages', name: 'Mensajes', path: '/renters/messages', icon: MessageSquareIcon }
];

const props = defineProps({
  activeTab: {
    type: String,
    default: 'dashboard'
  }
});

const emit = defineEmits(['changeTab']);
</script>

<style scoped>
.portal-footer {
  display: none;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  box-shadow: 0 -1px 4px rgba(245, 158, 11, 0.15);
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 100;
  height: 56px; /* Reduced height */
  overflow: hidden;
}

.portal-footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(251, 191, 36, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(245, 158, 11, 0.2) 0%, transparent 60%);
  z-index: 0;
}

.footer-container {
  height: 100%;
  position: relative;
  z-index: 1;
}

.footer-nav {
  display: flex;
  justify-content: space-around;
  height: 100%;
}

.footer-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: none;
  border: none;
  padding: 0;
  color: rgba(31, 41, 55, 0.7);
  cursor: pointer;
  flex: 1;
  text-decoration: none;
  transition: all 0.2s ease;
  position: relative;
  height: 100%;
}

.footer-tab::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 0;
  background-color: rgba(255, 255, 255, 0.15);
  transition: height 0.2s ease;
  z-index: -1;
}

.footer-tab:hover::before {
  height: 100%;
}

.footer-tab.active {
  color: #1e293b;
  font-weight: 500;
}

.footer-tab.active::after {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 30%;
  height: 3px;
  background-color: #1e293b;
  border-radius: 0 0 3px 3px;
}

.tab-icon {
  width: 20px; /* Smaller icons */
  height: 20px;
  margin-bottom: 2px; /* Reduced margin */
  transition: transform 0.2s ease;
}

.footer-tab:hover .tab-icon {
  transform: translateY(-1px);
}

.tab-text {
  font-size: 0.65rem; /* Smaller text */
  transition: transform 0.2s ease;
  white-space: nowrap;
}

@media (max-width: 768px) {
  .portal-footer {
    display: block;
  }
  
  /* Add padding to main content to account for fixed footer */
  :deep(.main-content) {
    padding-bottom: 56px; /* Match footer height */
  }
}

@media (max-width: 360px) {
  .tab-text {
    font-size: 0.6rem;
  }
  
  .tab-icon {
    width: 18px;
    height: 18px;
  }
}
</style>