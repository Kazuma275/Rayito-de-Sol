<template>
  <div class="reset-container">
    <div class="reset-inner">
      <div class="reset-card">
        <!-- Logo -->
        <div class="logo">
          <div class="logo-icon-wrapper">
            <SunIcon class="logo-icon" />
            <div class="logo-icon-glow"></div>
          </div>
          <h1 class="logo-text">Rayito de Sol</h1>
        </div>

        <div v-if="!isTokenValid" class="error-state">
          <div class="error-icon">
            <XCircleIcon class="x-icon" />
          </div>
          <h2 class="error-title">Enlace inválido o expirado</h2>
          <p class="error-message">
            El enlace de recuperación no es válido o ha expirado. 
            Por favor, solicita un nuevo enlace de recuperación.
          </p>
          <button @click="goToLogin" class="back-to-login-button">
            Volver al inicio de sesión
          </button>
        </div>

        <div v-else-if="!passwordReset" class="reset-form-container">
          <h2 class="reset-title">Restablecer contraseña</h2>
          <p class="reset-subtitle">
            Introduce tu nueva contraseña para completar el proceso
          </p>

          <form @submit.prevent="handleSubmit" class="reset-form">
            <div class="form-group">
              <label for="password">Nueva contraseña</label>
              <div class="password-input-container">
                <div class="input-wrapper">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    v-model="formData.password"
                    required
                    class="form-input"
                    :class="{ 'input-error': errors.password }"
                    placeholder="Introduce tu nueva contraseña"
                  />
                  <div class="input-glow"></div>
                </div>
                <button
                  type="button"
                  class="password-toggle"
                  @click="showPassword = !showPassword"
                  aria-label="Mostrar contraseña"
                >
                  <EyeIcon v-if="!showPassword" class="icon" />
                  <EyeOffIcon v-else class="icon" />
                </button>
              </div>
              <span v-if="errors.password" class="error-message">{{
                errors.password
              }}</span>
            </div>

            <div class="form-group">
              <label for="confirmPassword">Confirmar contraseña</label>
              <div class="password-input-container">
                <div class="input-wrapper">
                  <input
                    :type="showConfirmPassword ? 'text' : 'password'"
                    id="confirmPassword"
                    v-model="formData.confirmPassword"
                    required
                    class="form-input"
                    :class="{ 'input-error': errors.confirmPassword }"
                    placeholder="Confirma tu nueva contraseña"
                  />
                  <div class="input-glow"></div>
                </div>
                <button
                  type="button"
                  class="password-toggle"
                  @click="showConfirmPassword = !showConfirmPassword"
                  aria-label="Mostrar contraseña"
                >
                  <EyeIcon v-if="!showConfirmPassword" class="icon" />
                  <EyeOffIcon v-else class="icon" />
                </button>
              </div>
              <span v-if="errors.confirmPassword" class="error-message">{{
                errors.confirmPassword
              }}</span>
            </div>

            <div class="password-requirements">
              <h4>La contraseña debe contener:</h4>
              <ul>
                <li :class="{ valid: requirements.length }">
                  <CheckIcon v-if="requirements.length" class="check" />
                  <XIcon v-else class="x" />
                  Al menos 8 caracteres
                </li>
                <li :class="{ valid: requirements.uppercase }">
                  <CheckIcon v-if="requirements.uppercase" class="check" />
                  <XIcon v-else class="x" />
                  Una letra mayúscula
                </li>
                <li :class="{ valid: requirements.lowercase }">
                  <CheckIcon v-if="requirements.lowercase" class="check" />
                  <XIcon v-else class="x" />
                  Una letra minúscula
                </li>
                <li :class="{ valid: requirements.number }">
                  <CheckIcon v-if="requirements.number" class="check" />
                  <XIcon v-else class="x" />
                  Un número
                </li>
              </ul>
            </div>

            <button type="submit" class="submit-button" :disabled="isSubmitting || !isFormValid">
              <LoaderIcon v-if="isSubmitting" class="spinner" />
              <span v-else>Restablecer contraseña</span>
            </button>
          </form>
        </div>

        <div v-else class="success-state">
          <div class="success-icon">
            <CheckCircleIcon class="check-icon" />
          </div>
          <h2 class="success-title">¡Contraseña restablecida!</h2>
          <p class="success-message">
            Tu contraseña ha sido restablecida exitosamente. 
            Ya puedes iniciar sesión con tu nueva contraseña.
          </p>
          <button @click="goToLogin" class="login-button">
            Iniciar sesión
          </button>
        </div>

        <div class="back-button-container">
          <a href="/portal" class="back-button">
            <ArrowLeftIcon class="back-icon" />
            <span>Volver al portal</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import {
  EyeIcon,
  EyeOffIcon,
  LoaderIcon,
  SunIcon,
  ArrowLeftIcon,
  CheckCircleIcon,
  XCircleIcon,
  CheckIcon,
  XIcon
} from 'lucide-vue-next'
import api from '@/axios'

const router = useRouter()
const route = useRoute()

const formData = reactive({
  password: '',
  confirmPassword: ''
})

const errors = reactive({
  password: '',
  confirmPassword: ''
})

const isSubmitting = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const token = ref('')
const isTokenValid = ref(true)
const passwordReset = ref(false)

// Password requirements validation
const requirements = computed(() => ({
  length: formData.password.length >= 8,
  uppercase: /[A-Z]/.test(formData.password),
  lowercase: /[a-z]/.test(formData.password),
  number: /\d/.test(formData.password)
}))

const isFormValid = computed(() => {
  return Object.values(requirements.value).every(req => req) && 
         formData.password === formData.confirmPassword &&
         formData.password.length > 0
})

const validateForm = () => {
  let isValid = true
  Object.keys(errors).forEach(key => errors[key] = '')

  if (!Object.values(requirements.value).every(req => req)) {
    errors.password = 'La contraseña no cumple con los requisitos'
    isValid = false
  }

  if (formData.password !== formData.confirmPassword) {
    errors.confirmPassword = 'Las contraseñas no coinciden'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) return

  isSubmitting.value = true

  try {
    await api.post('/reset-password', {
      token: token.value,
      password: formData.password,
      password_confirmation: formData.confirmPassword
    })
    
    passwordReset.value = true
  } catch (error) {
    if (error.response?.status === 400) {
      errors.password = 'Token inválido o expirado'
    } else {
      errors.password = 'Hubo un error al restablecer la contraseña'
    }
  } finally {
    isSubmitting.value = false
  }
}

const goToLogin = () => {
  router.push('/login')
}

const verifyToken = async () => {
  try {
    await api.post('/verify-reset-token', {
      token: token.value
    })
    isTokenValid.value = true
  } catch (error) {
    isTokenValid.value = false
  }
}

onMounted(() => {
  token.value = route.query.token || ''
  if (token.value) {
    verifyToken()
  } else {
    isTokenValid.value = false
  }
})

// Clear confirm password error when passwords match
watch(() => formData.confirmPassword, () => {
  if (formData.password === formData.confirmPassword) {
    errors.confirmPassword = ''
  }
})
</script>

<style scoped>
.reset-container {
  min-height: 100vh;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.reset-container::before {
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
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.reset-inner {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 500px;
}

.reset-card {
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

.reset-title {
  font-size: 1.75rem;
  color: #1e3a8a;
  text-align: center;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.reset-subtitle {
  color: #64748b;
  text-align: center;
  margin-bottom: 2rem;
  font-size: 1rem;
}

.reset-form {
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

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
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
  border-color: #3b82f6;
}

.form-input:focus + .input-glow {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.form-input.input-error {
  border-color: #ef4444;
}

.password-input-container {
  position: relative;
}

.password-toggle {
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

.password-toggle:hover {
  color: #1e293b;
}

.icon {
  width: 20px;
  height: 20px;
}

.password-requirements {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1rem;
}

.password-requirements h4 {
  margin: 0 0 0.75rem 0;
  font-size: 0.9rem;
  color: #1e293b;
  font-weight: 500;
}

.password-requirements ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.password-requirements li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #64748b;
  transition: color 0.2s ease;
}

.password-requirements li.valid {
  color: #10b981;
}

.check, .x {
  width: 16px;
  height: 16px;
}

.check {
  color: #10b981;
}

.x {
  color: #ef4444;
}

.submit-button {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.submit-button:disabled {
  background: linear-gradient(135deg, #94a3b8 0%, #cbd5e1 100%);
  cursor: not-allowed;
  transform: none;
}

.spinner {
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state, .success-state {
  text-align: center;
  padding: 1rem 0;
}

.error-icon, .success-icon {
  margin-bottom: 1rem;
}

.x-icon {
  width: 48px;
  height: 48px;
  color: #ef4444;
  margin: 0 auto;
}

.check-icon {
  width: 48px;
  height: 48px;
  color: #10b981;
  margin: 0 auto;
}

.error-title, .success-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.error-title {
  color: #ef4444;
}

.success-title {
  color: #10b981;
}

.error-message, .success-message {
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.back-to-login-button, .login-button {
  padding: 0.75rem 2rem;
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.back-to-login-button:hover, .login-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.back-button-container {
  margin-top: 2rem;
  text-align: center;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #3b82f6;
  font-weight: 500;
  text-decoration: none;
  padding: 0.75rem 1.25rem;
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 12px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.5);
}

.back-button:hover {
  background: #3b82f6;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.back-icon {
  width: 18px;
  height: 18px;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

@keyframes glow {
  0%, 100% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.5);
    opacity: 0.8;
  }
}

@media (max-width: 500px) {
  .reset-card {
    padding: 2rem 1.5rem;
  }

  .logo-icon {
    width: 40px;
    height: 40px;
  }

  .logo-text {
    font-size: 1.75rem;
  }

  .reset-title {
    font-size: 1.5rem;
  }

  .reset-subtitle {
    font-size: 0.9rem;
  }

  .form-input {
    font-size: 0.95rem;
  }

  .submit-button {
    padding: 0.875rem;
    font-size: 0.95rem;
  }
}
</style>