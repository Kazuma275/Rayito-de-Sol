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
  font-size: 1.2rem;
  color: #003580;
  margin: 0 0 1.5rem;
}

.profile-avatar {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
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
}

.avatar-icon {
  width: 40px;
  height: 40px;
  color: #0071c2;
}

.change-avatar-button {
  background-color: #f5f5f5;
  color: #333;
  border: 1px solid #ccc;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.change-avatar-button:hover {
  background-color: #eee;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.save-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
}

.save-button:hover {
  background-color: #005999;
}

@media (max-width: 576px) {
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>