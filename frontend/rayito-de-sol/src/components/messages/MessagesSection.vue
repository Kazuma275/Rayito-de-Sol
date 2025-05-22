<template>
  <div class="messages-section">
    <h2 class="section-title">Mensajes</h2>
    
    <div class="messages-container">
      <div class="conversations-sidebar">
        <div class="search-bar">
          <SearchIcon class="search-icon" />
          <input type="text" placeholder="Buscar conversaciones..." class="search-input" v-model="searchQuery" />
        </div>
        
        <div class="conversations-list">
          <div 
            v-for="(conversation, index) in filteredConversations" 
            :key="index" 
            class="conversation-item"
            :class="{ active: activeConversation === index }"
            @click="selectConversation(index)"
          >
            <div class="conversation-avatar">
              <UserIcon class="avatar-icon" />
            </div>
            <div class="conversation-info">
              <div class="conversation-header">
                <h4>{{ conversation.name }}</h4>
                <span class="conversation-time">{{ formatTime(conversation.lastMessage.time) }}</span>
              </div>
              <p class="conversation-preview">{{ conversation.lastMessage.text.substring(0, 40) }}{{ conversation.lastMessage.text.length > 40 ? '...' : '' }}</p>
              <div class="conversation-property">
                <HomeIcon class="property-icon" />
                <span>{{ getPropertyName(conversation.propertyId) }}</span>
              </div>
            </div>
            <div v-if="conversation.unread" class="unread-badge">{{ conversation.unread }}</div>
          </div>
        </div>
      </div>
      
      <div class="conversation-view">
        <div v-if="activeConversation !== null" class="conversation-content">
          <div class="conversation-header">
            <div class="conversation-user">
              <div class="user-avatar">
                <UserIcon class="avatar-icon" />
              </div>
              <div>
                <h3>{{ conversations[activeConversation].name }}</h3>
                <div class="conversation-property">
                  <HomeIcon class="property-icon" />
                  <span>{{ getPropertyName(conversations[activeConversation].propertyId) }}</span>
                </div>
              </div>
            </div>
            <div class="conversation-actions">
              <button class="action-button">
                <PhoneIcon class="action-icon" />
              </button>
              <button class="action-button">
                <MoreVerticalIcon class="action-icon" />
              </button>
            </div>
          </div>
          
          <div class="messages-list" ref="messagesList">
            <div 
              v-for="(message, index) in conversations[activeConversation].messages" 
              :key="index" 
              class="message-bubble"
              :class="{ 'message-sent': message.sent, 'message-received': !message.sent }"
            >
              <div class="message-content">{{ message.text }}</div>
              <div class="message-time">{{ formatTime(message.time) }}</div>
            </div>
          </div>
          
          <div class="message-input">
            <textarea 
              v-model="newMessage" 
              placeholder="Escribe un mensaje..." 
              class="input-textarea" 
              @keyup.enter="sendMessage"
            ></textarea>
            <button class="send-button" @click="sendMessage">
              <SendIcon class="send-icon" />
              Enviar
            </button>
          </div>
        </div>
        
        <div v-else class="empty-conversation">
          <MessageSquareIcon class="empty-icon" />
          <h3>Selecciona una conversación</h3>
          <p>Elige una conversación de la lista para ver los mensajes</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { 
  UserIcon, 
  HomeIcon, 
  SearchIcon, 
  PhoneIcon, 
  MoreVerticalIcon, 
  SendIcon,
  MessageSquareIcon
} from 'lucide-vue-next';

const props = defineProps({
  properties: {
    type: Array,
    required: true
  }
});

// Estado
const activeConversation = ref(null);
const newMessage = ref('');
const searchQuery = ref('');
const messagesList = ref(null);

// Datos de ejemplo para conversaciones
const conversations = ref([
  {
    id: 1,
    name: 'Juan Pérez',
    propertyId: 1,
    unread: 2,
    lastMessage: {
      text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
      time: '2023-08-10T14:30:00'
    },
    messages: [
      {
        text: 'Hola, tengo una reserva para la próxima semana.',
        time: '2023-08-10T10:30:00',
        sent: false
      },
      {
        text: 'Hola Juan, sí, veo tu reserva del 15 al 22 de agosto.',
        time: '2023-08-10T10:35:00',
        sent: true
      },
      {
        text: '¿Podría hacer el check-in un poco antes? Llegaremos alrededor de las 13:00.',
        time: '2023-08-10T14:30:00',
        sent: false
      }
    ]
  },
  {
    id: 2,
    name: 'María García',
    propertyId: 2,
    unread: 0,
    lastMessage: {
      text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
      time: '2023-08-09T16:45:00'
    },
    messages: [
      {
        text: 'Hola, acabo de realizar una reserva para septiembre.',
        time: '2023-08-09T15:20:00',
        sent: false
      },
      {
        text: 'Hola María, he confirmado tu reserva. Gracias por elegir nuestra propiedad.',
        time: '2023-08-09T15:30:00',
        sent: true
      },
      {
        text: 'Gracias por aceptar mi reserva. ¿Hay algún restaurante que recomiende cerca del apartamento?',
        time: '2023-08-09T16:45:00',
        sent: false
      },
      {
        text: 'Claro, hay varios restaurantes excelentes a poca distancia. Te recomiendo "El Pescador" para mariscos y "La Terraza" para tapas. Ambos están a menos de 5 minutos caminando.',
        time: '2023-08-09T17:00:00',
        sent: true
      }
    ]
  },
  {
    id: 3,
    name: 'Carlos Rodríguez',
    propertyId: 3,
    unread: 0,
    lastMessage: {
      text: 'Perfecto, gracias por la información. Nos vemos el próximo mes.',
      time: '2023-08-05T11:20:00'
    },
    messages: [
      {
        text: 'Buenas tardes, tengo algunas preguntas sobre el ático.',
        time: '2023-08-05T10:00:00',
        sent: false
      },
      {
        text: 'Hola Carlos, dime en qué puedo ayudarte.',
        time: '2023-08-05T10:05:00',
        sent: true
      },
      {
        text: '¿La terraza es privada o compartida con otros apartamentos?',
        time: '2023-08-05T10:10:00',
        sent: false
      },
      {
        text: 'La terraza es completamente privada, solo para el uso de los huéspedes del ático.',
        time: '2023-08-05T10:15:00',
        sent: true
      },
      {
        text: 'Perfecto, gracias por la información. Nos vemos el próximo mes.',
        time: '2023-08-05T11:20:00',
        sent: false
      }
    ]
  }
]);

// Conversaciones filtradas por búsqueda
const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value;
  
  const query = searchQuery.value.toLowerCase();
  return conversations.value.filter(conversation => 
    conversation.name.toLowerCase().includes(query) || 
    getPropertyName(conversation.propertyId).toLowerCase().includes(query)
  );
});

// Métodos
const selectConversation = (index) => {
  activeConversation.value = index;
  
  // Marcar como leídos los mensajes no leídos
  if (conversations.value[index].unread > 0) {
    conversations.value[index].unread = 0;
  }
  
  // Scroll al final de la conversación
  nextTick(() => {
    if (messagesList.value) {
      messagesList.value.scrollTop = messagesList.value.scrollHeight;
    }
  });
};

const sendMessage = () => {
  if (!newMessage.value.trim() || activeConversation.value === null) return;
  
  const now = new Date().toISOString();
  
  // Añadir el mensaje a la conversación activa
  conversations.value[activeConversation.value].messages.push({
    text: newMessage.value.trim(),
    time: now,
    sent: true
  });
  
  // Actualizar el último mensaje
  conversations.value[activeConversation.value].lastMessage = {
    text: newMessage.value.trim(),
    time: now
  };
  
  // Limpiar el campo de mensaje
  newMessage.value = '';
  
  // Scroll al final de la conversación
  nextTick(() => {
    if (messagesList.value) {
      messagesList.value.scrollTop = messagesList.value.scrollHeight;
    }
  });
};

const formatTime = (timeString) => {
  if (!timeString) return '';
  
  const date = new Date(timeString);
  const now = new Date();
  const diffMs = now - date;
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) {
    // Hoy - mostrar hora
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
  } else if (diffDays === 1) {
    // Ayer
    return 'Ayer';
  } else if (diffDays < 7) {
    // Esta semana - mostrar día de la semana
    return date.toLocaleDateString('es-ES', { weekday: 'long' });
  } else {
    // Más de una semana - mostrar fecha
    return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
  }
};

const getPropertyName = (propertyId) => {
  const property = props.properties.find(p => p.id === propertyId);
  return property ? property.name : 'Propiedad desconocida';
};

// Scroll al final de la conversación cuando se selecciona una
watch(activeConversation, () => {
  nextTick(() => {
    if (messagesList.value) {
      messagesList.value.scrollTop = messagesList.value.scrollHeight;
    }
  });
});
</script>

<style scoped>
.messages-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 60vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  position: relative;
  overflow: hidden;
}

.messages-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(0, 113, 194, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(0, 53, 128, 0.03) 0%, transparent 60%);
  z-index: 0;
}

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

.messages-container {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 1.5rem;
  height: 600px;
  position: relative;
  z-index: 1;
}

.conversations-sidebar {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.search-bar {
  padding: 1rem;
  border-bottom: 1px solid #e6f0ff;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1.5rem;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  color: #64748b;
}

.search-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #1e293b;
  background-color: #f8fafc;
  transition: all 0.3s;
}

.search-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
  background-color: white;
}

.conversations-list {
  flex: 1;
  overflow-y: auto;
}

.conversation-item {
  display: flex;
  padding: 1rem;
  border-bottom: 1px solid #e6f0ff;
  cursor: pointer;
  transition: background-color 0.3s;
  position: relative;
}

.conversation-item:hover, .conversation-item.active {
  background-color: #f0f7ff;
}

.conversation-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  flex-shrink: 0;
}

.avatar-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
}

.conversation-info {
  flex: 1;
  min-width: 0;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.25rem;
}

.conversation-header h4 {
  font-size: 1rem;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #1e293b;
}

.conversation-time {
  font-size: 0.8rem;
  color: #64748b;
  white-space: nowrap;
}

.conversation-preview {
  font-size: 0.9rem;
  margin: 0 0 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #64748b;
}

.conversation-property {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.8rem;
  color: #0071c2;
}

.property-icon {
  width: 14px;
  height: 14px;
}

.unread-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #0071c2;
  color: white;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.conversation-view {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.conversation-content {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e6f0ff;
  background-color: #f8fafc;
}

.conversation-user {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.conversation-user h3 {
  font-size: 1.1rem;
  margin: 0 0 0.25rem;
  color: #1e293b;
}

.conversation-actions {
  display: flex;
  gap: 0.5rem;
}

.action-button {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: #f0f7ff;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.action-button:hover {
  background-color: #e6f0ff;
}

.action-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.messages-list {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background-color: #f8fafc;
}

.message-bubble {
  max-width: 70%;
  padding: 1rem;
  border-radius: 12px;
  position: relative;
}

.message-sent {
  align-self: flex-end;
  background-color: #e6f0ff;
  color: #1e293b;
}

.message-received {
  align-self: flex-start;
  background-color: white;
  color: #1e293b;
  border: 1px solid #e6f0ff;
}

.message-content {
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.message-time {
  font-size: 0.8rem;
  color: #64748b;
  text-align: right;
}

.message-input {
  padding: 1rem;
  border-top: 1px solid #e6f0ff;
  display: flex;
  gap: 1rem;
  background-color: white;
}

.input-textarea {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  resize: none;
  min-height: 60px;
  max-height: 120px;
  font-family: inherit;
  transition: all 0.3s;
}

.input-textarea:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.send-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.send-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.send-icon {
  width: 18px;
  height: 18px;
}

.empty-conversation {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 2rem;
  text-align: center;
  color: #64748b;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #ccc;
  margin-bottom: 1.5rem;
}

.empty-conversation h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
  color: #003580;
}

@media (max-width: 992px) {
  .messages-container {
    grid-template-columns: 300px 1fr;
  }
}

@media (max-width: 768px) {
  .messages-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }
  
  .messages-container {
    grid-template-columns: 1fr;
    height: auto;
  }
  
  .conversations-sidebar {
    height: 300px;
  }
  
  .conversation-view {
    height: 500px;
  }
}

@media (max-width: 576px) {
  .messages-section {
    padding: 1rem;
    margin: 1rem;
  }
  
  .conversation-header {
    padding: 0.75rem 1rem;
  }
  
  .messages-list {
    padding: 1rem;
  }
  
  .message-input {
    padding: 0.75rem;
  }
}
</style>