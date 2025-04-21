<template>
  <div class="conversation-view">
    <div class="conversation-header">
      <div class="conversation-user">
        <UserIcon class="user-icon" />
        <div>
          <h3>{{ conversation.name }}</h3>
          <p class="conversation-booking">Reserva #{{ conversation.bookingId }}</p>
        </div>
      </div>
      <div class="conversation-property">
        {{ property.name }}
      </div>
    </div>
    
    <div class="message-list">
      <div v-for="(message, index) in conversation.messages" :key="index" 
           class="message-bubble" 
           :class="{ 'message-sent': message.sent, 'message-received': !message.sent }">
        <div class="message-content">{{ message.text }}</div>
        <div class="message-time">{{ message.time }}</div>
      </div>
    </div>
    
    <div class="message-input">
      <textarea v-model="newMessage" placeholder="Escribe un mensaje..." class="input-textarea" rows="3"></textarea>
      <button class="send-button" @click="sendMessage">
        <SendIcon class="send-icon" />
        Enviar
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { UserIcon, SendIcon } from 'lucide-vue-next';

defineProps({
  conversation: {
    type: Object,
    required: true
  },
  property: {
    type: Object,
    required: true
  }
});

const newMessage = ref('');

const sendMessage = () => {
  if (newMessage.value.trim() === '') return;
  
  // Here you would typically send the message to your backend
  console.log('Sending message:', newMessage.value);
  
  // Clear the input
  newMessage.value = '';
};
</script>

<style scoped>
.conversation-view {
  display: flex;
  flex-direction: column;
  height: 100%;
  flex: 1;
}

.conversation-header {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.conversation-user {
  display: flex;
  align-items: center;
}

.user-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-right: 0.75rem;
}

.conversation-user h3 {
  font-size: 1.1rem;
  margin: 0 0 0.25rem;
}

.conversation-booking {
  font-size: 0.8rem;
  color: #666;
  margin: 0;
}

.conversation-property {
  font-size: 0.9rem;
  color: #0071c2;
}

.message-list {
  flex: 1;
  padding: 1rem;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message-bubble {
  max-width: 70%;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  position: relative;
}

.message-sent {
  align-self: flex-end;
  background-color: #e6f0ff;
  color: #0071c2;
}

.message-received {
  align-self: flex-start;
  background-color: #f5f5f5;
  color: #333;
}

.message-content {
  margin-bottom: 0.5rem;
}

.message-time {
  font-size: 0.8rem;
  color: #666;
  text-align: right;
}

.message-input {
  padding: 1rem;
  border-top: 1px solid #eee;
  display: flex;
  gap: 1rem;
}

.input-textarea {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.9rem;
  font-family: inherit;
  resize: none;
}

.send-button {
  display: flex;
  align-items: center;
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.send-button:hover {
  background-color: #005999;
}

.send-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}
</style>