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
.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 2rem;
  position: relative;
  z-index: 1;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.settings-container {
  display: flex;
  background-color: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.settings-sidebar {
  width: 250px;
  border-right: 1px solid #e6f0ff;
  padding: 1.5rem 0;
  background-color: #f8fafc;
}

.settings-tab {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #64748b;
  border-left: 3px solid transparent;
}

.settings-tab:hover {
  background-color: #f0f7ff;
  color: #0071c2;
}

.settings-tab.active {
  background-color: #e6f0ff;
  color: #0071c2;
  border-left: 3px solid #0071c2;
  font-weight: 500;
}

.settings-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.75rem;
}

.settings-content {
  flex: 1;
  padding: 2rem;
}

@media (max-width: 768px) {
  .settings-container {
    flex-direction: column;
  }

  .settings-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid #e6f0ff;
    padding: 1rem 0;
  }
  
  .settings-tab {
    padding: 0.75rem 1rem;
  }
  
  .settings-content {
    padding: 1.5rem;
  }
}
</style>