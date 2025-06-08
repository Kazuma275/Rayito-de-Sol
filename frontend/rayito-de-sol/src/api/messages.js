import axios from 'axios';

export function getConversations() {
  return axios.get('/api/conversations');
}
export function getMessages(conversationId) {
  return axios.get(`/api/conversations/${conversationId}/messages`);
}
export function sendMessage(conversationId, text, attachment = null) {
  const formData = new FormData();
  formData.append('text', text);
  if (attachment) formData.append('attachment', attachment);
  return axios.post(`/api/conversations/${conversationId}/messages`, formData);
}
export function createConversation(reservationId) {
  return axios.post('/api/conversations', { reservation_id: reservationId });
}