<template>
  <div class="messages-container">
    <h1 class="messages-title">Mis Mensajes</h1>
    
    <div class="messages-content">
      <div class="empty-state" v-if="!hasMessages">
        <MessageSquareIcon class="empty-icon" />
        <p>No tienes mensajes</p>
        <a href="/renters/bookings" class="action-button">Ver mis reservas</a>
      </div>
      
      <div v-else class="messages-list">
        <!-- Aquí irían los mensajes -->
        <div class="message-placeholder" v-for="i in 5" :key="i">
          <div class="message-avatar"></div>
          <div class="message-content">
            <div class="message-header">
              <div class="message-sender"></div>
              <div class="message-date"></div>
            </div>
            <div class="message-preview"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { MessageSquareIcon } from 'lucide-vue-next';

const hasMessages = ref(false); // Cambiar a true cuando haya mensajes
</script>

<style scoped>
.messages-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.messages-title {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin-bottom: 2rem;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  text-align: center;
  color: #666;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #ccc;
  margin-bottom: 1rem;
}

.action-button {
  margin-top: 1rem;
  background-color: var(--primary-color, #003580);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.3s;
}

.action-button:hover {
  background-color: var(--primary-light, #0071c2);
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message-placeholder {
  display: flex;
  background-color: white;
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  animation: pulse 1.5s infinite alternate;
}

.message-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #f5f5f5;
  margin-right: 1rem;
  flex-shrink: 0;
}

.message-content {
  flex: 1;
}

.message-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.message-sender {
  height: 20px;
  width: 40%;
  background-color: #f5f5f5;
  border-radius: 4px;
}

.message-date {
  height: 16px;
  width: 20%;
  background-color: #f5f5f5;
  border-radius: 4px;
}

.message-preview {
  height: 16px;
  width: 90%;
  background-color: #f5f5f5;
  border-radius: 4px;
}

@keyframes pulse {
  0% {
    opacity: 0.6;
  }
  100% {
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .messages-container {
    padding: 1rem;
  }
}
</style>
