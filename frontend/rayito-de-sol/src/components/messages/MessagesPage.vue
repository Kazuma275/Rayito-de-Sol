<template>
  <div>
    <h2 class="section-title">Mensajes</h2>
    
    <div class="messages-container">
      <ConversationList 
        :conversations="conversations" 
        :active-conversation="activeConversation"
        @select-conversation="activeConversation = $event"
      />
      
      <ConversationView 
        v-if="activeConversation !== null" 
        :conversation="conversations[activeConversation]"
        :property="getPropertyById(conversations[activeConversation].propertyId)"
      />
      
      <div v-else class="empty-conversation">
        <MessageSquareIcon class="empty-icon" />
        <h3>Selecciona una conversación</h3>
        <p>Elige una conversación de la lista para ver los mensajes</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { MessageSquareIcon } from 'lucide-vue-next';
import ConversationList from './ConversationList.vue';
import ConversationView from './ConversationView.vue';

const props = defineProps({
  properties: {
    type: Array,
    required: true
  }
});

const activeConversation = ref(null);

// Sample conversations data
const conversations = ref([
  {
    id: 1,
    name: 'Juan Pérez',
    propertyId: 1,
    bookingId: 'B5678',
    lastMessage: {
      text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
      time: 'Hace 2 horas'
    },
    messages: [
      {
        text: 'Hola, tengo una reserva para la próxima semana.',
        time: '10:30',
        sent: false
      },
      {
        text: 'Hola Juan, sí, veo tu reserva del 10 al 17 de agosto.',
        time: '10:35',
        sent: true
      },
      {
        text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
        time: '10:40',
        sent: false
      }
    ]
  },
  {
    id: 2,
    name: 'Ana Martínez',
    propertyId: 2,
    bookingId: 'B9012',
    lastMessage: {
      text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
      time: 'Hace 1 día'
    },
    messages: [
      {
        text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
        time: 'Ayer',
        sent: false
      }
    ]
  }
]);

const getPropertyById = (id) => {
  return props.properties.find(property => property.id === id) || { 
    name: 'Propiedad no encontrada'
  };
};
</script>

<style scoped>
.messages-container {
  display: flex;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  height: 600px;
}

.empty-conversation {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}

.empty-conversation .empty-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1rem;
}

.empty-conversation h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

.empty-conversation p {
  color: #666;
}

@media (max-width: 768px) {
  .messages-container {
    flex-direction: column;
    height: auto;
  }
}
</style>