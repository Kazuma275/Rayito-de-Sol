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
  background-color: #f5f5f5;
  background-image: linear-gradient(to bottom, #f0f7ff, #f5f9ff);
}

.main-content {
  flex: 1;
}

/* CSS Variables for theming */
:root {
  --primary-color: #003580;
  --primary-light: #0071c2;
  --secondary-color: #feba02;
  --text-color: #333;
  --text-light: #64748b;
  --border-color: #e6f0ff;
  --background-color: #f5f9ff;
  --card-background: white;
  --success-color: #00703c;
  --warning-color: #b27b00;
  --error-color: #e41c00;
}
</style>