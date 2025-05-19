<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <SunIcon class="logo-icon" />
        <h1 class="logo-text">Rayito de Sol</h1>
      </div>
      
      <h2 class="login-title">Portal de Inquilinos</h2>
      
      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email</label>
          <div class="input-container">
            <MailIcon class="input-icon" />
            <input 
              type="email" 
              id="email" 
              v-model="email" 
              placeholder="tu@email.com" 
              required 
              class="form-input"
            />
          </div>
        </div>
        
        <div class="form-group">
          <div class="password-label">
            <label for="password">Contraseña</label>
            <a href="#" class="forgot-password" @click.prevent="forgotPassword">¿Olvidaste tu contraseña?</a>
          </div>
          <div class="input-container">
            <LockIcon class="input-icon" />
            <input 
              :type="showPassword ? 'text' : 'password'" 
              id="password" 
              v-model="password" 
              placeholder="Tu contraseña" 
              required 
              class="form-input"
            />
            <button 
              type="button" 
              class="toggle-password" 
              @click="showPassword = !showPassword"
            >
              <EyeIcon v-if="!showPassword" class="eye-icon" />
              <EyeOffIcon v-else class="eye-icon" />
            </button>
          </div>
        </div>
        
        <div class="remember-me">
          <label class="checkbox-container">
            <input type="checkbox" v-model="rememberMe" />
            <span class="checkmark"></span>
            Recordarme
          </label>
        </div>
        
        <button 
          type="submit" 
          class="login-button" 
          :disabled="isLoading"
        >
          <LoaderIcon v-if="isLoading" class="spinner" />
          <span v-else>Iniciar Sesión</span>
        </button>
      </form>
      
      <div class="login-divider">
        <span>o</span>
      </div>
      
      <div class="social-login">
        <button class="social-button google">
          <div class="social-icon google-icon"></div>
          Continuar con Google
        </button>
        
        <button class="social-button facebook">
          <div class="social-icon facebook-icon"></div>
          Continuar con Facebook
        </button>
      </div>
      
      <div class="register-link">
        ¿No tienes una cuenta? <a href="#" @click.prevent="goToRegister">Regístrate</a>
      </div>
    </div>
    
    <div class="back-to-home">
      <a href="/" class="back-link">
        <ArrowLeftIcon class="back-icon" />
        Volver a la página principal
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { 
  SunIcon, 
  MailIcon, 
  LockIcon, 
  EyeIcon, 
  EyeOffIcon, 
  LoaderIcon,
  ArrowLeftIcon
} from 'lucide-vue-next';

const router = useRouter();

// Form state
const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const showPassword = ref(false);
const isLoading = ref(false);

// Navigation
const forgotPassword = () => {
  // Usar window.location.href en lugar de router.push
  window.location.href = '/renters/forgot-password';
};

const goToRegister = () => {
  // Usar window.location.href en lugar de router.push
  window.location.href = '/renters/register';
};

// Form submission
const handleLogin = async () => {
  isLoading.value = true;
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Set auth flag in localStorage
    localStorage.setItem('renters_auth', 'true');
    
    // Redirect to dashboard on success
    window.location.href = '/renters/dashboard';
  } catch (error) {
    console.error('Login error:', error);
    // Handle login error
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background-color: #f5f5f5;
}

.login-card {
  width: 100%;
  max-width: 400px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.login-header {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.logo-icon {
  width: 32px;
  height: 32px;
  color: var(--secondary-color, #feba02);
  margin-right: 0.75rem;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color, #003580);
  margin: 0;
}

.login-title {
  font-size: 1.5rem;
  text-align: center;
  margin-bottom: 2rem;
  color: #333;
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
  color: #333;
}

.password-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot-password {
  font-size: 0.8rem;
  color: var(--primary-color, #003580);
  text-decoration: none;
}

.forgot-password:hover {
  text-decoration: underline;
}

.input-container {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  color: #666;
}

.form-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color, #003580);
}

.toggle-password {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.eye-icon {
  width: 18px;
  height: 18px;
  color: #666;
}

.remember-me {
  display: flex;
  align-items: center;
}

.checkbox-container {
  display: flex;
  align-items: center;
  position: relative;
  padding-left: 28px;
  cursor: pointer;
  user-select: none;
}

.checkbox-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 18px;
  width: 18px;
  background-color: #f5f5f5;
  border: 1px solid #ccc;
  border-radius: 3px;
}

.checkbox-container:hover input ~ .checkmark {
  background-color: #eee;
}

.checkbox-container input:checked ~ .checkmark {
  background-color: var(--primary-color, #003580);
  border-color: var(--primary-color, #003580);
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.checkbox-container input:checked ~ .checkmark:after {
  display: block;
}

.checkbox-container .checkmark:after {
  left: 6px;
  top: 2px;
  width: 4px;
  height: 8px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.login-button {
  background-color: var(--primary-color, #003580);
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-button:hover {
  background-color: var(--primary-light, #0071c2);
}

.login-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.spinner {
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  100% { transform: rotate(360deg); }
}

.login-divider {
  display: flex;
  align-items: center;
  margin: 1.5rem 0;
}

.login-divider::before,
.login-divider::after {
  content: "";
  flex: 1;
  border-bottom: 1px solid #eee;
}

.login-divider span {
  padding: 0 1rem;
  color: #666;
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
  padding: 0.75rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
  border: 1px solid #eee;
  background-color: white;
}

.social-button:hover {
  background-color: #f5f5f5;
}

.social-button.google {
  color: #333;
}

.social-button.facebook {
  color: #1877f2;
}

.social-icon {
  width: 20px;
  height: 20px;
  margin-right: 0.75rem;
  background-color: #ddd;
  border-radius: 50%;
}

.google-icon {
  background-color: #DB4437;
}

.facebook-icon {
  background-color: #1877F2;
}

.register-link {
  text-align: center;
  font-size: 0.9rem;
  color: #666;
}

.register-link a {
  color: var(--primary-color, #003580);
  text-decoration: none;
  font-weight: 500;
}

.register-link a:hover {
  text-decoration: underline;
}

.back-to-home {
  margin-top: 2rem;
}

.back-link {
  display: flex;
  align-items: center;
  color: #666;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s;
}

.back-link:hover {
  color: var(--primary-color, #003580);
}

.back-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

@media (max-width: 576px) {
  .login-card {
    padding: 1.5rem;
  }
}
</style>
