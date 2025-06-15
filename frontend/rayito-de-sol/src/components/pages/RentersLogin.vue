<template>
  <div class="login-container">
    <div class="login-inner">
      <div class="login-card">
        <!-- Logo -->
        <div class="logo">
          <div class="logo-icon-wrapper">
            <SunIcon class="logo-icon" />
            <div class="logo-icon-glow"></div>
          </div>
          <h1 class="logo-text">Rayito de Sol</h1>
        </div>

        <h2 class="login-title">Portal de Inquilinos</h2>
        <p class="login-subtitle">
          Accede a tu cuenta para gestionar tus reservas
        </p>

        <form class="login-form" @submit.prevent="handleSubmit">
          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
              <MailIcon class="input-icon" />
              <input
                type="email"
                id="email"
                v-model="formData.email"
                placeholder="tu@email.com"
                required
                class="form-input"
                :class="{ 'input-error': errors.email }"
              />
              <div class="input-glow"></div>
            </div>
            <span v-if="errors.email" class="error-message">{{
              errors.email
            }}</span>
          </div>

          <div class="form-group">
            <div class="password-label">
              <label for="password">Contraseña</label>
              <a
                href="#"
                class="forgot-password"
                @click.prevent="forgotPassword"
                >¿Olvidaste tu contraseña?</a
              >
            </div>
            <div class="input-wrapper">
              <LockIcon class="input-icon" />
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                v-model="formData.password"
                placeholder="Tu contraseña"
                required
                class="form-input"
                :class="{ 'input-error': errors.password }"
              />
              <div class="input-glow"></div>
              <button
                type="button"
                class="toggle-password"
                @click="showPassword = !showPassword"
                aria-label="Mostrar contraseña"
              >
                <EyeIcon v-if="!showPassword" class="eye-icon" />
                <EyeOffIcon v-else class="eye-icon" />
              </button>
            </div>
            <span v-if="errors.password" class="error-message">{{
              errors.password
            }}</span>
          </div>

          <div class="remember-me">
            <label class="checkbox-container">
              <input
                type="checkbox"
                v-model="formData.remember"
                class="custom-checkbox"
              />
              <span class="checkbox-label">Recordarme</span>
            </label>
          </div>

          <button type="submit" class="login-button" :disabled="isSubmitting">
            <LoaderIcon v-if="isSubmitting" class="spinner" />
            <span v-else>Iniciar Sesión</span>
          </button>
        </form>

        <div class="register-link">
          ¿No tienes una cuenta?
          <a href="#" @click.prevent="goToRegister">Regístrate</a>
        </div>

        <div class="back-button-container">
          <a href="/" class="back-button">
            <ArrowLeftIcon class="back-icon" />
            <span>Volver al portal</span>
          </a>
        </div>
      </div>
    </div>

    <ForgotPasswordModal
      :isOpen="showForgotPasswordModal"
      @close="closeForgotPasswordModal"
    />

    <!-- Modal de sesión expirada -->
    <SessionExpiredModal
      :isOpen="showSessionExpiredModal"
      @close="closeSessionExpiredModal"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router"; // <- IMPORTA useRoute aquí
import {
  EyeIcon,
  EyeOffIcon,
  LoaderIcon,
  SunIcon,
  ArrowLeftIcon,
  MailIcon,
  LockIcon,
} from "lucide-vue-next";
import api from "@/axios";
import { useUserStore } from "@/stores/userStore.js";
import { authState } from "@/router/auth-guard";
import ForgotPasswordModal from "../views/ForgotPasswordModal.vue";
import SessionExpiredModal from "../views/SessionExpiredModal.vue";
import { useToast } from "vue-toastification";

const toast = useToast();

const router = useRouter();
const route = useRoute(); // <- AGREGA ESTA LÍNEA

// Si necesitas leer el rol de la ruta, descomenta esto:
// const role = computed(() => route.params.role);

const formData = reactive({
  email: "",
  password: "",
  remember: false,
});

function playSound() {
  const audio = new Audio("/assets/sounds/wrong.mp3");
  audio.play();
}

const errors = reactive({
  email: "",
  password: "",
});

const userStore = useUserStore();
const isSubmitting = ref(false);
const showPassword = ref(false);
const isValid = ref(true);
const showForgotPasswordModal = ref(false);
const showSessionExpiredModal = ref(false);

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("session_expired") === "true") {
    showSessionExpiredModal.value = true;
  }
});

const validateForm = () => {
  isValid.value = true;
  Object.keys(errors).forEach((key) => (errors[key] = ""));
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(formData.email)) {
    errors.email = "Por favor, introduce un correo electrónico válido";
    isValid.value = false;
  }
  if (formData.password.length < 1) {
    errors.password = "Por favor, introduce tu contraseña";
    isValid.value = false;
  }
  return isValid.value;
};

const setupExpirationTimer = () => {
  if (window.tokenExpirationTimer) {
    clearInterval(window.tokenExpirationTimer);
  }
  window.tokenExpirationTimer = setInterval(() => {
    const expirationTime = parseInt(
      localStorage.getItem("token_expiration") || "0"
    );
    if (expirationTime && Date.now() > expirationTime) {
      clearInterval(window.tokenExpirationTimer);
      userStore.logout();
      authState.isAuthenticated = false;
      authState.user = null;
      router.push("/renters/login?session_expired=true");
    }
  }, 10000);
};

const handleSubmit = async () => {
  if (!validateForm()) return;
  isSubmitting.value = true;
  try {
    const response = await api.post(
      "/login",
      {
        email: formData.email,
        password: formData.password,
      },
      {
        withCredentials: false,
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      }
    );

    // VALIDA EL ROL DE INQUILINO
    if (!response.data.user || response.data.user.role !== "guest") {
      toast.error("Solo los inquilinos pueden iniciar sesión en este portal.");
      playSound();
      isSubmitting.value = false;
      return;
    }

    userStore.setUser(response.data.user, formData.remember);
    userStore.setToken(response.data.token, formData.remember);
    const expirationTime = Date.now() + 300000;
    localStorage.setItem("token_expiration", expirationTime.toString());
    authState.isAuthenticated = true;
    authState.user = response.data.user;
    setupExpirationTimer();
    router.push("/renters/dashboard");
  } catch (error) {
    console.error("AXIOS ERROR:", error);
    if (error.response) {
      toast.error(
        error.response.data.message ||
          "Correo electrónico o contraseña incorrectos."
      );
    } else if (error.request) {
      toast.error("No se pudo conectar con el servidor. Revisa la consola.");
    } else {
      toast.error("Error desconocido: " + error.message);
    }
  }
};

const forgotPassword = () => {
  showForgotPasswordModal.value = true;
};

const closeForgotPasswordModal = () => {
  showForgotPasswordModal.value = false;
};

const closeSessionExpiredModal = () => {
  showSessionExpiredModal.value = false;
  const url = new URL(window.location);
  url.searchParams.delete("session_expired");
  window.history.replaceState({}, "", url);
};

// Importante: así navegas al register para inquilinos
const goToRegister = () => {
  router.push({ name: "Register", params: { role: "guest" } });
};
</script>
<style scoped>
.login-container {
  min-height: 100vh;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.login-container::before {
  content: "";
  position: absolute;
  width: 200%;
  height: 200%;
  top: -50%;
  left: -50%;
  background: radial-gradient(
      circle at center,
      rgba(59, 130, 246, 0.1) 0%,
      transparent 70%
    ),
    radial-gradient(
      circle at 30% 70%,
      rgba(251, 191, 36, 0.1) 0%,
      transparent 60%
    );
  animation: rotate 30s linear infinite;
  z-index: 0;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.login-inner {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 460px;
}

.login-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 24px;
  padding: 3rem 2.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.6);
  transform: translateY(20px);
  opacity: 0;
  animation: slideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes slideUp {
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 2rem;
  position: relative;
}

.logo-icon-wrapper {
  position: relative;
  margin-right: 1rem;
}

.logo-icon {
  width: 48px;
  height: 48px;
  color: #facc15;
  filter: drop-shadow(0 0 8px rgba(250, 204, 21, 0.5));
  position: relative;
  z-index: 1;
  animation: pulse 3s infinite ease-in-out;
}

.logo-icon-glow {
  position: absolute;
  width: 48px;
  height: 48px;
  top: 0;
  left: 0;
  background: radial-gradient(
    circle,
    rgba(250, 204, 21, 0.3) 0%,
    rgba(250, 204, 21, 0) 70%
  );
  border-radius: 50%;
  animation: glow 3s infinite ease-in-out;
  z-index: 0;
}

.logo-text {
  font-size: 2rem;
  font-weight: 700;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.login-title {
  font-size: 1.75rem;
  color: #1e3a8a;
  text-align: center;
  margin-bottom: 2rem;
  font-weight: 600;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #f59e0b;
  z-index: 1;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 12px;
  font-size: 1rem;
  background: rgba(255, 255, 255, 0.8);
  color: #1e293b;
  transition: all 0.3s ease;
}

.input-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 12px;
  pointer-events: none;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #f59e0b;
}

.form-input:focus + .input-glow {
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
}

.form-input::placeholder {
  color: #94a3b8;
}

.password-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot-password {
  color: #f59e0b;
  font-size: 0.9rem;
  text-decoration: none;
  transition: all 0.3s ease;
}

.forgot-password:hover {
  color: #d97706;
  text-decoration: underline;
}

.toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 8px;
  transition: all 0.3s ease;
}

.toggle-password:hover {
  color: #1e293b;
}

.eye-icon {
  width: 20px;
  height: 20px;
}

.remember-me {
  display: flex;
  align-items: center;
}

.checkbox-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.custom-checkbox {
  appearance: none;
  width: 18px;
  height: 18px;
  border: 2px solid #f59e0b;
  border-radius: 4px;
  position: relative;
  cursor: pointer;
  transition: all 0.2s ease;
}

.custom-checkbox:checked {
  background-color: #f59e0b;
}

.custom-checkbox:checked::after {
  content: "✓";
  position: absolute;
  color: white;
  font-size: 12px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.checkbox-label {
  color: #1e293b;
  font-size: 0.9rem;
}

.login-button {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
  color: #1e293b;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.login-button::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transform: translateX(-100%);
  transition: transform 0s;
}

.login-button:hover::before {
  transform: translateX(100%);
  transition: transform 0.6s ease;
}

.login-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.login-button:disabled {
  background: linear-gradient(135deg, #94a3b8 0%, #cbd5e1 100%);
  cursor: not-allowed;
  transform: none;
}

.spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.login-divider {
  display: flex;
  align-items: center;
  margin: 1.5rem 0;
  gap: 1rem;
}

.login-divider::before,
.login-divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: rgba(148, 163, 184, 0.2);
}

.login-divider span {
  color: #64748b;
  font-size: 0.9rem;
}

.social-login {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.social-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 0.875rem;
  border-radius: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(148, 163, 184, 0.2);
}

.social-button:hover {
  background: rgba(255, 255, 255, 0.8);
  transform: translateY(-2px);
}

.social-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
}

.google-icon {
  background-color: #db4437;
}

.facebook-icon {
  background-color: #1877f2;
}

.register-link {
  margin-top: 1.5rem;
  text-align: center;
  color: #64748b;
  font-size: 0.95rem;
}

.register-link a {
  color: #f59e0b;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
}

.register-link a:hover {
  color: #d97706;
  text-decoration: underline;
}

.back-button-container {
  margin-top: 2rem;
  text-align: center;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #f59e0b;
  font-weight: 500;
  text-decoration: none;
  padding: 0.75rem 1.25rem;
  border: 1px solid rgba(245, 158, 11, 0.2);
  border-radius: 12px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.5);
}

.back-button:hover {
  background: #f59e0b;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.back-icon {
  width: 18px;
  height: 18px;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

@keyframes glow {
  0%,
  100% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.5);
    opacity: 0.8;
  }
}

@media (max-width: 500px) {
  .login-card {
    padding: 2rem 1.5rem;
  }

  .logo-icon {
    width: 40px;
    height: 40px;
  }

  .logo-text {
    font-size: 1.75rem;
  }

  .login-title {
    font-size: 1.5rem;
  }

  .form-input {
    font-size: 0.95rem;
  }

  .login-button {
    padding: 0.875rem;
    font-size: 0.95rem;
  }

  .social-button {
    padding: 0.75rem;
    font-size: 0.9rem;
  }
}
</style>