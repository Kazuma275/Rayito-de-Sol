<template>
  <div class="conversation-item" :class="{ active }" @click="$emit('click')">
    <div class="conversation-avatar">
      <UserIcon class="avatar-icon" />
    </div>
    <div class="conversation-info">
      <div class="conversation-header">
        <h4>{{ conversation.name }}</h4>
        <span class="conversation-time">{{ conversation.lastMessage?.time }}</span>
      </div>
      <p class="conversation-preview">
        {{ conversation.lastMessage?.text?.substring(0, 40) }}{{ conversation.lastMessage?.text?.length > 40 ? '...' : '' }}
      </p>
      <div class="conversation-property">
        {{ getPropertyById(conversation.propertyId)?.name }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { UserIcon } from 'lucide-vue-next'
defineProps({
  conversation: Object,
  active: Boolean,
  getPropertyById: Function
})
</script>