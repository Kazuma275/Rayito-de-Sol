<template>
  <div class="messages-sidebar">
    <div class="messages-search">
      <SearchIcon class="search-icon" />
      <input type="text" v-model="search" placeholder="Buscar mensajes..." class="search-input" />
    </div>
    <div class="conversation-list">
      <ConversationItem
        v-for="(conversation, index) in filteredConversations"
        :key="index"
        :conversation="conversation"
        :active="activeConversation === index"
        :get-property-by-id="getPropertyById"
        @click="() => $emit('select', index)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { SearchIcon } from 'lucide-vue-next'
import ConversationItem from './ConversationItem.vue'

const props = defineProps({
  conversations: Array,
  activeConversation: [Number, null],
  getPropertyById: Function
})

const search = ref('')

const filteredConversations = computed(() => {
  if (!search.value) return props.conversations
  return props.conversations.filter(c =>
    c.name.toLowerCase().includes(search.value.toLowerCase())
    || (c.lastMessage?.text || '').toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>