<template>
  <div class="register-container">
    <div class="register-card">
      <h2 class="register-title">Crear una cuenta</h2>
      <p class="register-subtitle">Regístrate para gestionar tus propiedades</p>
      
      <form @submit.prevent="handleSubmit" class="register-form">
        <div class="form-group">
          <label for="name">Nombre completo</label>
          <input 
            type="text" 
            id="name" 
            v-model="formData.name" 
            required 
            class="form-input"
            :class="{ 'input-error': errors.name }"
          />
          <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
        </div>
        
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
        
        <div class="form-group">
          <label for="confirmPassword">Confirmar contraseña</label>
          <input 
            type="password" 
            id="confirmPassword" 
            v-model="formData.confirmPassword" 
            required 
            class="form-input"
            :class="{ 'input-error': errors.confirmPassword }"
          />
          <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
        </div>
        
        <div class="form-group checkbox-group">
          <input 
            type="checkbox" 
            id="terms" 
            v-model="formData.terms" 
            required
            :class="{ 'input-error': errors.terms }"
          />
          <label for="terms">Acepto los <a href="#" class="terms-link">términos y condiciones</a></label>
          <span v-if="errors.terms" class="error-message">{{ errors.terms }}</span>
        </div>
        
        <button type="submit" class="submit-button" :disabled="isSubmitting">
          <LoaderIcon v-if="isSubmitting" class="spinner" />
          <span v-else>Registrarse</span>
        </button>
      </form>
      
      <div class="login-link">
        ¿Ya tienes una cuenta? <a href="#" @click.prevent="goToLogin">Iniciar sesión</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { EyeIcon, EyeOffIcon, LoaderIcon } from 'lucide-vue-next';

const router = useRouter();
const emit = defineEmits(['login']);

const formData = reactive({
  name: '',
  email: '',
  password: '',
  confirmPassword: '',
  terms: false
});

const errors = reactive({
  name: '',
  email: '',
  password: '',
  confirmPassword: '',
  terms: ''
});

const isSubmitting = ref(false);
const showPassword = ref(false);

const validateForm = () => {
  let isValid = true;
  
  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key] = '';
  });
  
  // Validate name
  if (formData.name.trim().length < 3) {
    errors.name = 'El nombre debe tener al menos 3 caracteres';
    isValid = false;
  }
  
  // Validate email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(formData.email)) {
    errors.email = 'Por favor, introduce un correo electrónico válido';
    isValid = false;
  }
  
  // Validate password
  if (formData.password.length < 8) {
    errors.password = 'La contraseña debe tener al menos 8 caracteres';
    isValid = false;
  }
  
  // Validate password confirmation
  if (formData.password !== formData.confirmPassword) {
    errors.confirmPassword = 'Las contraseñas no coinciden';
    isValid = false;
  }
  
  // Validate terms
  if (!formData.terms) {
    errors.terms = 'Debes aceptar los términos y condiciones';
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
    console.log('Datos de registro:', {
      name: formData.name,
      email: formData.email,
      password: formData.password
    });
    
    // Redirigir al usuario o mostrar mensaje de éxito
    alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
    
    // Redirigir a la página de inicio de sesión
    goToLogin();
  } catch (error) {
    console.error('Error al registrar:', error);
    alert('Ha ocurrido un error al registrarse. Por favor, inténtalo de nuevo.');
  } finally {
    isSubmitting.value = false;
  }
};

const goToLogin = () => {
  emit('login');
  // Si tienes una ruta de login, puedes usar:
  // router.push('/login');
};
</script>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.register-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  width: 100%;
  max-width: 500px;
}

.register-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--primary-color, #0071c2);
  margin-bottom: 0.5rem;
  text-align: center;
}

.register-subtitle {
  color: #666;
  margin-bottom: 2rem;
  text-align: center;
}

.register-form {
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

.terms-link {
  color: var(--primary-color, #0071c2);
  text-decoration: none;
}

.terms-link:hover {
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

.login-link {
  margin-top: 1.5rem;
  text-align: center;
  color: #666;
}

.login-link a {
  color: var(--primary-color, #0071c2);
  text-decoration: none;
  font-weight: 500;
}

.login-link a:hover {
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