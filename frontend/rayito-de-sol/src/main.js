import { createApp } from 'vue'
import App from './App.vue'
import router from './router/routes'
import { createPinia } from 'pinia'
import './echo.js';
import ToastPlugin from './plugins/toast.js';
import 'vue-toastification/dist/index.css'
import { useUserStore } from '@/stores/userStore'
import { setupGlobalExpirationCheck } from './router/auth-guard';
import '@/assets/toast-styles.css'; 

// Configurar verificación global de expiración de sesión
setupGlobalExpirationCheck();

// Opciones de configuración para vue-toastification
const toastOptions = {
  position: "top-right",
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 60,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: "button",
  icon: true,
  rtl: false
};

const app = createApp(App)
const pinia = createPinia()
app.use(router)
app.use(pinia)
app.use(ToastPlugin);

// Hidratación y sincronización del usuario SOLO AQUÍ
const userStore = useUserStore()
userStore.hydrate()
window.addEventListener('storage', (event) => {
  if (event.key === 'auth_user' || event.key === 'auth_token') {
    userStore.hydrate()
  }
})

app.mount('#app')