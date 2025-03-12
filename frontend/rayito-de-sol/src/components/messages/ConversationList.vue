<template>
  <div class="messages-sidebar">
    <div class="messages-search">
      <SearchIcon class="search-icon" />
      <input type="text" placeholder="Buscar mensajes..." class="search-input" />
    </div>
    
    <div class="conversation-list">
      <div v-for="(conversation, index) in conversations" :key="index" 
           class="conversation-item" 
           :class="{ active: activeConversation === index }"
           @click="$emit('select-conversation', index)">
        <div class="conversation-avatar">
          <UserIcon class="avatar-icon" />
        </div>
        <div class="conversation-info">
          <div class="conversation-header">
            <h4>{{ conversation.name }}</h4>
            <span class="conversation-time">{{ conversation.lastMessage.time }}</span>
          </div>
          <p class="conversation-preview">{{ conversation.lastMessage.text.substring(0, 40) }}{{ conversation.lastMessage.text.length > 40 ? '...' : '' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { SearchIcon, UserIcon } from 'lucide-vue-next';

defineProps({
  conversations: {
    type: Array,
    required: true
  },
  activeConversation: {
    type: Number,
    default: null
  }
});

defineEmits(['select-conversation']);
</script>

<style scoped>
.messages-sidebar {
  width: 300px;
  border-right: 1px solid #eee;
  display: flex;
  flex-direction: column;
}

.messages-search {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1.5rem;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #666;
}

.search-input {
  width: 100%;
  padding: 0.5rem 0.5rem 0.5rem 2rem;
  border: 1px solid #ccc;
  border-radius: 20px;
  font-size: 0.9rem;
}

.conversation-list {
  flex: 1;
  overflow-y: auto;
}

.conversation-item {
  display: flex;
  padding: 1rem;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.3s;
}

.conversation-item:hover, .conversation-item.active {
  background-color: #f5f5f5;
}

.conversation-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
}

.avatar-icon {
  width: 20px;
  height: 20px;
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
}

.conversation-time {
  font-size: 0.8rem;
  color: #666;
}

.conversation-preview {
  font-size: 0.9rem;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #666;
}

@media (max-width: 768px) {
  .messages-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid #eee;
  }
}
</style>