<template>
  <div v-if="isOpen" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2 class="modal-title">Recuperar contraseña</h2>
        <button @click="closeModal" class="close-button">
          <XIcon class="close-icon" />
        </button>
      </div>

      <div v-if="!emailSent" class="modal-body">
        <p class="modal-description">
          Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <form @submit.prevent="handleSubmit" class="recovery-form">
          <div class="form-group">
            <label for="recovery-email">Correo electrónico</label>
            <div class="input-wrapper">
              <input
                type="email"
                id="recovery-email"
                v-model="email"
                required
                class="form-input"
                :class="{ 'input-error': error }"
                placeholder="ejemplo@correo.com"
              />
              <div class="input-glow"></div>
            </div>
            <span v-if="error" class="error-message">{{ error }}</span>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="cancel-button">
              Cancelar
            </button>
            <button type="submit" class="submit-button" :disabled="isSubmitting">
              <LoaderIcon v-if="isSubmitting" class="spinner" />
              <span v-else>Enviar enlace</span>
            </button>
          </div>
        </form>
      </div>

      <div v-else class="success-content">
        <div class="success-icon">
          <CheckCircleIcon class="check-icon" />
        </div>
        <h3 class="success-title">¡Correo enviado!</h3>
        <p class="success-message">
          Hemos enviado un enlace de recuperación a <strong>{{ email }}</strong>.
          Revisa tu bandeja de entrada y sigue las instrucciones.
        </p>
        <button @click="closeModal" class="ok-button">
          Entendido
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { XIcon, LoaderIcon, CheckCircleIcon } from 'lucide-vue-next'
import api from '@/axios'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const email = ref('')
const error = ref('')
const isSubmitting = ref(false)
const emailSent = ref(false)

const closeModal = () => {
  emit('close')
  // Reset form when closing
  setTimeout(() => {
    email.value = ''
    error.value = ''
    emailSent.value = false
    isSubmitting.value = false
  }, 300)
}

const validateEmail = () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(email.value)) {
    error.value = 'Por favor, introduce un correo electrónico válido'
    return false
  }
  error.value = ''
  return true
}

const handleSubmit = async () => {
  if (!validateEmail()) return

  isSubmitting.value = true
  error.value = ''

  try {
    await api.post('/forgot-password', {
      email: email.value
    })
    
    emailSent.value = true
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'No encontramos una cuenta con este correo electrónico'
    } else {
      error.value = 'Hubo un error al enviar el correo. Inténtalo de nuevo.'
    }
  } finally {
    isSubmitting.value = false
  }
}

// Reset form when modal opens
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    email.value = ''
    error.value = ''
    emailSent.value = false
    isSubmitting.value = false
  }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.5rem 0;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  color: #64748b;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.close-button:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 1.5rem;
}

.modal-description {
  color: #64748b;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.recovery-form {
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

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
}

.form-input:focus + .input-glow {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
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

.form-input.input-error {
  border-color: #ef4444;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.cancel-button {
  padding: 0.75rem 1.5rem;
  border: 1px solid #e2e8f0;
  background: white;
  color: #64748b;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cancel-button:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.submit-button {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.submit-button:disabled {
  background: #94a3b8;
  cursor: not-allowed;
  transform: none;
}

.spinner {
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.success-content {
  padding: 2rem 1.5rem;
  text-align: center;
}

.success-icon {
  margin-bottom: 1rem;
}

.check-icon {
  width: 48px;
  height: 48px;
  color: #10b981;
  margin: 0 auto;
}

.success-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.success-message {
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.ok-button {
  padding: 0.75rem 2rem;
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.ok-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

@media (max-width: 500px) {
  .modal-content {
    margin: 1rem;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .cancel-button,
  .submit-button {
    width: 100%;
    justify-content: center;
  }
}
</style>