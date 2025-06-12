import { createApp } from 'vue'
import App from './App.vue'
import router from './router/routes'
import { createPinia } from 'pinia'
import './echo.js';
import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import { useUserStore } from '@/stores/userStore'

const app = createApp(App)
const pinia = createPinia()
app.use(router)
app.use(pinia)
app.use(Toast, {
  position: POSITION.TOP_RIGHT,
  timeout: 2500,
  closeOnClick: true,
  pauseOnHover: true,
})

// Hidratación y sincronización del usuario SOLO AQUÍ
const userStore = useUserStore()
userStore.hydrate()
window.addEventListener('storage', (event) => {
  if (event.key === 'auth_user' || event.key === 'auth_token') {
    userStore.hydrate()
  }
})

app.mount('#app')