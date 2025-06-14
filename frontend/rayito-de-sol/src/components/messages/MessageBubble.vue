<template>
  <div
    class="message-bubble"
    :class="{ 'message-sent': message.sent, 'message-received': !message.sent }"
  >
    <div class="message-content">{{ message.text }}</div>
    <div class="message-time">{{ message.time }}</div>
  </div>
</template>

<script setup>
defineProps({ message: Object })
</script>