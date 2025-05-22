<template>
  <div class="settings-section">
    <h2 class="section-title">Configuración</h2>
    
    <div class="settings-container">
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
      
      <div class="settings-content">
        <!-- Sección de perfil -->
        <div v-if="activeSection === 'profile'" class="settings-panel">
          <h3 class="panel-title">Perfil del Propietario</h3>
          
          <div class="profile-header">
            <div class="profile-avatar">
              <div v-if="!profileImage" class="avatar-placeholder">
                <UserIcon class="avatar-icon" />
              </div>
              <img v-else :src="profileImage" alt="Foto de perfil" class="avatar-image" />
              <button class="change-avatar-button" @click="triggerFileInput">
                <CameraIcon class="button-icon" />
                <span>Cambiar foto</span>
              </button>
              <input 
                type="file" 
                ref="fileInput" 
                accept="image/*" 
                @change="handleImageUpload" 
                style="display: none" 
              />
            </div>
            
            <div class="profile-info">
              <h4>{{ profileData.name }}</h4>
              <p class="profile-status">
                <CheckCircleIcon class="status-icon" />
                <span>Propietario verificado</span>
              </p>
              <p class="profile-member-since">Miembro desde: {{ formatDate(profileData.memberSince) }}</p>
            </div>
          </div>
          
          <form class="settings-form" @submit.prevent="saveProfile">
            <div class="form-row">
              <div class="form-group">
                <label for="profile-name">Nombre</label>
                <div class="input-wrapper">
                  <input type="text" id="profile-name" v-model="profileData.name" class="form-input" />
                  <div class="input-glow"></div>
                </div>
              </div>
              
              <div class="form-group">
                <label for="profile-lastname">Apellidos</label>
                <div class="input-wrapper">
                  <input type="text" id="profile-lastname" v-model="profileData.lastname" class="form-input" />
                  <div class="input-glow"></div>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="profile-email">Email</label>
              <div class="input-wrapper">
                <MailIcon class="input-icon" />
                <input type="email" id="profile-email" v-model="profileData.email" class="form-input with-icon" />
                <div class="input-glow"></div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="profile-phone">Teléfono</label>
              <div class="input-wrapper">
                <PhoneIcon class="input-icon" />
                <input type="tel" id="profile-phone" v-model="profileData.phone" class="form-input with-icon" />
                <div class="input-glow"></div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="profile-address">Dirección</label>
              <div class="input-wrapper">
                <HomeIcon class="input-icon" />
                <input type="text" id="profile-address" v-model="profileData.address" class="form-input with-icon" />
                <div class="input-glow"></div>
              </div>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="save-button">
                <SaveIcon class="button-icon" />
                Guardar cambios
              </button>
            </div>
          </form>
        </div>
        
        <!-- Sección de notificaciones -->
        <div v-if="activeSection === 'notifications'" class="settings-panel">
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
              
              <div class="notification-option">
                <div>
                  <h5>Actualizaciones de la plataforma</h5>
                  <p>Recibe emails sobre nuevas características y mejoras</p>
                </div>
                <label class="toggle">
                  <input type="checkbox" v-model="notifications.platformUpdatesEmail" />
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
              
              <div class="notification-option">
                <div>
                  <h5>Mensajes urgentes</h5>
                  <p>Recibe un SMS para mensajes marcados como urgentes</p>
                </div>
                <label class="toggle">
                  <input type="checkbox" v-model="notifications.urgentMessagesSMS" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            
            <div class="notification-group">
              <h4>Notificaciones push</h4>
              
              <div class="notification-option">
                <div>
                  <h5>Activar notificaciones push</h5>
                  <p>Recibe notificaciones en tiempo real en tu navegador</p>
                </div>
                <label class="toggle">
                  <input type="checkbox" v-model="notifications.pushEnabled" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            
            <div class="form-actions">
              <button class="save-button" @click="saveNotifications">
                <SaveIcon class="button-icon" />
                Guardar preferencias
              </button>
            </div>
          </div>
        </div>
        
        <!-- Sección de seguridad -->
        <div v-if="activeSection === 'security'" class="settings-panel">
          <h3 class="panel-title">Seguridad</h3>
          
          <div class="security-settings">
            <div class="security-group">
              <h4>Cambiar contraseña</h4>
              
              <form class="settings-form" @submit.prevent="changePassword">
                <div class="form-group">
                  <label for="current-password">Contraseña actual</label>
                  <div class="password-input-container">
                    <div class="input-wrapper">
                      <LockIcon class="input-icon" />
                      <input 
                        :type="showCurrentPassword ? 'text' : 'password'" 
                        id="current-password" 
                        v-model="passwordData.current" 
                        class="form-input with-icon" 
                      />
                      <div class="input-glow"></div>
                    </div>
                    <button 
                      type="button" 
                      class="password-toggle" 
                      @click="showCurrentPassword = !showCurrentPassword"
                    >
                      <EyeIcon v-if="!showCurrentPassword" class="toggle-icon" />
                      <EyeOffIcon v-else class="toggle-icon" />
                    </button>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="new-password">Nueva contraseña</label>
                  <div class="password-input-container">
                    <div class="input-wrapper">
                      <LockIcon class="input-icon" />
                      <input 
                        :type="showNewPassword ? 'text' : 'password'" 
                        id="new-password" 
                        v-model="passwordData.new" 
                        class="form-input with-icon" 
                      />
                      <div class="input-glow"></div>
                    </div>
                    <button 
                      type="button" 
                      class="password-toggle" 
                      @click="showNewPassword = !showNewPassword"
                    >
                      <EyeIcon v-if="!showNewPassword" class="toggle-icon" />
                      <EyeOffIcon v-else class="toggle-icon" />
                    </button>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="confirm-password">Confirmar nueva contraseña</label>
                  <div class="password-input-container">
                    <div class="input-wrapper">
                      <LockIcon class="input-icon" />
                      <input 
                        :type="showConfirmPassword ? 'text' : 'password'" 
                        id="confirm-password" 
                        v-model="passwordData.confirm" 
                        class="form-input with-icon" 
                      />
                      <div class="input-glow"></div>
                    </div>
                    <button 
                      type="button" 
                      class="password-toggle" 
                      @click="showConfirmPassword = !showConfirmPassword"
                    >
                      <EyeIcon v-if="!showConfirmPassword" class="toggle-icon" />
                      <EyeOffIcon v-else class="toggle-icon" />
                    </button>
                  </div>
                </div>
                
                <div class="password-requirements">
                  <h5>Requisitos de contraseña:</h5>
                  <ul>
                    <li :class="{ valid: passwordStrength.length }">Al menos 8 caracteres</li>
                    <li :class="{ valid: passwordStrength.uppercase }">Al menos una letra mayúscula</li>
                    <li :class="{ valid: passwordStrength.lowercase }">Al menos una letra minúscula</li>
                    <li :class="{ valid: passwordStrength.number }">Al menos un número</li>
                    <li :class="{ valid: passwordStrength.special }">Al menos un carácter especial</li>
                  </ul>
                </div>
                
                <div class="form-actions">
                  <button type="submit" class="save-button" :disabled="!isPasswordValid">
                    <KeyIcon class="button-icon" />
                    Cambiar contraseña
                  </button>
                </div>
              </form>
            </div>
            
            <div class="security-group">
              <h4>Sesiones activas</h4>
              
              <div class="sessions-list">
                <div v-for="(session, index) in activeSessions" :key="index" class="session-item">
                  <div class="session-info">
                    <div class="session-device">
                      <SmartphoneIcon v-if="session.device === 'mobile'" class="device-icon" />
                      <MonitorIcon v-else class="device-icon" />
                      <div>
                        <h5>{{ session.deviceName }}</h5>
                        <p>{{ session.location }} · {{ session.browser }}</p>
                      </div>
                    </div>
                    <div class="session-status" :class="{ current: session.current }">
                      {{ session.current ? 'Sesión actual' : 'Activa' }}
                    </div>
                  </div>
                  <button 
                    v-if="!session.current" 
                    class="close-session-button"
                    @click="closeSession(session.id)"
                  >
                    <LogOutIcon class="button-icon" />
                    Cerrar sesión
                  </button>
                </div>
              </div>
              
              <button class="close-all-sessions-button" @click="closeAllSessions">
                <LogOutIcon class="button-icon" />
                Cerrar todas las sesiones
              </button>
            </div>
            
            <div class="security-group">
              <h4>Verificación en dos pasos</h4>
              
              <div class="two-factor-auth">
                <div class="two-factor-header">
                  <div>
                    <h5>Autenticación de dos factores</h5>
                    <p>Añade una capa extra de seguridad a tu cuenta</p>
                  </div>
                  <label class="toggle">
                    <input type="checkbox" v-model="twoFactorEnabled" @change="toggleTwoFactor" />
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                <div v-if="twoFactorEnabled" class="two-factor-setup">
                  <p>La autenticación de dos factores está activada. Recibirás un código por SMS cada vez que inicies sesión desde un nuevo dispositivo.</p>
                  
                  <div class="form-group">
                    <label for="phone-number">Número de teléfono para verificación</label>
                    <div class="input-wrapper">
                      <PhoneIcon class="input-icon" />
                      <input type="tel" id="phone-number" v-model="twoFactorPhone" class="form-input with-icon" />
                      <div class="input-glow"></div>
                    </div>
                  </div>
                  
                  <button class="save-button" @click="saveTwoFactorSettings">
                    <SaveIcon class="button-icon" />
                    Guardar configuración
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Sección de pagos -->
        <div v-if="activeSection === 'payments'" class="settings-panel">
          <h3 class="panel-title">Métodos de Pago</h3>
          
          <div class="payment-methods">
            <div v-if="paymentMethods.length > 0" class="payment-list">
              <div v-for="(method, index) in paymentMethods" :key="index" class="payment-method-card">
                <div class="payment-method-icon" :class="method.type">
                  <CreditCardIcon v-if="method.type === 'credit-card'" class="method-icon" />
                  <BuildingIcon v-else-if="method.type === 'bank'" class="method-icon" />
                  <SmartphoneIcon v-else class="method-icon" />
                </div>
                
                <div class="payment-method-details">
                  <h4>{{ getMethodTitle(method) }}</h4>
                  <p>{{ getMethodDescription(method) }}</p>
                </div>
                
                <div class="payment-method-actions">
                  <button class="edit-button" @click="editPaymentMethod(index)">
                    <EditIcon class="action-icon" />
                  </button>
                  <button class="delete-button" @click="deletePaymentMethod(index)">
                    <TrashIcon class="action-icon" />
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="empty-payment-methods">
              <CreditCardIcon class="empty-icon" />
              <p>No tienes métodos de pago guardados</p>
            </div>
            
            <button class="add-payment-button" @click="showAddPaymentModal = true">
              <PlusIcon class="button-icon" />
              Añadir método de pago
            </button>
          </div>
          
          <div class="payment-preferences">
            <h4>Preferencias de pago</h4>
            
            <div class="form-group">
              <label for="default-currency">Moneda predeterminada</label>
              <div class="dropdown-wrapper">
                <div class="selected-option" @click="toggleCurrencyDropdown">
                  <span>{{ getCurrencyLabel(paymentPreferences.defaultCurrency) }}</span>
                  <ChevronDownIcon class="dropdown-icon" :class="{ 'rotate': showCurrencyDropdown }" />
                </div>
                <div class="dropdown-menu" v-if="showCurrencyDropdown">
                  <div 
                    v-for="currency in currencies" 
                    :key="currency.value" 
                    class="dropdown-item"
                    :class="{ active: paymentPreferences.defaultCurrency === currency.value }"
                    @click="selectCurrency(currency.value)"
                  >
                    {{ currency.label }}
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="payment-schedule">Programación de pagos</label>
              <div class="dropdown-wrapper">
                <div class="selected-option" @click="toggleScheduleDropdown">
                  <span>{{ getScheduleLabel(paymentPreferences.paymentSchedule) }}</span>
                  <ChevronDownIcon class="dropdown-icon" :class="{ 'rotate': showScheduleDropdown }" />
                </div>
                <div class="dropdown-menu" v-if="showScheduleDropdown">
                  <div 
                    v-for="schedule in schedules" 
                    :key="schedule.value" 
                    class="dropdown-item"
                    :class="{ active: paymentPreferences.paymentSchedule === schedule.value }"
                    @click="selectSchedule(schedule.value)"
                  >
                    {{ schedule.label }}
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group checkbox-group">
              <input type="checkbox" id="auto-payout" v-model="paymentPreferences.autoPayout" />
              <label for="auto-payout">Habilitar pagos automáticos</label>
            </div>
            
            <button class="save-preferences-button" @click="savePaymentPreferences">
              <SaveIcon class="button-icon" />
              Guardar preferencias
            </button>
          </div>
        </div>
        
        <!-- Sección de privacidad -->
        <div v-if="activeSection === 'privacy'" class="settings-panel">
          <h3 class="panel-title">Privacidad</h3>
          
          <div class="privacy-settings">
            <div class="privacy-group">
              <h4>Visibilidad del perfil</h4>
              
              <div class="privacy-option">
                <div>
                  <h5>Mostrar mi perfil a usuarios registrados</h5>
                  <p>Permite que otros usuarios vean tu información de perfil</p>
                </div>
                <label class="toggle">
                  <input type="checkbox" v-model="privacySettings.showProfile" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
              
              <div class="privacy-option">
                <div>
                  <h5>Mostrar mis propiedades en búsquedas públicas</h5>
                  <p>Permite que tus propiedades aparezcan en resultados de búsqueda</p>
                </div>
                <label class="toggle">
                  <input type="checkbox" v-model="privacySettings.showProperties" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            
            <div class="privacy-group">
              <h4>Datos y cookies</h4>
              
              <div class="privacy-option">
                <div>
                  <h5>Compartir datos de uso</h5>
                  <p>Ayúdanos a mejorar compartiendo datos anónimos de uso</p>
                </div>
                <label class="toggle">
                  <input type="checkbox" v-model="privacySettings.shareUsageData" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
              
              <div class="privacy-option">
                <div>
                  <h5>Cookies de terceros</h5>
                  <p>Permitir cookies de terceros para personalizar tu experiencia</p>
                </div>
                <label class="toggle">
                  <input type="checkbox" v-model="privacySettings.thirdPartyCookies" />
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            
            <div class="privacy-group">
              <h4>Gestión de cuenta</h4>
              
              <button class="download-data-button" @click="downloadPersonalData">
                <DownloadIcon class="button-icon" />
                Descargar mis datos personales
              </button>
              
              <button class="delete-account-button" @click="showDeleteAccountConfirmation = true">
                <TrashIcon class="button-icon" />
                Eliminar mi cuenta
              </button>
            </div>
            
            <div class="form-actions">
              <button class="save-button" @click="savePrivacySettings">
                <SaveIcon class="button-icon" />
                Guardar configuración
              </button>
            </div>
          </div>
          
          <!-- Modal de confirmación para eliminar cuenta -->
          <div v-if="showDeleteAccountConfirmation" class="modal-overlay" @click="showDeleteAccountConfirmation = false">
            <div class="modal-content" @click.stop>
              <div class="modal-header">
                <h3>Eliminar cuenta</h3>
                <button class="close-button" @click="showDeleteAccountConfirmation = false">
                  <XIcon class="close-icon" />
                </button>
              </div>
              <div class="modal-body">
                <AlertTriangleIcon class="warning-icon" />
                <h4>¿Estás seguro de que quieres eliminar tu cuenta?</h4>
                <p>Esta acción es irreversible y eliminará todos tus datos, propiedades y reservas. No podrás recuperar esta información después.</p>
                
                <div class="confirmation-input">
                  <label for="delete-confirmation">Para confirmar, escribe "ELIMINAR" en el campo de abajo:</label>
                  <input type="text" id="delete-confirmation" v-model="deleteConfirmation" class="form-input" />
                </div>
                
                <div class="modal-actions">
                  <button class="cancel-button" @click="showDeleteAccountConfirmation = false">
                    Cancelar
                  </button>
                  <button 
                    class="delete-button" 
                    :disabled="deleteConfirmation !== 'ELIMINAR'"
                    @click="deleteAccount"
                  >
                    Eliminar mi cuenta
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { 
  UserIcon, 
  BellIcon, 
  ShieldIcon, 
  CreditCardIcon,
  LockIcon,
  MailIcon,
  PhoneIcon,
  HomeIcon,
  EyeIcon,
  EyeOffIcon,
  SaveIcon,
  CheckCircleIcon,
  SmartphoneIcon,
  MonitorIcon,
  LogOutIcon,
  PlusIcon,
  EditIcon,
  TrashIcon,
  BuildingIcon,
  ChevronDownIcon,
  DownloadIcon,
  XIcon,
  AlertTriangleIcon,
  KeyIcon,
  CameraIcon
} from 'lucide-vue-next';

// Secciones de configuración
const sections = [
  { id: 'profile', name: 'Perfil', icon: UserIcon },
  { id: 'notifications', name: 'Notificaciones', icon: BellIcon },
  { id: 'security', name: 'Seguridad', icon: LockIcon },
  { id: 'payments', name: 'Pagos', icon: CreditCardIcon },
  { id: 'privacy', name: 'Privacidad', icon: ShieldIcon }
];

// Sección activa
const activeSection = ref('profile');

// Datos de perfil
const profileData = ref({
  name: 'Carlos',
  lastname: 'Rodríguez',
  email: 'carlos@example.com',
  phone: '+34 612 345 678',
  address: 'Calle Principal 123, Madrid',
  memberSince: '2023-01-15'
});

const profileImage = ref(null);
const fileInput = ref(null);

// Notificaciones
const notifications = ref({
  newBookingEmail: true,
  newMessageEmail: true,
  platformUpdatesEmail: false,
  newBookingSMS: false,
  urgentMessagesSMS: true,
  pushEnabled: true
});

// Datos de contraseña
const passwordData = ref({
  current: '',
  new: '',
  confirm: ''
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Fortaleza de la contraseña
const passwordStrength = computed(() => {
  const password = passwordData.value.new;
  return {
    length: password.length >= 8,
    uppercase: /[A-Z]/.test(password),
    lowercase: /[a-z]/.test(password),
    number: /[0-9]/.test(password),
    special: /[^A-Za-z0-9]/.test(password)
  };
});

const isPasswordValid = computed(() => {
  const strength = passwordStrength.value;
  return (
    strength.length && 
    strength.uppercase && 
    strength.lowercase && 
    strength.number && 
    strength.special &&
    passwordData.value.new === passwordData.value.confirm
  );
});

// Sesiones activas
const activeSessions = ref([
  {
    id: 1,
    deviceName: 'MacBook Pro',
    device: 'desktop',
    browser: 'Chrome',
    location: 'Madrid, España',
    lastActive: '2023-08-10T14:30:00',
    current: true
  },
  {
    id: 2,
    deviceName: 'iPhone 13',
    device: 'mobile',
    browser: 'Safari',
    location: 'Barcelona, España',
    lastActive: '2023-08-09T10:15:00',
    current: false
  }
]);

// Autenticación de dos factores
const twoFactorEnabled = ref(false);
const twoFactorPhone = ref('+34 612 345 678');

// Métodos de pago
const paymentMethods = ref([
  {
    id: 1,
    type: 'credit-card',
    cardNumber: '•••• •••• •••• 4242',
    expiryDate: '12/25',
    isDefault: true
  },
  {
    id: 2,
    type: 'bank',
    accountHolder: 'Carlos Rodríguez',
    iban: 'ES91 •••• •••• •••• •••• 1332',
    bankName: 'CaixaBank'
  }
]);

const showAddPaymentModal = ref(false);

// Preferencias de pago
const paymentPreferences = ref({
  defaultCurrency: 'EUR',
  paymentSchedule: 'immediate',
  autoPayout: true
});

// Dropdown states
const showCurrencyDropdown = ref(false);
const showScheduleDropdown = ref(false);

// Opciones para los dropdowns
const currencies = [
  { value: 'EUR', label: 'EUR (€)' },
  { value: 'USD', label: 'USD ($)' },
  { value: 'GBP', label: 'GBP (£)' }
];

const schedules = [
  { value: 'immediate', label: 'Inmediato' },
  { value: 'weekly', label: 'Semanal' },
  { value: 'monthly', label: 'Mensual' }
];

// Configuración de privacidad
const privacySettings = ref({
  showProfile: true,
  showProperties: true,
  shareUsageData: true,
  thirdPartyCookies: true
});

const showDeleteAccountConfirmation = ref(false);
const deleteConfirmation = ref('');

// Métodos
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      profileImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const saveProfile = () => {
  // Aquí iría la lógica para guardar los datos del perfil
  console.log('Guardando perfil:', profileData.value);
  alert('Perfil guardado correctamente');
};

const saveNotifications = () => {
  // Aquí iría la lógica para guardar las preferencias de notificaciones
  console.log('Guardando notificaciones:', notifications.value);
  alert('Preferencias de notificaciones guardadas correctamente');
};

const changePassword = () => {
  if (!isPasswordValid.value) {
    alert('Por favor, asegúrate de que la nueva contraseña cumple con todos los requisitos y que las contraseñas coinciden.');
    return;
  }
  
  // Aquí iría la lógica para cambiar la contraseña
  console.log('Cambiando contraseña');
  alert('Contraseña cambiada correctamente');
  
  // Resetear los campos
  passwordData.value = {
    current: '',
    new: '',
    confirm: ''
  };
};

const closeSession = (sessionId) => {
  // Aquí iría la lógica para cerrar una sesión específica
  console.log('Cerrando sesión:', sessionId);
  activeSessions.value = activeSessions.value.filter(session => session.id !== sessionId);
};

const closeAllSessions = () => {
  // Aquí iría la lógica para cerrar todas las sesiones excepto la actual
  console.log('Cerrando todas las sesiones');
  activeSessions.value = activeSessions.value.filter(session => session.current);
  alert('Todas las demás sesiones han sido cerradas');
};

const toggleTwoFactor = () => {
  if (twoFactorEnabled.value) {
    console.log('Activando autenticación de dos factores');
  } else {
    console.log('Desactivando autenticación de dos factores');
  }
};

const saveTwoFactorSettings = () => {
  console.log('Guardando configuración de dos factores:', {
    enabled: twoFactorEnabled.value,
    phone: twoFactorPhone.value
  });
  alert('Configuración de autenticación de dos factores guardada');
};

const getMethodTitle = (method) => {
  if (method.type === 'credit-card') {
    return `Tarjeta terminada en ${method.cardNumber.slice(-4)}`;
  } else if (method.type === 'bank') {
    return `Cuenta bancaria (${method.bankName})`;
  } else {
    return `Pago móvil`;
  }
};

const getMethodDescription = (method) => {
  if (method.type === 'credit-card') {
    return `Expira: ${method.expiryDate}${method.isDefault ? ' · Predeterminada' : ''}`;
  } else if (method.type === 'bank') {
    return `IBAN: ${method.iban}${method.isDefault ? ' · Predeterminada' : ''}`;
  } else {
    return `${method.mobileNumber || ''}${method.isDefault ? ' · Predeterminada' : ''}`;
  }
};

const editPaymentMethod = (index) => {
  console.log('Editando método de pago:', paymentMethods.value[index]);
  // Aquí iría la lógica para editar un método de pago
};

const deletePaymentMethod = (index) => {
  if (confirm('¿Estás seguro de que quieres eliminar este método de pago?')) {
    paymentMethods.value.splice(index, 1);
  }
};

// Métodos para los dropdowns
const toggleCurrencyDropdown = () => {
  showCurrencyDropdown.value = !showCurrencyDropdown.value;
  if (showCurrencyDropdown.value) {
    showScheduleDropdown.value = false;
  }
};

const toggleScheduleDropdown = () => {
  showScheduleDropdown.value = !showScheduleDropdown.value;
  if (showScheduleDropdown.value) {
    showCurrencyDropdown.value = false;
  }
};

const selectCurrency = (value) => {
  paymentPreferences.value.defaultCurrency = value;
  showCurrencyDropdown.value = false;
};

const selectSchedule = (value) => {
  paymentPreferences.value.paymentSchedule = value;
  showScheduleDropdown.value = false;
};

const getCurrencyLabel = (value) => {
  const currency = currencies.find(c => c.value === value);
  return currency ? currency.label : value;
};

const getScheduleLabel = (value) => {
  const schedule = schedules.find(s => s.value === value);
  return schedule ? schedule.label : value;
};

const savePaymentPreferences = () => {
  console.log('Guardando preferencias de pago:', paymentPreferences.value);
  alert('Preferencias de pago guardadas correctamente');
};

const savePrivacySettings = () => {
  console.log('Guardando configuración de privacidad:', privacySettings.value);
  alert('Configuración de privacidad guardada correctamente');
};

const downloadPersonalData = () => {
  console.log('Descargando datos personales');
  alert('Se ha iniciado la descarga de tus datos personales');
};

const deleteAccount = () => {
  if (deleteConfirmation.value === 'ELIMINAR') {
    console.log('Eliminando cuenta');
    alert('Tu cuenta ha sido eliminada. Serás redirigido a la página de inicio.');
    // Aquí iría la lógica para eliminar la cuenta y redirigir al usuario
  }
};

// Cerrar dropdowns al hacer clic fuera
document.addEventListener('click', () => {
  showCurrencyDropdown.value = false;
  showScheduleDropdown.value = false;
});
</script>

<style scoped>
.settings-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 60vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  position: relative;
  overflow: hidden;
}

.settings-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(0, 113, 194, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(0, 53, 128, 0.03) 0%, transparent 60%);
  z-index: 0;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 2rem;
  position: relative;
  z-index: 1;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.settings-container {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
  position: relative;
  z-index: 1;
}

.settings-sidebar {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
  height: fit-content;
}

.sidebar-button {
  display: flex;
  align-items: center;
  padding: 1rem 1.5rem;
  background: none;
  border: none;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #64748b;
  border-left: 3px solid transparent;
}

.sidebar-button:hover {
  background-color: #f0f7ff;
  color: #0071c2;
}

.sidebar-button.active {
  background-color: #e6f0ff;
  color: #0071c2;
  border-left: 3px solid #0071c2;
  font-weight: 500;
}

.sidebar-icon {
  width: 20px;
  height: 20px;
  margin-right: 1rem;
}

.settings-panel {
  background-color: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.panel-title {
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 2rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e6f0ff;
}

/* Perfil */
.profile-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
}

.profile-avatar {
  position: relative;
}

.avatar-placeholder {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
}

.avatar-icon {
  width: 50px;
  height: 50px;
  color: #0071c2;
}

.avatar-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 0.5rem;
}

.change-avatar-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background-color: #f0f7ff;
  color: #0071c2;
  border: none;
  padding: 0.5rem;
  border-radius: 8px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
}

.change-avatar-button:hover {
  background-color: #e6f0ff;
}

.button-icon {
  width: 16px;
  height: 16px;
}

.profile-info {
  flex: 1;
}

.profile-info h4 {
  font-size: 1.5rem;
  color: #1e293b;
  margin: 0 0 0.5rem;
}

.profile-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #10b981;
  margin-bottom: 0.5rem;
}

.status-icon {
  width: 16px;
  height: 16px;
}

.profile-member-since {
  color: #64748b;
  font-size: 0.9rem;
}

.settings-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #1e293b;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #0071c2;
  width: 18px;
  height: 18px;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  color: #1e293b;
  background-color: #f8fafc;
}

.form-input.with-icon {
  padding-left: 2.5rem;
}

.input-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 8px;
  pointer-events: none;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #0071c2;
  background-color: white;
}

.form-input:focus + .input-glow {
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 1rem;
}

.save-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
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

.save-button:disabled {
  background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Notificaciones */
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
  font-size: 1.2rem;
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

input:checked + .toggle-slider {
  background-color: #0071c2;
}

input:focus + .toggle-slider {
  box-shadow: 0 0 1px #0071c2;
}

input:checked + .toggle-slider:before {
  transform: translateX(26px);
}

/* Seguridad */
.security-settings {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.security-group {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.security-group h4 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
  font-weight: 600;
}

.password-input-container {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.toggle-icon {
  width: 18px;
  height: 18px;
}

.password-requirements {
  background-color: #f8fafc;
  border-radius: 8px;
  padding: 1rem;
  border: 1px solid #e6f0ff;
}

.password-requirements h5 {
  font-size: 1rem;
  color: #1e293b;
  margin: 0 0 0.75rem;
}

.password-requirements ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.password-requirements li {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
  color: #64748b;
}

.password-requirements li::before {
  content: '✕';
  color: #ef4444;
  margin-right: 0.5rem;
}

.password-requirements li.valid::before {
  content: '✓';
  color: #10b981;
}

.sessions-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.session-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.session-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex: 1;
}

.session-device {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.device-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
}

.session-device h5 {
  font-size: 1rem;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.session-device p {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
}

.session-status {
  font-size: 0.9rem;
  color: #64748b;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  background-color: #f1f5f9;
}

.session-status.current {
  color: #10b981;
  background-color: #ecfdf5;
}

.close-session-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #fff2f0;
  color: #ef4444;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.close-session-button:hover {
  background-color: #fee2e2;
}

.close-all-sessions-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background-color: #fff2f0;
  color: #ef4444;
  border: 1px solid #ef4444;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
}

.close-all-sessions-button:hover {
  background-color: #fee2e2;
}

.two-factor-auth {
  background-color: #f8fafc;
  border-radius: 8px;
  padding: 1.5rem;
  border: 1px solid #e6f0ff;
}

.two-factor-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.two-factor-header h5 {
  font-size: 1rem;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.two-factor-header p {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
}

.two-factor-setup {
  border-top: 1px solid #e6f0ff;
  padding-top: 1.5rem;
}

.two-factor-setup p {
  margin-bottom: 1.5rem;
  color: #1e293b;
}

/* Pagos */
.payment-methods {
  margin-bottom: 2.5rem;
}

.payment-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.payment-method-card {
  display: flex;
  align-items: center;
  background-color: #f8fafc;
  border-radius: 8px;
  padding: 1rem;
  border: 1px solid #e6f0ff;
  transition: all 0.3s ease;
}

.payment-method-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 53, 128, 0.1);
}

.payment-method-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  flex-shrink: 0;
}

.payment-method-icon.credit-card {
  background-color: #e6f0ff;
}

.payment-method-icon.bank {
  background-color: #e6f7ee;
}

.payment-method-icon.mobile {
  background-color: #fff8e6;
}

.method-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
}

.payment-method-icon.bank .method-icon {
  color: #00703c;
}

.payment-method-icon.mobile .method-icon {
  color: #b27b00;
}

.payment-method-details {
  flex: 1;
}

.payment-method-details h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  color: #1e293b;
}

.payment-method-details p {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
}

.payment-method-actions {
  display: flex;
  gap: 0.5rem;
}

.edit-button, .delete-button {
  background: none;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.edit-button {
  color: #0071c2;
}

.delete-button {
  color: #ef4444;
}

.edit-button:hover {
  background-color: #e6f0ff;
}

.delete-button:hover {
  background-color: #fff2f0;
}

.action-icon {
  width: 18px;
  height: 18px;
}

.empty-payment-methods {
  text-align: center;
  padding: 2rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
  margin-bottom: 1.5rem;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #cce0ff;
  margin-bottom: 1rem;
}

.add-payment-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.add-payment-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.payment-preferences {
  background-color: #f8fafc;
  border-radius: 8px;
  padding: 1.5rem;
  border: 1px solid #e6f0ff;
}

.payment-preferences h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.dropdown-wrapper {
  position: relative;
}

.selected-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.875rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  background-color: #f8fafc;
  cursor: pointer;
  transition: all 0.3s ease;
}

.selected-option:hover {
  background-color: #f0f7ff;
}

.dropdown-icon {
  width: 18px;
  height: 18px;
  color: #64748b;
  transition: transform 0.3s ease;
}

.dropdown-icon.rotate {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 0;
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 10;
  overflow: hidden;
}

.dropdown-item {
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background-color: #f0f7ff;
}

.dropdown-item.active {
  background-color: #e6f0ff;
  color: #0071c2;
  font-weight: 500;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 1.5rem 0;
}

.checkbox-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #0071c2;
}

.save-preferences-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.save-preferences-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

/* Privacidad */
.privacy-settings {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.privacy-group {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.privacy-group h4 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0 0 0.5rem;
  font-weight: 600;
}

.privacy-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.privacy-option h5 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  color: #1e293b;
}

.privacy-option p {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
}

.download-data-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background-color: #f0f7ff;
  color: #0071c2;
  border: 1px solid #0071c2;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  margin-bottom: 1rem;
}

.download-data-button:hover {
  background-color: #e6f0ff;
}

.delete-account-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background-color: #fff2f0;
  color: #ef4444;
  border: 1px solid #ef4444;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
}

.delete-account-button:hover {
  background-color: #fee2e2;
}

/* Modal de confirmación */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background-color: #ef4444;
  color: white;
}

.modal-header h3 {
  font-size: 1.2rem;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 2rem;
  text-align: center;
}

.warning-icon {
  width: 64px;
  height: 64px;
  color: #ef4444;
  margin-bottom: 1.5rem;
}

.modal-body h4 {
  font-size: 1.2rem;
  color: #1e293b;
  margin: 0 0 1rem;
}

.modal-body p {
  color: #64748b;
  margin-bottom: 1.5rem;
}

.confirmation-input {
  margin-bottom: 1.5rem;
  text-align: left;
}

.confirmation-input label {
  display: block;
  font-weight: 500;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.modal-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.cancel-button {
  background-color: #f1f5f9;
  color: #64748b;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover {
  background-color: #e2e8f0;
}

.delete-button {
  background-color: #ef4444;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.delete-button:hover {
  background-color: #dc2626;
}

.delete-button:disabled {
  background-color: #fca5a5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 992px) {
  .settings-container {
    grid-template-columns: 1fr;
  }
  
  .settings-sidebar {
    margin-bottom: 1.5rem;
  }
}

@media (max-width: 768px) {
  .settings-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }
  
  .profile-header {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .form-row {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .session-item {
    flex-direction: column;
    gap: 1rem;
  }
  
  .session-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .close-session-button {
    width: 100%;
    justify-content: center;
  }
  
  .modal-actions {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .cancel-button, .delete-button {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .settings-section {
    padding: 1rem;
    margin: 1rem;
  }
  
  .settings-panel {
    padding: 1.5rem;
  }
  
  .notification-option {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .notification-option .toggle {
    align-self: flex-start;
  }
  
  .privacy-option {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .privacy-option .toggle {
    align-self: flex-start;
  }
}
</style>