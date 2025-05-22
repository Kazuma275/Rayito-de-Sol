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
            <input type="checkbox" v-model="localNotifications.newBookingEmail" />
            <span class="toggle-slider"></span>
          </label>
        </div>
        <div class="notification-option">
          <div>
            <h5>Mensajes</h5>
            <p>Recibe un email cuando recibas un nuevo mensaje</p>
          </div>
          <label class="toggle">
            <input type="checkbox" v-model="localNotifications.newMessageEmail" />
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
            <input type="checkbox" v-model="localNotifications.newBookingSMS" />
            <span class="toggle-slider"></span>
          </label>
        </div>
      </div>
      <div class="form-actions">
        <button class="save-button" @click="onSave">Guardar preferencias</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({ notifications: Object })
const emit = defineEmits(['save'])
const localNotifications = ref({ ...props.notifications })
watch(
  () => props.notifications,
  (val) => { localNotifications.value = { ...val } }
)
function onSave() {
  emit('save', localNotifications.value)
}
</script>