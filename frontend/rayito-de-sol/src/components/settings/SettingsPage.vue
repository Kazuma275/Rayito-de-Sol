<template>
  <div>
    <h2 class="section-title">Configuración</h2>
    
    <div class="settings-container">
      <div class="settings-sidebar">
        <div v-for="(setting, index) in settingsTabs" :key="index" 
             class="settings-tab" 
             :class="{ active: activeSettingsTab === setting.id }"
             @click="activeSettingsTab = setting.id">
          <component :is="setting.icon" class="settings-icon" />
          <span>{{ setting.name }}</span>
        </div>
      </div>
      
      <div class="settings-content">
        <ProfileSettings v-if="activeSettingsTab === 'profile'" />
        <NotificationSettings v-if="activeSettingsTab === 'notifications'" />
        <PaymentSettings v-if="activeSettingsTab === 'payment'" />
        <TermsConditions v-if="activeSettingsTab === 'terms'" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { UserIcon, BellIcon, CreditCardIcon, FileTextIcon } from 'lucide-vue-next';
import ProfileSettings from './ProfileSettings.vue';
import NotificationSettings from './NotificationSettings.vue';
import PaymentSettings from './PaymentSettings.vue';
import TermsConditions from './TermsConditions.vue';

const settingsTabs = [
  { id: 'profile', name: 'Perfil', icon: UserIcon },
  { id: 'notifications', name: 'Notificaciones', icon: BellIcon },
  { id: 'payment', name: 'Pagos', icon: CreditCardIcon },
  { id: 'terms', name: 'Términos y Condiciones', icon: FileTextIcon }
];

const activeSettingsTab = ref('profile');
</script>

<style scoped>
.settings-container {
  display: flex;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.settings-sidebar {
  width: 250px;
  border-right: 1px solid #eee;
  padding: 1.5rem 0;
}

.settings-tab {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.settings-tab:hover, .settings-tab.active {
  background-color: #f5f5f5;
}

.settings-tab.active {
  border-left: 3px solid #0071c2;
}

.settings-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.75rem;
}

.settings-content {
  flex: 1;
  padding: 1.5rem;
}

@media (max-width: 768px) {
  .settings-container {
    flex-direction: column;
  }

  .settings-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid #eee;
  }
}
</style>