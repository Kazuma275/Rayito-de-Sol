<template>
  <div>
    <h2>Configuración de cuenta</h2>
    <form @submit.prevent="saveUserData">
      <div>
        <label for="name">Nombre</label>
        <input id="name" v-model="userData.name" />
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" v-model="userData.email" />
      </div>
      <div>
        <label for="phone">Teléfono</label>
        <input id="phone" v-model="userData.phone" />
      </div>
      <button type="submit" :disabled="isSaving">
        {{ isSaving ? 'Guardando...' : 'Guardar cambios' }}
      </button>
    </form>
    <div style="margin-top:2em">
      <h3>Cambiar contraseña</h3>
      <form @submit.prevent="changePassword">
        <div>
          <label for="currentPassword">Contraseña actual</label>
          <input id="currentPassword" type="password" v-model="passwordData.currentPassword" />
        </div>
        <div>
          <label for="newPassword">Nueva contraseña</label>
          <input id="newPassword" type="password" v-model="passwordData.newPassword" />
        </div>
        <div>
          <label for="confirmPassword">Confirmar nueva contraseña</label>
          <input id="confirmPassword" type="password" v-model="passwordData.confirmPassword" />
        </div>
        <div v-if="passwordError" style="color:red">{{ passwordError }}</div>
        <button type="submit" :disabled="isChangingPassword">
          {{ isChangingPassword ? 'Cambiando...' : 'Cambiar contraseña' }}
        </button>
      </form>
    </div>
    <div v-if="saveMessage">{{ saveMessage }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Estado del usuario
const userData = ref({
  name: '',
  email: '',
  phone: ''
})
const isSaving = ref(false)
const isChangingPassword = ref(false)
const saveMessage = ref('')

// Para contraseña
const passwordData = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})
const passwordError = ref('')

// Cargar usuario actual al montar
onMounted(async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (token) {
      const { data } = await axios.get('http://localhost:8000/api/user/profile', {
        headers: { Authorization: `Bearer ${token}` }
      })
      if (data.user) {
        userData.value.name = data.user.name || ''
        userData.value.email = data.user.email || ''
        userData.value.phone = data.user.phone || ''
      }
    }
  } catch (e) {
    saveMessage.value = 'No se pudo cargar el usuario'
  }
})

// Guardar datos modificados
const saveUserData = async () => {
  isSaving.value = true
  saveMessage.value = ''
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) throw new Error('No hay token')
    await axios.patch('http://localhost:8000/api/user/profile', {
      name: userData.value.name,
      email: userData.value.email,
      phone: userData.value.phone
    }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    saveMessage.value = 'Datos guardados correctamente'
  } catch (e) {
    saveMessage.value = 'Error al guardar los datos'
  } finally {
    isSaving.value = false
  }
}

// Cambiar contraseña
const changePassword = async () => {
  passwordError.value = ''
  if (passwordData.value.newPassword !== passwordData.value.confirmPassword) {
    passwordError.value = 'Las contraseñas no coinciden'
    return
  }
  if (passwordData.value.newPassword.length < 8) {
    passwordError.value = 'La contraseña debe tener al menos 8 caracteres'
    return
  }
  isChangingPassword.value = true
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) throw new Error('No hay token')
    await axios.post('http://localhost:8000/api/user/change-password', {
      current_password: passwordData.value.currentPassword,
      new_password: passwordData.value.newPassword
    }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    passwordData.value.currentPassword = ''
    passwordData.value.newPassword = ''
    passwordData.value.confirmPassword = ''
    saveMessage.value = 'Contraseña cambiada correctamente'
  } catch (e) {
    passwordError.value = 'Error al cambiar la contraseña. Verifica la contraseña actual.'
  } finally {
    isChangingPassword.value = false
  }
}
</script>