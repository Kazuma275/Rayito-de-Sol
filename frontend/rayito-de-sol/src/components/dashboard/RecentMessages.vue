<template>
  <div class="dashboard-card">
    <div class="card-header">
      <h3>Mensajes Recientes</h3>
      <button class="view-all-button">Ver todos</button>
    </div>
    <div v-if="messages.length > 0" class="messages-list">
      <div v-for="(message, index) in messages" :key="index" class="message-item">
        <div class="message-sender">
          <UserIcon class="message-icon" />
          <div>
            <h4>{{ message.sender }}</h4>
            <p class="message-property">{{ getPropertyById(message.propertyId).name }}</p>
          </div>
        </div>
        <p class="message-preview">{{ message.text.substring(0, 60) }}{{ message.text.length > 60 ? '...' : '' }}</p>
        <p class="message-time">{{ message.time }}</p>
      </div>
    </div>
    <div v-else class="empty-state">
      <MailIcon class="empty-icon" />
      <p>No hay mensajes nuevos</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { UserIcon, MailIcon } from 'lucide-vue-next';

const props = defineProps({
  properties: {
    type: Array,
    required: true
  }
});

// Sample messages data
const messages = ref([
  {
    sender: 'Juan Pérez',
    propertyId: 1,
    text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
    time: 'Hace 2 horas'
  },
  {
    sender: 'Ana Martínez',
    propertyId: 2,
    text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
    time: 'Hace 1 día'
  }
]);

const getPropertyById = (id) => {
  return props.properties.find(property => property.id === id) || { 
    name: 'Propiedad no encontrada'
  };
};
</script>

<style scoped>
.dashboard-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.card-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.view-all-button {
  background: none;
  border: none;
  color: #0071c2;
  font-size: 0.9rem;
  cursor: pointer;
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message-item {
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.message-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.message-sender {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.message-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.message-sender h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.message-property {
  font-size: 0.8rem;
  color: #666;
}

.message-preview {
  font-size: 0.9rem;
  margin: 0 0 0.5rem;
}

.message-time {
  font-size: 0.8rem;
  color: #666;
  text-align: right;
  margin: 0;
}

.empty-state {
  text-align: center;
  padding: 2rem 0;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #ccc;
  margin-bottom: 1rem;
}
</style>