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
              placeholder="Introduce tu contraseña"
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

        <div class="form-options">
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
        </div>

        <button type="submit" class="submit-button" :disabled="isSubmitting">
          <LoaderIcon v-if="isSubmitting" class="spinner" />
          <span v-else>Iniciar sesión</span>
        </button>
      </form>

      <div class="register-link">
        ¿No tienes una cuenta? <a href="#" @click.prevent="goToRegister" class="link">Regístrate</a>
      </div>

      <div class="back-button-container">
        <a href="http://localhost:5173/portal/" class="back-button">
          ← Volver al portal
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { EyeIcon, EyeOffIcon, LoaderIcon } from 'lucide-vue-next';
import axios from 'axios';
/* CAMBIAR */
import { useUserStore } from '../../../stores/user';

const router = useRouter();
const userStore = useUserStore()
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
const isValid = ref(true);

const validateForm = () => {
  isValid.value = true;
  Object.keys(errors).forEach(key => errors[key] = '');

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(formData.email)) {
    errors.email = 'Por favor, introduce un correo electrónico válido';
    isValid.value = false;
  }

  if (formData.password.length < 1) {
    errors.password = 'Por favor, introduce tu contraseña';
    isValid.value = false;
  }

  return isValid.value;
};

/* LLAMADA A LARAVEL */
const handleSubmit = async () => {
  if (!validateForm()) return;  // Verifica si el formulario es válido

  isSubmitting.value = true;  // Cambia el estado de envío a verdadero

  try {
    // Realiza la solicitud POST a la API de Laravel
    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      email: formData.email,
      password: formData.password,
    });

    // Si la autenticación es exitosa, puedes almacenar el token o realizar cualquier acción
    console.log('Inicio de sesión exitoso:', response.data);

    // Almacena el token de la respuesta (si es un JWT u otro tipo de token)
    localStorage.setItem('auth_token', response.data.token);  
    localStorage.setItem('auth_user', JSON.stringify(response.data.user));
    userStore.setUser(response.data.user)
    userStore.setToken(response.data.token)

    // Redirigir al main 
    router.push('/main');
  } catch (error) {
    // Verifica si el error tiene una respuesta y muestra el mensaje correspondiente
    if (error.response) {
      console.error('Error al iniciar sesión:', error.response.data.message);
      alert(error.response.data.message || 'Correo electrónico o contraseña incorrectos.');
    } else {
      console.error('Error de red:', error);
      alert('Hubo un problema con la conexión, por favor intenta más tarde.');
    }
  } finally {
    isSubmitting.value = false; 
  }
};

const forgotPassword = () => {
  alert('Funcionalidad de recuperación de contraseña pendiente');
};

const goToRegister = () => {
  router.push('/register');
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  min-height: calc(100vh - 100px);
  background: linear-gradient(145deg, #eaf1f8, #f5f7fa);
}

.login-card {
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0, 113, 194, 0.15);
  padding: 3rem 2.5rem;
  width: 100%;
  max-width: 460px;
  transition: transform 0.3s ease;
}

.login-card:hover {
  transform: translateY(-3px);
}

.login-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color, #0071c2);
  margin-bottom: 0.25rem;
  text-align: center;
}

.login-subtitle {
  color: #666;
  margin-bottom: 2rem;
  text-align: center;
  font-size: 1rem;
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

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.checkbox-group {
  flex-direction: row;
  align-items: center;
  gap: 0.75rem;
  display: flex;
}

.forgot-password a {
  color: var(--primary-color, #0071c2);
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.3s ease;
}

.forgot-password a:hover {
  text-decoration: underline;
  color: #005999;
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

.register-link {
  margin-top: 1.5rem;
  text-align: center;
  color: #666;
}

.register-link .link {
  color: var(--primary-color, #0071c2);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.register-link .link:hover {
  text-decoration: underline;
  color: #005999;
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

.back-button-container {
  margin-top: 1.5rem;
  text-align: center;
}

.back-button {
  display: inline-block;
  color: #0071c2;
  font-weight: 500;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border: 1px solid #0071c2;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.back-button:hover {
  background-color: #0071c2;
  color: white;
}

/* 🎯 Mobile Responsive */
@media (max-width: 500px) {
  .login-card {
    padding: 2rem 1.5rem;
  }

  .login-title {
    font-size: 1.6rem;
  }

  .login-subtitle {
    font-size: 0.95rem;
  }

  .form-input {
    font-size: 0.95rem;
  }

  .submit-button {
    font-size: 0.95rem;
    height: 44px;
  }

  .back-button {
    font-size: 0.9rem;
    padding: 0.4rem 0.75rem;
  }
}
</style>
