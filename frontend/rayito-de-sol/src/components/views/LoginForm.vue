<template>
  <div class="login-container">
    <div class="login-card">
      <h2 class="login-title">Iniciar sesión</h2>
      <p class="login-subtitle">Accede a tu cuenta para gestionar tus propiedades</p>
      
      <form @submit.prevent="handleSubmit" class="login-form">
        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input 
            type="email" 
            id="email" 
            v-model="formData.email" 
            required 
            class="form-input"
            :class="{ 'input-error': errors.email }"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>
        
        <div class="form-group">
          <label for="password">Contraseña</label>
          <div class="password-input-container">
            <input 
              :type="showPassword ? 'text' : 'password'" 
              id="password" 
              v-model="formData.password" 
              required 
              class="form-input"
              :class="{ 'input-error': errors.password }"
            />
            <button 
              type="button" 
              class="password-toggle" 
              @click="showPassword = !showPassword"
            >
              <EyeIcon v-if="!showPassword" class="icon" />
              <EyeOffIcon v-else class="icon" />
            </button>
          </div>
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>
        
        <div class="form-group checkbox-group">
          <input 
            type="checkbox" 
            id="remember" 
            v-model="formData.remember"
          />
          <label for="remember">Recordar mi sesión</label>
        </div>
        
        <div class="forgot-password">
          <a href="#" @click.prevent="forgotPassword">¿Olvidaste tu contraseña?</a>
        </div>
        
        <button type="submit" class="submit-button" :disabled="isSubmitting">
          <LoaderIcon v-if="isSubmitting" class="spinner" />
          <span v-else>Iniciar sesión</span>
        </button>
      </form>
      
      <div class="register-link">
        ¿No tienes una cuenta? <a href="#" @click.prevent="goToRegister">Regístrate</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { EyeIcon, EyeOffIcon, LoaderIcon } from 'lucide-vue-next';

const router = useRouter();
const emit = defineEmits(['register']);

const formData = reactive({
  email: '',
  password: '',
  remember: false
});

const errors = reactive({
  email: '',
  password: ''
});

const isSubmitting = ref(false);
const showPassword = ref(false);

const validateForm = () => {
  let isValid = true;
  
  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key] = '';
  });
  
  // Validate email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(formData.email)) {
    errors.email = 'Por favor, introduce un correo electrónico válido';
    isValid = false;
  }
  
  // Validate password
  if (formData.password.length < 1) {
    errors.password = 'Por favor, introduce tu contraseña';
    isValid = false;
  }
  
  return isValid;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }
  
  isSubmitting.value = true;
  
  try {
    // Simulamos una petición al servidor
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Aquí iría la lógica para enviar los datos al servidor
    console.log('Datos de inicio de sesión:', {
      email: formData.email,
      password: formData.password,
      remember: formData.remember
    });
    
    // Redirigir al usuario al dashboard
  } catch (error) {
    console.error('Error al iniciar sesión:', error);
    alert('Correo electrónico o contraseña incorrectos. Por favor, inténtalo de nuevo.');
  } finally {
    isSubmitting.value = false;
  }
};

const forgotPassword = () => {
  alert('Funcionalidad de recuperación de contraseña pendiente');
};

const goToRegister = () => {
  emit('register');
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.login-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  width: 100%;
  max-width: 450px;
}

.login-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--primary-color, #0071c2);
  margin-bottom: 0.5rem;
  text-align: center;
}

.login-subtitle {
  color: #666;
  margin-bottom: 2rem;
  text-align: center;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-input {
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color, #0071c2);
}

.input-error {
  border-color: #e53935;
}

.error-message {
  color: #e53935;
  font-size: 0.875rem;
}

.checkbox-group {
  flex-direction: row;
  align-items: center;
  gap: 0.75rem;
}

.forgot-password {
  text-align: right;
  margin-top: -0.5rem;
}

.forgot-password a {
  color: var(--primary-color, #0071c2);
  text-decoration: none;
  font-size: 0.875rem;
}

.forgot-password a:hover {
  text-decoration: underline;
}

.submit-button {
  background-color: var(--primary-color, #0071c2);
  color: white;
  border: none;
  border-radius: 4px;
  padding: 0.875rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}

.submit-button:hover {
  background-color: #005999;
}

.submit-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.spinner {
  animation: spin 1s linear infinite;
  width: 20px;
  height: 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.register-link {
  margin-top: 1.5rem;
  text-align: center;
  color: #666;
}

.register-link a {
  color: var(--primary-color, #0071c2);
  text-decoration: none;
  font-weight: 500;
}

.register-link a:hover {
  text-decoration: underline;
}

.password-input-container {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
}

.icon {
  width: 18px;
  height: 18px;
}
</style>