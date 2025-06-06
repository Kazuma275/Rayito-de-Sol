<template>
  <div class="app-wrapper">
    <Header v-if="isMainPage" :user="user" :activeTab="activeTab" @changeTab="changeTab" />

    <main class="main-content" role="main" tabindex="-1">
      <router-view />
    </main>

    <Footer v-if="isMainPage" @changeTab="changeTab" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import Header from './components/layout/Header.vue';
import Footer from './components/layout/Footer.vue';
import { useUserStore } from '@/stores/userStore.js';

// Sample user data
const user = ref({
  name: 'Carlos Rodríguez',
  email: 'carlos@example.com',
  avatar: null
});


const userStore = useUserStore()
const route = useRoute();
const isMainPage = computed(() => route.path === '/main');

userStore.hydrate()

</script>

<style>
:root {
  --primary-color: #003580;
  --primary-light: #0071c2;
  --secondary-color: #feba02;
  --text-color: #333;
  --light-gray: #f5f5f5;
  --border-color: #eee;
}

*,
*::before,
*::after {
  box-sizing: border-box;
}

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  background: var(--light-gray);
  color: var(--text-color);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  scroll-behavior: smooth;
}

body {
  min-height: 100vh;
}

.app-wrapper {
  display: flex;
  flex-direction: column;
  background: var(--light-gray);
}

.main-content {
  flex: 1 0 auto;
  width: 100%;
  display: flex;
  flex-direction: column;
  min-height: 0;
  /* No padding/margin aquí: el padding debe estar en el contenido de cada página */
}

footer, .Footer {
  flex-shrink: 0;
  background: #333;
  color: #fff;
  padding: 1rem 0;
  text-align: center;
  width: 100%;
  box-sizing: border-box;
}

header, .Header {
  width: 100%;
  box-sizing: border-box;
}

/* Asegura que el contenido tenga fondo blanco consistente */
.properties-section {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  padding: 2rem;
  min-height: 60vh; /* Ajusta según diseño para evitar fondo blanco "hueco" */
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  display: flex;
  flex-direction: column;
}

section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Responsive adjustments */
@media (max-width: 1280px) {
  .properties-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }
}

@media (max-width: 768px) {
  .properties-section {
    padding: 1rem;
    margin: 1rem;
  }
  .section-title {
    font-size: 1.5rem;
  }
}
</style>