<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h2 class="register-title">Crear una cuenta</h2>
        <p class="register-subtitle">
          Regístrate para gestionar tus propiedades
        </p>
      </div>

      <form @submit.prevent="handleSubmit" class="register-form">
        <div class="form-group">
          <label for="username">Nombre completo</label>
          <div class="input-wrapper">
            <UserIcon class="input-icon" />
            <input
              type="text"
              id="username"
              v-model="formData.username"
              required
              class="form-input"
              :class="{ 'input-error': errors.username }"
              placeholder="Introduce tu nombre completo"
            />
          </div>
          <span v-if="errors.username" class="error-message">
            <AlertCircleIcon class="error-icon" />
            {{ errors.username }}
          </span>
        </div>

        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <div class="input-wrapper">
            <MailIcon class="input-icon" />
            <input
              type="email"
              id="email"
              v-model="formData.email"
              required
              class="form-input"
              :class="{ 'input-error': errors.email }"
              placeholder="ejemplo@correo.com"
            />
          </div>
          <span v-if="errors.email" class="error-message">
            <AlertCircleIcon class="error-icon" />
            {{ errors.email }}
          </span>
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <div class="input-wrapper">
            <LockIcon class="input-icon" />
            <input
              :type="showPassword ? 'text' : 'password'"
              id="password"
              v-model="formData.password"
              required
              class="form-input"
              :class="{ 'input-error': errors.password }"
              placeholder="Mínimo 8 caracteres"
            />
            <button
              type="button"
              class="password-toggle"
              @click="showPassword = !showPassword"
              aria-label="Mostrar contraseña"
            >
              <EyeIcon v-if="!showPassword" class="toggle-icon" />
              <EyeOffIcon v-else class="toggle-icon" />
            </button>
          </div>
          <span v-if="errors.password" class="error-message">
            <AlertCircleIcon class="error-icon" />
            {{ errors.password }}
          </span>
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirmar contraseña</label>
          <div class="input-wrapper">
            <LockIcon class="input-icon" />
            <input
              :type="showConfirmPassword ? 'text' : 'password'"
              id="confirmPassword"
              v-model="formData.confirmPassword"
              required
              class="form-input"
              :class="{ 'input-error': errors.confirmPassword }"
              placeholder="Repite tu contraseña"
            />
            <button
              type="button"
              class="password-toggle"
              @click="showConfirmPassword = !showConfirmPassword"
              aria-label="Mostrar contraseña"
            >
              <EyeIcon v-if="!showConfirmPassword" class="toggle-icon" />
              <EyeOffIcon v-else class="toggle-icon" />
            </button>
          </div>
          <span v-if="errors.confirmPassword" class="error-message">
            <AlertCircleIcon class="error-icon" />
            {{ errors.confirmPassword }}
          </span>
        </div>

        <div class="form-group checkbox-group">
          <div class="checkbox-wrapper">
            <input
              type="checkbox"
              id="terms"
              v-model="formData.terms"
              required
              class="checkbox-input"
              :class="{ 'input-error': errors.terms }"
            />
            <label for="terms" class="terms-label">
              Acepto los
              <router-link to="/terms" class="terms-link">términos y condiciones</router-link>
            </label>
          </div>
          <span v-if="errors.terms" class="error-message terms-error">
            <AlertCircleIcon class="error-icon" />
            {{ errors.terms }}
          </span>
        </div>

        <button type="submit" class="submit-button" :disabled="isSubmitting">
          <LoaderIcon v-if="isSubmitting" class="spinner" />
          <span v-else>Registrarse</span>
        </button>
      </form>

      <div class="login-link">
        ¿Ya tienes una cuenta?
        <router-link to="/login" class="link">Iniciar sesión</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import axios from "axios";
import { 
  EyeIcon, 
  EyeOffIcon, 
  LoaderIcon, 
  UserIcon, 
  MailIcon, 
  LockIcon,
  AlertCircleIcon,
  CheckCircleIcon
} from "lucide-vue-next";

const router = useRouter();
const toast = useToast();

const formData = reactive({
  username: "",
  email: "",
  password: "",
  confirmPassword: "",
  terms: false,
});

const errors = reactive({
  username: "",
  email: "",
  password: "",
  confirmPassword: "",
  terms: "",
});

const isSubmitting = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

// Verificar si los términos ya fueron aceptados
const termsAccepted = ref(localStorage.getItem('termsAccepted') === 'true');
onMounted(() => {
  if (termsAccepted.value) {
    formData.terms = true;
  }
});

const validateForm = () => {
  let isValid = true;

  // Reset errors
  Object.keys(errors).forEach((key) => {
    errors[key] = "";
  });

  // Validate name
  if (formData.username.trim().length < 3) {
    errors.username = "El nombre de usuario debe tener al menos 3 caracteres";
    isValid = false;
  }

  // Validate email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(formData.email)) {
    errors.email = "Por favor, introduce un correo electrónico válido";
    isValid = false;
  }

  // Validate password
  if (formData.password.length < 8) {
    errors.password = "La contraseña debe tener al menos 8 caracteres";
    isValid = false;
  }

  // Validate password confirmation
  if (formData.password !== formData.confirmPassword) {
    errors.confirmPassword = "Las contraseñas no coinciden";
    isValid = false;
  }

  // Validate terms
  if (!formData.terms) {
    errors.terms = "Debes aceptar los términos y condiciones";
    isValid = false;
  }

  return isValid;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    toast.error("Por favor, corrige los errores en el formulario");
    return;
  }

  isSubmitting.value = true;

  try {
    // Llamada a la API de Laravel para registrar al usuario
    const response = await axios.post("http://127.0.0.1:8000/api/register", {
      username: formData.username,
      email: formData.email,
      password: formData.password,
      password_confirmation: formData.confirmPassword,
    });

    // Si la respuesta es exitosa, redirigir al login
    toast.success("¡Registro exitoso! Ahora puedes iniciar sesión.");
    
    // Guardar en localStorage que el usuario está registrado
    localStorage.setItem('userRegistered', 'true');
    
    router.push("/login");
  } catch (error) {
    console.error("Respuesta de error:", error.response?.data);

    if (error.response && error.response.data) {
      if (error.response.data.errors) {
        console.log("Errores de validación:", error.response.data.errors);
        const firstError = Object.values(error.response.data.errors)[0][0];
        toast.error(firstError);
      } else if (error.response.data.message) {
        toast.error(error.response.data.message);
      } else {
        toast.error("Error desconocido en la respuesta del servidor.");
      }
    } else {
      toast.error("Error de red o del servidor.");
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.register-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
  padding: 0;
  width: 100%;
  max-width: 450px;
  overflow: hidden;
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.register-header {
  background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
  padding: 2.5rem 2rem;
  text-align: center;
  color: white;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.register-header::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 60%);
  animation: pulse 15s infinite linear;
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.3; }
  50% { transform: scale(1.1); opacity: 0.5; }
  100% { transform: scale(1); opacity: 0.3; }
}

.register-title {
  font-size: 2rem;
  font-weight: 800;
  margin: 0 0 0.5rem 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  position: relative;
}

.register-subtitle {
  margin: 0;
  opacity: 0.9;
  font-size: 1.1rem;
  position: relative;
}

.register-form {
  padding: 2.5rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
  transition: all 0.3s ease;
}

.input-wrapper:focus-within {
  filter: drop-shadow(0 4px 6px rgba(37, 99, 235, 0.15));
  transform: translateY(-1px);
}

.input-icon {
  position: absolute;
  left: 1rem;
  color: #6b7280;
  width: 18px;
  height: 18px;
  z-index: 1;
}

.form-input {
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #ffffff;
  width: 100%;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03);
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.03);
}

.form-input::placeholder {
  color: #9ca3af;
}

.input-error {
  border-color: #ef4444 !important;
  background: #fef2f2 !important;
}

.password-toggle {
  position: absolute;
  right: 1rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
}

.password-toggle:hover {
  color: #3b82f6;
  background: rgba(59, 130, 246, 0.1);
}

.toggle-icon {
  width: 20px;
  height: 20px;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  margin-top: 0.25rem;
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.error-icon {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.checkbox-group {
  margin-top: 0.5rem;
}

.checkbox-wrapper {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.checkbox-input {
  margin-top: 2px;
  width: 18px;
  height: 18px;
  accent-color: #3b82f6;
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.terms-label {
  line-height: 1.4;
  font-weight: 400 !important;
  color: #4b5563;
}

.terms-link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  position: relative;
  transition: all 0.2s ease;
}

.terms-link::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 2px;
  bottom: -2px;
  left: 0;
  background-color: #3b82f6;
  transform: scaleX(0);
  transform-origin: bottom right;
  transition: transform 0.3s ease;
}

.terms-link:hover::after {
  transform: scaleX(1);
  transform-origin: bottom left;
}

.terms-error {
  margin-top: 0.5rem;
  margin-left: 2.5rem;
}

.submit-button {
  background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 1rem;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 1rem;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2), 0 1px 3px rgba(37, 99, 235, 0.1);
}

.submit-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: all 0.6s ease;
}

.submit-button:hover:not(:disabled)::before {
  left: 100%;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(37, 99, 235, 0.3);
}

.submit-button:active:not(:disabled) {
  transform: translateY(0);
  box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.spinner {
  animation: spin 1s linear infinite;
  width: 20px;
  height: 20px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.login-link {
  padding: 1.5rem 2rem 2rem;
  text-align: center;
  color: #6b7280;
  border-top: 1px solid #f3f4f6;
  font-size: 0.95rem;
}

.login-link .link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  margin-left: 0.25rem;
  position: relative;
  transition: all 0.2s ease;
}

.login-link .link::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 2px;
  bottom: -2px;
  left: 0;
  background-color: #3b82f6;
  transform: scaleX(0);
  transform-origin: bottom right;
  transition: transform 0.3s ease;
}

.login-link .link:hover::after {
  transform: scaleX(1);
  transform-origin: bottom left;
}

/* Responsive */
@media (max-width: 500px) {
  .register-container {
    padding: 1rem;
  }

  .register-card {
    border-radius: 12px;
  }

  .register-header {
    padding: 1.75rem 1.5rem;
  }

  .register-title {
    font-size: 1.75rem;
  }

  .register-form {
    padding: 1.5rem;
  }
}
</style>