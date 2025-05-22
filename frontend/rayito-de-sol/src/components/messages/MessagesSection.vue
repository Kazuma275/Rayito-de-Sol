<template>
  <section class="messages-section">
    <h2 class="section-title">Mensajes</h2>
    <div class="messages-container">
      <MessagesSidebar
        :conversations="conversations"
        :active-conversation="activeConversation"
        @select="onSelectConversation"
        :get-property-by-id="getPropertyById"
      />
      <MessagesContent
        :conversation="activeConversationObj"
        :get-property-by-id="getPropertyById"
        @send="onSendMessage"
      />
    </div>
  </section>
</template>

<script setup>
import MessagesSidebar from './MessagesSidebar.vue'
import MessagesContent from './MessagesContent.vue'

// Props
defineProps({
  conversations: Array,
  activeConversation: [Number, null],
  getPropertyById: Function
})

// Emits
const emit = defineEmits(['select-conversation', 'send-message'])

// Computed para obtener la conversación activa
import { computed } from 'vue'
const activeConversationObj = computed(() => 
  props.activeConversation !== null
    ? props.conversations[props.activeConversation]
    : null
)

// Métodos para emitir eventos
function onSelectConversation(idx) {
  emit('select-conversation', idx)
}
function onSendMessage(message) {
  emit('send-message', message)
}
</script>