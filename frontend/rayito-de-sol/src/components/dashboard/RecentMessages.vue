<template>
  <div class="dashboard-card">
    <div class="card-header">
      <h3>Mensajes Recientes</h3>
      <router-link to="/manage/messages" class="view-all-button">Ver todos</router-link>
    </div>
    <div v-if="messages.length > 0" class="messages-list">
      <div v-for="message in messages" :key="message.id" class="message-item">
        <div class="message-sender">
          <UserIcon class="message-icon" />
          <div>
            <h4>{{ message.sender_name }}</h4>
            <p class="message-property">{{ getPropertyById(message.property_id).name }}</p>
          </div>
        </div>
        <p class="message-preview">{{ message.text.substring(0, 60) }}{{ message.text.length > 60 ? '...' : '' }}</p>
        <p class="message-time">{{ formatDateTime(message.created_at) }}</p>
      </div>
    </div>
    <div v-else class="empty-state">
      <MailIcon class="empty-icon" />
      <p>No hay mensajes nuevos</p>
    </div>
  </div>
</template>

<script setup>
import { UserIcon, MailIcon } from 'lucide-vue-next'

defineProps({
  messages: Array,
  getPropertyById: Function,
  formatDateTime: Function
})
</script>