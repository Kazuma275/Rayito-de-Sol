import { createApp } from 'vue'
import App from './App.vue'
import router from './router/routes'
import { createPinia } from 'pinia'
import './echo.js';

const app = createApp(App)
app.use(router) 
app.use(createPinia())
app.mount('#app')