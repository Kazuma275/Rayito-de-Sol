<template>
  <div class="message-input">
    <textarea
      placeholder="Escribe un mensaje..."
      class="input-textarea"
      rows="3"
      v-model="text"
      @keydown.enter.exact.prevent="emitMessage"
    ></textarea>
    <button class="send-button" :disabled="!text.trim()" @click="emitMessage">
      <SendIcon class="send-icon" />
      Enviar
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { SendIcon } from 'lucide-vue-next'
const text = ref('')
const emit = defineEmits(['send'])
function emitMessage() {
  if (text.value.trim()) {
    emit('send', text.value.trim())
    text.value = ''
  }
}
</script>