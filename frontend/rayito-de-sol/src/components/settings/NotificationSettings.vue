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
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e6f0ff;
}

.notification-settings {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.notification-group {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.notification-group h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 0.5rem;
  font-weight: 600;
}

.notification-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.notification-option h5 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  color: #1e293b;
}

.notification-option p {
  font-size: 0.9rem;
  color: #64748b;
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
  .notification-option {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .toggle {
    align-self: flex-start;
  }
}
</style>