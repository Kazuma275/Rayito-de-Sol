<template>
  <div class="settings-container">
    <h1 class="settings-title">Configuración</h1>
    
    <div class="settings-content">
      <div class="settings-sidebar">
        <button 
          v-for="section in sections" 
          :key="section.id" 
          class="sidebar-button" 
          :class="{ active: activeSection === section.id }"
          @click="activeSection = section.id"
        >
          <component :is="section.icon" class="sidebar-icon" />
          <span>{{ section.name }}</span>
        </button>
      </div>
      
      <div class="settings-main">
        <!-- Sección de cuenta -->
        <div v-if="activeSection === 'account'" class="settings-section">
          <h2 class="section-title">Configuración de cuenta</h2>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" value="usuario@example.com" class="form-input" />
          </div>
          
          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-input">
              <input type="password" id="password" value="********" class="form-input" disabled />
              <button class="change-password-button">Cambiar</button>
            </div>
          </div>
          
          <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="tel" id="phone" value="+34 600 000 000" class="form-input" />
          </div>
          
          <button class="save-button">Guardar cambios</button>
        </div>
        
        <!-- Sección de notificaciones -->
        <div v-if="activeSection === 'notifications'" class="settings-section">
          <h2 class="section-title">Notificaciones</h2>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Notificaciones por email</h3>
              <p class="option-description">Recibe emails sobre tus reservas, mensajes y más</p>
            </div>
            <label class="switch">
              <input type="checkbox" checked />
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Notificaciones push</h3>
              <p class="option-description">Recibe notificaciones en tu dispositivo</p>
            </div>
            <label class="switch">
              <input type="checkbox" />
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Notificaciones de marketing</h3>
              <p class="option-description">Recibe ofertas y promociones especiales</p>
            </div>
            <label class="switch">
              <input type="checkbox" />
              <span class="slider"></span>
            </label>
          </div>
          
          <button class="save-button">Guardar preferencias</button>
        </div>
        
        <!-- Sección de privacidad -->
        <div v-if="activeSection === 'privacy'" class="settings-section">
          <h2 class="section-title">Privacidad</h2>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Compartir datos de uso</h3>
              <p class="option-description">Ayúdanos a mejorar compartiendo datos anónimos de uso</p>
            </div>
            <label class="switch">
              <input type="checkbox" checked />
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Cookies de terceros</h3>
              <p class="option-description">Permitir cookies de terceros para personalizar tu experiencia</p>
            </div>
            <label class="switch">
              <input type="checkbox" checked />
              <span class="slider"></span>
            </label>
          </div>
          
          <button class="delete-account-button">Eliminar mi cuenta</button>
        </div>
        
        <!-- Sección de pagos -->
        <div v-if="activeSection === 'payments'" class="settings-section">
          <h2 class="section-title">Métodos de pago</h2>
          
          <div class="empty-state">
            <CreditCardIcon class="empty-icon" />
            <p>No tienes métodos de pago guardados</p>
            <button class="action-button">Añadir método de pago</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { 
  UserIcon, 
  BellIcon, 
  ShieldIcon, 
  CreditCardIcon 
} from 'lucide-vue-next';

const sections = [
  { id: 'account', name: 'Cuenta', icon: UserIcon },
  { id: 'notifications', name: 'Notificaciones', icon: BellIcon },
  { id: 'privacy', name: 'Privacidad', icon: ShieldIcon },
  { id: 'payments', name: 'Pagos', icon: CreditCardIcon }
];

const activeSection = ref('account');
</script>

<style scoped>
.settings-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.settings-title {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin-bottom: 2rem;
}

.settings-content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
}

.settings-sidebar {
  background-color: white;
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  height: fit-content;
}

.sidebar-button {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 1rem;
  background: none;
  border: none;
  border-radius: 4px;
  text-align: left;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  transition: all 0.3s;
}

.sidebar-button:hover {
  background-color: #f5f5f5;
}

.sidebar-button.active {
  background-color: #f0f7ff;
  color: var(--primary-color, #003580);
}

.sidebar-icon {
  width: 20px;
  height: 20px;
  margin-right: 0.75rem;
}

.settings-main {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 1.5rem;
  color: var(--primary-color, #003580);
  margin: 0 0 2rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #eee;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: #333;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color, #003580);
}

.password-input {
  display: flex;
  gap: 1rem;
}

.change-password-button {
  background-color: #f5f5f5;
  border: none;
  padding: 0 1rem;
  border-radius: 4px;
  font-weight: 500;
  color: var(--primary-color, #003580);
  cursor: pointer;
  transition: background-color 0.3s;
}

.change-password-button:hover {
  background-color: #eee;
}

.save-button {
  background-color: var(--primary-color, #003580);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.save-button:hover {
  background-color: var(--primary-light, #0071c2);
}

.notification-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 0;
  border-bottom: 1px solid #eee;
}

.option-title {
  font-size: 1.1rem;
  margin: 0 0 0.5rem;
  color: #333;
}

.option-description {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: var(--primary-color, #003580);
}

input:focus + .slider {
  box-shadow: 0 0 1px var(--primary-color, #003580);
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.delete-account-button {
  margin-top: 2rem;
  background-color: #fff2f0;
  color: #e41c00;
  border: 1px solid #e41c00;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.delete-account-button:hover {
  background-color: #ffe5e2;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
  color: #666;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #ccc;
  margin-bottom: 1rem;
}

.action-button {
  margin-top: 1rem;
  background-color: var(--primary-color, #003580);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.action-button:hover {
  background-color: var(--primary-light, #0071c2);
}

@media (max-width: 768px) {
  .settings-container {
    padding: 1rem;
  }
  
  .settings-content {
    grid-template-columns: 1fr;
  }
  
  .settings-sidebar {
    margin-bottom: 1.5rem;
  }
}
</style>
