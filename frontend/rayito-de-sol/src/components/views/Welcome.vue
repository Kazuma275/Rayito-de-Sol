<script setup lang="ts">
import { ref } from 'vue';

const title = ref('¡Bienvenido a nuestra aplicación!');
</script>

<template>
  <div class="welcome">
    <h1>{{ title }}</h1>
    <p>Gracias por visitarnos. Esperamos que disfrutes de tu experiencia.</p>
  </div>
</template>

<style scoped>
.welcome {
  text-align: center;
  padding: 2rem;
}

h1 {
  color: var(--primary-color);
  margin-bottom: 1rem;
}
</style>