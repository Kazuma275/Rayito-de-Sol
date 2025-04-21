<template>
  <div class="app-container">
    <Header v-if="isMainPage" :user="user" :activeTab="activeTab" @changeTab="changeTab" />
    
    <!-- Este es el espacio donde se mostrarán las rutas y sus componentes -->
    <div class="main-content">
      <router-view></router-view> <!-- Aquí se renderizan las páginas según la ruta -->
    </div>
    
    <Footer v-if="isMainPage" @changeTab="changeTab" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'; 
import { useRoute } from 'vue-router';
import Header from './components/layout/Header.vue';
import Footer from './components/layout/Footer.vue';

// Sample user data
const user = ref({
  name: 'Carlos Rodríguez',
  email: 'carlos@example.com',
  avatar: null
});

const route = useRoute();
const isMainPage = computed(() => route.path === '/main');
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

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html, body {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  overflow-x: hidden;  /* Evita el desplazamiento horizontal */
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: var(--text-color);
  background-color: var(--light-gray);
  line-height: 1.6;
  min-height: 100vh; /* Hace que el body ocupe toda la altura disponible */
}

.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;  /* Asegura que el contenedor ocupe toda la altura */
  width: 100%; /* Asegura que ocupe todo el ancho */
}

.main-content {
  flex-grow: 1;  /* Hace que el contenido ocupe todo el espacio restante */
  width: 100%; /* Asegura que ocupe todo el ancho disponible */
  max-width: 100%; /* Elimina cualquier limitación de ancho */
  margin: 0;  /* Elimina márgenes no deseados */
  box-sizing: border-box; /* Asegura que padding no afecte el tamaño del contenedor */
}

footer {
  background-color: #333;
  color: white;
  padding: 1rem;
  text-align: center;
  width: 100%;
  position: relative;
  bottom: 0;
}

footer p {
  margin: 0;
}

section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Responsive adjustments */
@media (max-width: 1280px) {
  .main-content {
    padding: 1.5rem;  /* Ajuste de padding en pantallas más pequeñas */
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem;  /* Ajuste de padding en dispositivos móviles */
  }
  
  .section-title {
    font-size: 1.5rem;
  }
}
</style>
