<template>
  <div class="messages-content">
    <div v-if="conversation" class="conversation-view">
      <div class="conversation-header">
        <div class="conversation-user">
          <UserIcon class="user-icon" />
          <div>
            <h3>{{ conversation.name }}</h3>
            <p class="conversation-booking">Reserva #{{ conversation.bookingId }}</p>
          </div>
        </div>
        <div class="conversation-property">
          {{ getPropertyById(conversation.propertyId)?.name }}
        </div>
      </div>
      <div class="message-list">
        <MessageBubble
          v-for="(message, index) in conversation.messages"
          :key="index"
          :message="message"
        />
      </div>
      <MessageInput @send="onSend" />
    </div>
    <div v-else class="empty-conversation">
      <MessageSquareIcon class="empty-icon" />
      <h3>Selecciona una conversación</h3>
      <p>Elige una conversación de la lista para ver los mensajes</p>
    </div>
  </div>
</template>

<script setup>
import { UserIcon, MessageSquareIcon } from 'lucide-vue-next'
import MessageBubble from './MessageBubble.vue'
import MessageInput from './MessageInput.vue'

const props = defineProps({
  conversation: Object,
  getPropertyById: Function
})

const emit = defineEmits(['send'])

function onSend(text) {
  emit('send', text)
}
</script>