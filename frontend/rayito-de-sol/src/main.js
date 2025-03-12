import { createApp } from 'vue'
import App from './App.vue'
import router from './router/routes' // Make sure this path is correct

const app = createApp(App)
app.use(router) // This line is crucial
app.mount('#app')