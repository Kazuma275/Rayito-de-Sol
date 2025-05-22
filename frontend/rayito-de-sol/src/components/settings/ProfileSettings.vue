<template>
  <div class="settings-panel">
    <h3 class="panel-title">Perfil</h3>
    
    <form class="settings-form">
      <div class="profile-avatar">
        <div class="avatar-placeholder">
          <UserIcon class="avatar-icon" />
        </div>
        <button type="button" class="change-avatar-button">Cambiar foto</button>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="profile-name">Nombre</label>
          <input id="profile-name" type="text" v-model="profile.name" class="form-input" />
        </div>
        
        <div class="form-group">
          <label for="profile-lastname">Apellidos</label>
          <input id="profile-lastname" type="text" v-model="profile.lastname" class="form-input" />
        </div>
      </div>
      
      <div class="form-group">
        <label for="profile-email">Email</label>
        <input id="profile-email" type="email" v-model="profile.email" class="form-input" />
      </div>
      
      <div class="form-group">
        <label for="profile-phone">Teléfono</label>
        <input id="profile-phone" type="tel" v-model="profile.phone" class="form-input" />
      </div>
      
      <div class="form-actions">
        <button type="submit" class="save-button">Guardar cambios</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { UserIcon } from 'lucide-vue-next';

const profile = ref({
  name: 'Carlos',
  lastname: 'Rodríguez',
  email: 'carlos@example.com',
  phone: '+34 612 345 678'
});
</script>

<style scoped>
.settings-panel {
  max-width: 600px;
}

.panel-title {
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e6f0ff;
}

.profile-avatar {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
}

.avatar-placeholder {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1.5rem;
  border: 2px solid #0071c2;
}

.avatar-icon {
  width: 40px;
  height: 40px;
  color: #0071c2;
}

.change-avatar-button {
  background-color: #f0f7ff;
  color: #0071c2;
  border: 1px solid #0071c2;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.change-avatar-button:hover {
  background-color: #e6f0ff;
  transform: translateY(-2px);
}

.form-row {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.form-actions {
  margin-top: 2rem;
}

.save-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.save-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

@media (max-width: 576px) {
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
  
  .profile-avatar {
    flex-direction: column;
    text-align: center;
  }
  
  .avatar-placeholder {
    margin-right: 0;
    margin-bottom: 1rem;
  }
}
</style>