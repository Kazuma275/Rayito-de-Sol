// echo.js - VERSIÓN CORREGIDA
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { apiHeaders } from "@/../utils/api";

// Para debug
Pusher.logToConsole = true;

window.Pusher = Pusher;

// Asegúrate de que apiHeaders() devuelve el token correcto
const headers = apiHeaders().headers;
console.log('Headers de autenticación:', headers);

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu',
  forceTLS: true, 
  authEndpoint: "http://localhost:8000/broadcasting/auth",
  auth: {
    headers: {
      ...headers,
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      'Accept': 'application/json',
    }
  }
});

// Agregar listeners para debug
window.Echo.connector.pusher.connection.bind('state_change', function(states) {
  console.log('Estado Pusher:', states.previous, '->', states.current);
});

window.Echo.connector.pusher.connection.bind('error', function(err) {
  console.error('Error Pusher:', err);
});

export default window.Echo;