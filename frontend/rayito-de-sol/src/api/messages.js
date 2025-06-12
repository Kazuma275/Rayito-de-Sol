import axios from 'axios';
import { apiHeaders } from '../../utils/api';
// Obtener todas las conversaciones
export function getConversations() {
  return axios.get('/api/conversations', apiHeaders());
}

// Obtener mensajes de una conversación específica
export function getMessages(conversationId) {
  return axios.get(`/api/conversations/${conversationId}/messages`, apiHeaders());
}

// Enviar un mensaje en una conversación específica (con adjunto opcional)
export function sendMessage(conversationId, text, attachment = null) {
  const formData = new FormData();
  formData.append('text', text);
  if (attachment) formData.append('attachment', attachment);
  return axios.post(`/api/conversations/${conversationId}/messages`, formData, apiHeaders());
}

// Crear una nueva conversación a partir de una reserva
export function createConversation(reservationId) {
  return axios.post('/api/conversations', { reservation_id: reservationId }, apiHeaders());
}

// 🚨 TAMBIÉN AGREGAR ESTA FUNCIÓN PARA TYPING
export async function sendTypingIndicator(conversationId) {
  return await axios.post(`/api/conversations/${conversationId}/typing`, {}, apiHeaders());
}

// Marcar todos los mensajes como leídos en una conversación
export function markAsRead(conversationId) {
  return axios.post(`/api/conversations/${conversationId}/markAsRead`, {}, apiHeaders());
}

// Notificar que el usuario está escribiendo en una conversación
export function sendTyping(conversationId) {
  return axios.post(`/api/conversations/${conversationId}/typing`, {}, apiHeaders());
}