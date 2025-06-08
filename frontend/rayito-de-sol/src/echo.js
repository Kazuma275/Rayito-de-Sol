import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { apiHeaders } from "@/../utils/api";

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  forceTLS: false,
  disableStats: true,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu',
  enabledTransports: ['ws', 'wss'],
  authEndpoint: "http://localhost:8000/broadcasting/auth",
  auth: {
    headers: {
      ...apiHeaders().headers
    }
  }
});