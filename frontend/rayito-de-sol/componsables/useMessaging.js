import { ref, onUnmounted } from 'vue';

export function useMessaging(userId) {
  const newMessage = ref(null);
  const typingUserId = ref(null);
  const onlineUsers = ref([]);

  const listenMessages = (callback) => {
    window.Echo.channel(`user.${userId}`)
      .listen('.message.sent', (e) => {
        newMessage.value = e.message;
        if (callback) callback(e.message);
      });
  };

  const listenTyping = (callback) => {
    window.Echo.channel(`user.${userId}`)
      .listen('.user.typing', (e) => {
        typingUserId.value = e.from_user_id;
        if (callback) callback(e.from_user_id);
      });
  };

  const listenOnline = () => {
    window.Echo.join('online')
      .here((users) => { onlineUsers.value = users; })
      .joining((user) => { if (!onlineUsers.value.some(u => u.id === user.id)) onlineUsers.value.push(user); })
      .leaving((user) => { onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id); });
  };

  onUnmounted(() => {
    window.Echo.leave(`user.${userId}`);
    window.Echo.leave('online');
  });

  return {
    newMessage,
    typingUserId,
    onlineUsers,
    listenMessages,
    listenTyping,
    listenOnline,
  };
}