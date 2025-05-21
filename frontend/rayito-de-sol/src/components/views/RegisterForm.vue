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
            placeholder="Introduce tu nombre completo"
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
            placeholder="ejemplo@correo.com"
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
              placeholder="Mínimo 8 caracteres"
            />
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
            placeholder="Repite tu contraseña"
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
          <label for="terms" class="terms-label">
            Acepto los <router-link to="/terms" class="terms-link">términos y condiciones</router-link>
          </label>
        </div>
        <span v-if="errors.terms" class="error-message terms-error">{{ errors.terms }}</span>
        
        <button type="submit" class="submit-button" :disabled="isSubmitting">
          <LoaderIcon v-if="isSubmitting" class="spinner" />
          <span v-else>Registrarse</span>
        </button>
      </form>
      
      <div class="login-link">
        ¿Ya tienes una cuenta? <router-link to="/login" class="link">Iniciar sesión</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { EyeIcon, EyeOffIcon, LoaderIcon } from 'lucide-vue-next';

const router = useRouter();

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
    // Llamada a la API de Laravel para registrar al usuario
    const response = await axios.post('http://127.0.0.1:8000/api/register', {
      name: formData.name,
      email: formData.email,
      password: formData.password,
      password_confirmation: formData.confirmPassword,
    }, {
      withCredentials: true,
    });

    // Si la respuesta es exitosa, redirigir al login
    alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
    router.push('/login');
  } catch (error) {
  console.error('Respuesta de error:', error.response?.data);

  if (error.response && error.response.data) {
    if (error.response.data.errors) {
      console.log('Errores de validación:', error.response.data.errors);
      const firstError = Object.values(error.response.data.errors)[0][0];
      alert(firstError);
    } else if (error.response.data.message) {
      alert(error.response.data.message);
    } else {
      alert('Error desconocido en la respuesta del servidor.');
    }
  } else {
    alert('Error de red o del servidor.');
  }
}
};
</script>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  min-height: calc(100vh - 100px);
  background: linear-gradient(145deg, #eaf1f8, #f5f7fa);
}

.register-card {
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0, 113, 194, 0.15);
  padding: 3rem 2.5rem;
  width: 100%;
  max-width: 500px;
  transition: transform 0.3s ease;
}

.register-card:hover {
  transform: translateY(-3px);
}

.register-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color, #0071c2);
  margin-bottom: 0.25rem;
  text-align: center;
}

.register-subtitle {
  color: #666;
  margin-bottom: 2rem;
  text-align: center;
  font-size: 1rem;
}

.register-form {
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
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.form-input {
  padding: 0.75rem 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  color: #333;
}

.form-input::placeholder {
  color: #a0a7b4;
  font-style: italic;
  font-size: 0.95rem;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color, #0071c2);
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.15);
}

.input-error {
  border-color: #e53935;
}

.error-message {
  color: #e53935;
  font-size: 0.875rem;
}

.terms-error {
  margin-top: -0.75rem;
}

.checkbox-group {
  flex-direction: row;
  align-items: flex-start;
  gap: 0.75rem;
  display: flex;
}

.terms-label {
  line-height: 1.4;
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
  border-radius: 8px;
  padding: 0.875rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 48px;
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

.login-link .link {
  color: var(--primary-color, #0071c2);
  text-decoration: none;
  font-weight: 500;
}

.login-link .link:hover {
  text-decoration: underline;
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
  cursor: pointer;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}

.icon {
  width: 20px;
  height: 20px;
}

/* 🎯 Mobile Responsive */
@media (max-width: 500px) {
  .register-card {
    padding: 2rem 1.5rem;
  }

  .register-title {
    font-size: 1.6rem;
  }

  .register-subtitle {
    font-size: 0.95rem;
  }

  .form-input {
    font-size: 0.95rem;
  }

  .submit-button {
    font-size: 0.95rem;
    height: 44px;
  }
}
</style>