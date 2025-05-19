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

// Navigation tabs for mobile footer
const navigationTabs = [
  { id: 'dashboard', name: 'Inicio', path: '/renters/login', icon: HomeIcon },
  { id: 'search', name: 'Buscar', path: '/renters/login', icon: SearchIcon },
  { id: 'bookings', name: 'Reservas', path: '/renters/login', icon: CalendarIcon },
  { id: 'favorites', name: 'Favoritos', path: '/renters/login', icon: HeartIcon },
  { id: 'messages', name: 'Mensajes', path: '/renters/login', icon: MessageSquareIcon }
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
  background-color: white;
  box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 100;
}

.footer-container {
  padding: 0.5rem;
}

.footer-nav {
  display: flex;
  justify-content: space-around;
}

.footer-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: none;
  border: none;
  padding: 0.5rem;
  color: #666;
  cursor: pointer;
  flex: 1;
  text-decoration: none;
}

.footer-tab.active {
  color: var(--primary-color, #003580);
}

.tab-icon {
  width: 24px;
  height: 24px;
  margin-bottom: 0.25rem;
}

.tab-text {
  font-size: 0.7rem;
}

@media (max-width: 768px) {
  .portal-footer {
    display: block;
  }
  
  /* Add padding to main content to account for fixed footer */
  :deep(.main-content) {
    padding-bottom: 70px;
  }
}
</style>
