import Toast, { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";

// Configuración básica para vue-toastification
export const toastOptions = {
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

export default {
  install: (app) => {
    app.use(Toast, toastOptions);
  }
};