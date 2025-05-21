<template>
  <div class="profile-container">
    <h1 class="profile-title">Mi Perfil</h1>
    
    <div class="profile-content">
      <div class="profile-card">
        <div class="profile-header">
          <div class="profile-avatar">
            <UserIcon class="avatar-icon" />
          </div>
          <div class="profile-info">
            <h2 class="profile-name">Usuario</h2>
            <p class="profile-email">usuario@example.com</p>
            <p class="profile-member-since">Miembro desde: Mayo 2023</p>
          </div>
        </div>
        
        <div class="profile-actions">
          <button class="profile-action-button">
            <EditIcon class="action-icon" />
            Editar perfil
          </button>
          <button class="profile-action-button">
            <KeyIcon class="action-icon" />
            Cambiar contraseña
          </button>
        </div>
      </div>
      
      <div class="profile-sections">
        <div class="profile-section">
          <h3 class="section-title">Información personal</h3>
          <div class="section-content">
            <div class="form-group">
              <label for="name">Nombre completo</label>
              <input type="text" id="name" value="Usuario" class="form-input" disabled />
            </div>
            
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" value="usuario@example.com" class="form-input" disabled />
            </div>
            
            <div class="form-group">
              <label for="phone">Teléfono</label>
              <input type="tel" id="phone" value="+34 600 000 000" class="form-input" disabled />
            </div>
            
            <div class="form-group">
              <label for="address">Dirección</label>
              <input type="text" id="address" value="Calle Ejemplo, 123" class="form-input" disabled />
            </div>
          </div>
        </div>
        
        <div class="profile-section">
          <h3 class="section-title">Preferencias</h3>
          <div class="section-content">
            <div class="form-group">
              <label for="language">Idioma</label>
              <select id="language" class="form-input" disabled>
                <option value="es">Español</option>
                <option value="en">Inglés</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="currency">Moneda</label>
              <select id="currency" class="form-input" disabled>
                <option value="eur">EUR (€)</option>
                <option value="usd">USD ($)</option>
              </select>
            </div>
            
            <div class="form-group">
              <label class="checkbox-container">
                <input type="checkbox" checked disabled />
                <span class="checkmark"></span>
                Recibir notificaciones por email
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { UserIcon, EditIcon, KeyIcon } from 'lucide-vue-next';
</script>

<style scoped>
.profile-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.profile-title {
  font-size: 1.8rem;
  color: var(--primary-color, #003580);
  margin-bottom: 2rem;
}

.profile-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

.profile-card {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.profile-header {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
}

.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background-color: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 2rem;
}

.avatar-icon {
  width: 50px;
  height: 50px;
  color: #ccc;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: 1.5rem;
  margin: 0 0 0.5rem;
  color: var(--primary-color, #003580);
}

.profile-email {
  color: #666;
  margin: 0 0 0.5rem;
}

.profile-member-since {
  color: #999;
  font-size: 0.9rem;
  margin: 0;
}

.profile-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.profile-action-button {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  background-color: #f5f5f5;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  color: #333;
  cursor: pointer;
  transition: background-color 0.3s;
}

.profile-action-button:hover {
  background-color: #eee;
}

.action-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

.profile-sections {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

.profile-section {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 1.2rem;
  color: var(--primary-color, #003580);
  margin: 0 0 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #eee;
}

.section-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  background-color: #f9f9f9;
}

.form-input:disabled {
  opacity: 0.7;
  cursor: not-allowed;
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

@media (max-width: 768px) {
  .profile-container {
    padding: 1rem;
  }
  
  .profile-header {
    flex-direction: column;
    text-align: center;
  }
  
  .profile-avatar {
    margin-right: 0;
    margin-bottom: 1.5rem;
  }
}
</style>
