<template>
  <div class="settings-panel">
    <h3 class="panel-title">Perfil</h3>
    <form class="settings-form" @submit.prevent="onSubmit">
      <div class="profile-avatar">
        <div class="avatar-placeholder">
          <img v-if="previewImage" :src="previewImage" class="avatar-preview" />
          <UserIcon v-else class="avatar-icon" />
        </div>
        <button type="button" class="change-avatar-button" @click="triggerFileInput">Cambiar foto</button>
        <input
          type="file"
          ref="avatarInput"
          accept="image/*"
          @change="onAvatarChange"
          style="display: none"
        />
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="profile-name">Nombre</label>
          <input id="profile-name" type="text" v-model="localProfile.name" class="form-input" />
        </div>
        <div class="form-group">
          <label for="profile-lastname">Apellidos</label>
          <input id="profile-lastname" type="text" v-model="localProfile.lastname" class="form-input" />
        </div>
      </div>
      <div class="form-group">
        <label for="profile-email">Email</label>
        <input id="profile-email" type="email" v-model="localProfile.email" class="form-input" />
      </div>
      <div class="form-group">
        <label for="profile-phone">Teléfono</label>
        <input id="profile-phone" type="tel" v-model="localProfile.phone" class="form-input" />
      </div>
      <div class="form-actions">
        <button type="submit" class="save-button">Guardar cambios</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { UserIcon } from 'lucide-vue-next'

const props = defineProps({
  profile: Object,
  previewImage: String
})
const emit = defineEmits(['change-avatar', 'save'])

const localProfile = ref({ ...props.profile })
watch(
  () => props.profile,
  (val) => { localProfile.value = { ...val } }
)

const avatarInput = ref(null)
function triggerFileInput() { avatarInput.value.click() }
function onAvatarChange(e) {
  const file = e.target.files[0]
  if (file && file.type.startsWith('image/')) emit('change-avatar', file)
}
function onSubmit() { emit('save', localProfile.value) }
</script>