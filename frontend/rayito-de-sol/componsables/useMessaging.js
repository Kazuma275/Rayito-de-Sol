// useMessaging.js - VERSIÓN CORREGIDA
import { ref, onUnmounted } from 'vue';
import axios from 'axios';
import { apiHeaders } from '@/../utils/api';

export function useMessaging(userId) {
  const newMessage = ref(null);
  const typingUserId = ref(null);
  const onlineUsers = ref([]);
  const activeChannels = ref(new Map());
  const isConnected = ref(false);

  // Verificar conexión antes de usar
  const ensureConnection = () => {
    if (!window.Echo || !window.Echo.connector?.pusher) {
      console.warn('⚠️ Echo no está listo');
      return false;
    }
    return true;
  };

  // Función para escuchar una conversación específica
  const listenToConversation = (conversationId, onMessage, onTyping) => {
    if (!ensureConnection()) return;

    const channelName = `chat.${conversationId}`;
    
    if (activeChannels.value.has(channelName)) {
      console.log(`Ya escuchando canal: ${channelName}`);
      return;
    }

    console.log(`🎯 Conectando al canal: ${channelName}`);

    try {
      const channel = window.Echo.private(channelName)
        .listen('.message.sent', (e) => {
          console.log('✅ Mensaje recibido:', e);
          newMessage.value = e.message;
          if (onMessage) onMessage(e.message);
        })
        .listen('.user.typing', (e) => {
          console.log('⌨️ Usuario escribiendo:', e);
          typingUserId.value = e.from_user_id;
          if (onTyping) onTyping(e.from_user_id);
        })
        .subscribed(() => {
          console.log(`✅ Suscrito al canal: ${channelName}`);
          isConnected.value = true;
        })
        .error((error) => {
          console.error(`❌ Error en canal ${channelName}:`, error);
          isConnected.value = false;
        });

      activeChannels.value.set(channelName, channel);

    } catch (error) {
      console.error(`❌ Error conectando al canal ${channelName}:`, error);
    }
  };

  // Función para dejar de escuchar una conversación
  const leaveConversation = (conversationId) => {
    const channelName = `chat.${conversationId}`;
    
    if (activeChannels.value.has(channelName)) {
      console.log(`🔌 Desconectando del canal: ${channelName}`);
      window.Echo.leave(channelName);
      activeChannels.value.delete(channelName);
    }
  };

  // Función para escuchar usuarios en línea
  const listenOnline = () => {
    if (!ensureConnection()) return;

    try {
      window.Echo.join('online')
        .here((users) => {
          console.log('👥 Usuarios aquí:', users);
          onlineUsers.value = users;
        })
        .joining((user) => {
          console.log('👋 Usuario se unió:', user);
          if (!onlineUsers.value.some(u => u.id === user.id)) {
            onlineUsers.value.push(user);
          }
        })
        .leaving((user) => {
          console.log('👋 Usuario se fue:', user);
          onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
        })
        .error((error) => {
          console.error('❌ Error en canal online:', error);
        });
    } catch (error) {
      console.error('❌ Error conectando al canal online:', error);
    }
  };

  // 🚨 ESTA ES LA FUNCIÓN QUE FALTABA
  const sendTyping = async (conversationId) => {
    if (!conversationId) {
      console.warn('⚠️ No se puede enviar typing sin conversationId');
      return;
    }

    try {
      await axios.post(
        `/api/conversations/${conversationId}/typing`, 
        {}, 
        apiHeaders()
      );
      console.log('✅ Typing enviado para conversación:', conversationId);
    } catch (error) {
      console.error('❌ Error enviando typing:', error);
    }
  };

  // Función legacy para compatibilidad (las funciones que tenías antes)
  const listenMessages = (callback) => {
    console.warn('⚠️ listenMessages está deprecated, usa listenToConversation');
    if (callback && newMessage.value) {
      callback(newMessage.value);
    }
  };

  const listenTyping = (callback) => {
    console.warn('⚠️ listenTyping está deprecated, usa listenToConversation');
    if (callback && typingUserId.value) {
      callback(typingUserId.value);
    }
  };

  // Limpiar al desmontar
  onUnmounted(() => {
    console.log('🧹 Limpiando canales...');
    activeChannels.value.forEach((channel, channelName) => {
      window.Echo.leave(channelName);
    });
    window.Echo.leave('online');
    activeChannels.value.clear();
  });

  return {
    // Estados reactivos
    newMessage,
    typingUserId,
    onlineUsers,
    isConnected,
    
    // Funciones principales
    listenToConversation,
    leaveConversation,
    listenOnline,
    sendTyping, // 🚨 IMPORTANTE: Esta función ahora está incluida
    
    // Funciones legacy (para compatibilidad)
    listenMessages,
    listenTyping,
    
    // Funciones helper
    ensureConnection,
  };
}