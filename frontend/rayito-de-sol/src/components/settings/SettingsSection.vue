<template>
  <section class="settings-section">
    <h2 class="section-title">Configuración</h2>
    <div class="settings-container">
      <div class="settings-sidebar">
        <div
          v-for="(setting, index) in settingsTabs"
          :key="index"
          class="settings-tab"
          :class="{ active: activeSettingsTab === setting.id }"
          @click="onTab(setting.id)"
        >
          <component :is="setting.icon" class="settings-icon" />
          <span>{{ setting.name }}</span>
        </div>
      </div>
      <div class="settings-content">
        <ProfileSettings
          v-if="activeSettingsTab === 'profile'"
          :profile="profile"
          :preview-image="previewImage"
          @change-avatar="onChangeAvatar"
          @save="onSaveProfile"
        />
        <NotificationSettings
          v-if="activeSettingsTab === 'notifications'"
          :notifications="notifications"
          @save="onSaveNotifications"
        />
        <PaymentSettings
          v-if="activeSettingsTab === 'payment'"
          :payment-methods="paymentMethods"
          :bank-account="bankAccount"
          @add-method="onAddPaymentMethod"
          @edit-method="onEditPaymentMethod"
          @delete-method="onDeletePaymentMethod"
          @save-bank="onSaveBank"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import ProfileSettings from './ProfileSettings.vue'
import NotificationSettings from './NotificationSettings.vue'
import PaymentSettings from './PaymentSettings.vue'
import { UserIcon, BellIcon, CreditCardIcon } from 'lucide-vue-next'

// Props
defineProps({
  settingsTabs: Array,
  activeSettingsTab: String,
  profile: Object,
  previewImage: String,
  notifications: Object,
  paymentMethods: Array,
  bankAccount: Object
})

// Emits
const emit = defineEmits([
  'change-tab',
  'change-avatar',
  'save-profile',
  'save-notifications',
  'add-payment-method',
  'edit-payment-method',
  'delete-payment-method',
  'save-bank'
])

function onTab(tabId) { emit('change-tab', tabId) }
function onChangeAvatar(file) { emit('change-avatar', file) }
function onSaveProfile(profile) { emit('save-profile', profile) }
function onSaveNotifications(notifications) { emit('save-notifications', notifications) }
function onAddPaymentMethod(method) { emit('add-payment-method', method) }
function onEditPaymentMethod(method) { emit('edit-payment-method', method) }
function onDeletePaymentMethod(idx) { emit('delete-payment-method', idx) }
function onSaveBank(bank) { emit('save-bank', bank) }
</script>