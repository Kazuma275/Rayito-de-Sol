<template>
  <div class="settings-panel">
    <h3 class="panel-title">Notificaciones</h3>
    
    <div class="notification-settings">
      <div class="notification-group">
        <h4>Email</h4>
        
        <div class="notification-option">
          <div>
            <h5>Nuevas reservas</h5>
            <p>Recibe un email cuando recibas una nueva reserva</p>
          </div>
          <label class="toggle">
            <input type="checkbox" v-model="notifications.newBookingEmail" />
            <span class="toggle-slider"></span>
          </label>
        </div>
        
        <div class="notification-option">
          <div>
            <h5>Mensajes</h5>
            <p>Recibe un email cuando recibas un nuevo mensaje</p>
          </div>
          <label class="toggle">
            <input type="checkbox" v-model="notifications.newMessageEmail" />
            <span class="toggle-slider"></span>
          </label>
        </div>
      </div>
      
      <div class="notification-group">
        <h4>SMS</h4>
        
        <div class="notification-option">
          <div>
            <h5>Nuevas reservas</h5>
            <p>Recibe un SMS cuando recibas una nueva reserva</p>
          </div>
          <label class="toggle">
            <input type="checkbox" v-model="notifications.newBookingSMS" />
            <span class="toggle-slider"></span>
          </label>
        </div>
      </div>
      
      <div class="form-actions">
        <button class="save-button">Guardar preferencias</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const notifications = ref({
  newBookingEmail: true,
  newMessageEmail: true,
  newBookingSMS: false
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

.notification-settings {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.notification-group {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.notification-group h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 0.5rem;
}

.notification-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-option h5 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.notification-option p {
  font-size: 0.9rem;
  color: #666;
  margin: 0;
}

.toggle {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
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

.toggle-slider:before {
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

.toggle input:checked + .toggle-slider {
  background-color: #0071c2;
}

.toggle input:checked + .toggle-slider:before {
  transform: translateX(26px);
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
</style>