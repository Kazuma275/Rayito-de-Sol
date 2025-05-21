<template>
  <div class="renters-portal">
    <RentersHeader v-if="isAuthenticated && !isLoginPage" />
    
    <main class="main-content">
      <router-view />
    </main>
    
    <RentersFooter 
      v-if="isAuthenticated && !isLoginPage" 
      :activeTab="activeTab"
      @changeTab="handleTabChange"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import RentersHeader from '@/components/layout/RentersHeader.vue';
import RentersFooter from '@/components/layout/RentersFooter.vue';

// Active tab for the footer navigation
const activeTab = ref('dashboard');

// Get current route
const route = useRoute();

// Check if user is on login page
const isLoginPage = computed(() => {
  return route.path === '/renters/login' || route.path === '/renters/register';
});

// In a real app, this would come from your auth store
const isAuthenticated = computed(() => {
  // For demo purposes, consider user authenticated if not on login/register page
  return !isLoginPage.value || localStorage.getItem('renters_auth') === 'true';
});

// Handle tab change from footer
const handleTabChange = (tabId) => {
  activeTab.value = tabId;
};
</script>

<style scoped>
.renters-portal {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%);
  position: relative;
  overflow: hidden;
}

.renters-portal::before {
  content: '';
  position: absolute;
  width: 200%;
  height: 200%;
  top: -50%;
  left: -50%;
  background: radial-gradient(circle at center, rgba(251, 191, 36, 0.1) 0%, transparent 70%),
              radial-gradient(circle at 30% 70%, rgba(245, 158, 11, 0.1) 0%, transparent 60%);
  animation: rotate 30s linear infinite;
  z-index: 0;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.main-content {
  flex: 1;
  position: relative;
  z-index: 1;
  padding: 1rem;
  max-width: 1280px;
  margin: 0 auto;
  width: 100%;
}

/* CSS Variables for theming - Updated to match golden/yellow theme */
:root {
  --primary-color: #f59e0b;
  --primary-light: #fbbf24;
  --secondary-color: #facc15;
  --text-color: #1e293b;
  --text-light: #64748b;
  --border-color: rgba(251, 191, 36, 0.2);
  --background-color: #f8fafc;
  --card-background: rgba(255, 255, 255, 0.9);
  --success-color: #10b981;
  --warning-color: #f59e0b;
  --error-color: #ef4444;
  --button-gradient: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  --button-hover-gradient: linear-gradient(135deg, #f59e0b 0%, #fcd34d 100%);
}

/* Add glow effects for buttons and interactive elements */
.glow-effect {
  position: relative;
  overflow: hidden;
}

.glow-effect::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transform: translateX(-100%);
  transition: transform 0s;
}

.glow-effect:hover::before {
  transform: translateX(100%);
  transition: transform 0.6s ease;
}

/* Animation for elements */
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

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .main-content {
    padding: 1rem 0.5rem;
    padding-bottom: 70px; /* Space for mobile footer */
  }
}
</style>