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
      
      <div class="settings-main">
        <!-- Sección de cuenta -->
        <div v-if="activeSection === 'account'" class="settings-section">
          <h2 class="section-title">Configuración de cuenta</h2>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" v-model="userData.email" class="form-input" />
          </div>
          
          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-input">
              <input type="password" id="password" value="********" class="form-input" disabled />
              <button class="change-password-button" @click="showPasswordModal = true">Cambiar</button>
            </div>
          </div>
          
          <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="tel" id="phone" v-model="userData.phone" class="form-input" />
          </div>
          
          <button class="save-button" @click="saveUserData" :disabled="isSaving">
            <span v-if="isSaving">Guardando...</span>
            <span v-else>Guardar cambios</span>
          </button>
          
          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
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
              <input type="checkbox" v-model="notificationSettings.email" />
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Notificaciones push</h3>
              <p class="option-description">Recibe notificaciones en tu dispositivo</p>
            </div>
            <label class="switch">
              <input type="checkbox" v-model="notificationSettings.push" />
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Notificaciones de marketing</h3>
              <p class="option-description">Recibe ofertas y promociones especiales</p>
            </div>
            <label class="switch">
              <input type="checkbox" v-model="notificationSettings.marketing" />
              <span class="slider"></span>
            </label>
          </div>
          
          <button class="save-button" @click="saveNotificationSettings" :disabled="isSaving">
            <span v-if="isSaving">Guardando...</span>
            <span v-else>Guardar preferencias</span>
          </button>
          
          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
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
              <input type="checkbox" v-model="privacySettings.shareUsageData" />
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="notification-option">
            <div>
              <h3 class="option-title">Cookies de terceros</h3>
              <p class="option-description">Permitir cookies de terceros para personalizar tu experiencia</p>
            </div>
            <label class="switch">
              <input type="checkbox" v-model="privacySettings.thirdPartyCookies" />
              <span class="slider"></span>
            </label>
          </div>
          
          <button class="save-button" @click="savePrivacySettings" :disabled="isSaving">
            <span v-if="isSaving">Guardando...</span>
            <span v-else>Guardar configuración</span>
          </button>
          
          <button class="delete-account-button" @click="confirmDeleteAccount">Eliminar mi cuenta</button>
          
          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
        </div>
        
        <!-- Sección de pagos -->
        <div v-if="activeSection === 'payments'" class="settings-section">
          <h2 class="section-title">Métodos de pago</h2>
          
          <div v-if="paymentMethods.length > 0" class="payment-methods-list">
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
          
          <div v-else class="empty-state">
            <CreditCardIcon class="empty-icon" />
            <p>No tienes métodos de pago guardados</p>
          </div>
          
          <button class="add-payment-button" @click="showPaymentModal = true">
            <PlusIcon class="button-icon" />
            Añadir método de pago
          </button>
          
          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal para cambiar contraseña -->
    <div v-if="showPasswordModal" class="modal-overlay" @click="showPasswordModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Cambiar contraseña</h3>
          <button class="close-button" @click="showPasswordModal = false">
            <XIcon class="close-icon" />
          </button>
        </div>
        
        <div class="modal-body">
          <form @submit.prevent="changePassword" class="password-form">
            <div class="form-group">
              <label for="current-password">Contraseña actual</label>
              <input type="password" id="current-password" v-model="passwordData.currentPassword" class="form-input" required />
            </div>
            
            <div class="form-group">
              <label for="new-password">Nueva contraseña</label>
              <input type="password" id="new-password" v-model="passwordData.newPassword" class="form-input" required />
            </div>
            
            <div class="form-group">
              <label for="confirm-password">Confirmar nueva contraseña</label>
              <input type="password" id="confirm-password" v-model="passwordData.confirmPassword" class="form-input" required />
            </div>
            
            <div v-if="passwordError" class="error-message">
              {{ passwordError }}
            </div>
            
            <div class="form-actions">
              <button type="button" class="cancel-button" @click="showPasswordModal = false">Cancelar</button>
              <button type="submit" class="save-button" :disabled="isChangingPassword">
                <span v-if="isChangingPassword">Cambiando...</span>
                <span v-else>Cambiar contraseña</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Modal para añadir/editar método de pago -->
    <div v-if="showPaymentModal" class="modal-overlay" @click="showPaymentModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ editingPaymentIndex !== null ? 'Editar método de pago' : 'Añadir método de pago' }}</h3>
          <button class="close-button" @click="showPaymentModal = false">
            <XIcon class="close-icon" />
          </button>
        </div>
        
        <div class="modal-body">
          <form @submit.prevent="savePaymentMethod" class="payment-form">
            <div class="form-group">
              <label for="payment-type">Tipo de método de pago</label>
              <select id="payment-type" v-model="newPaymentMethod.type" class="form-select">
                <option value="credit-card">Tarjeta de crédito</option>
                <option value="bank">Cuenta bancaria</option>
                <option value="mobile">Pago móvil</option>
              </select>
            </div>
            
            <div v-if="newPaymentMethod.type === 'credit-card'">
              <div class="form-group">
                <label for="card-number">Número de tarjeta</label>
                <input type="text" id="card-number" v-model="newPaymentMethod.cardNumber" class="form-input" placeholder="•••• •••• •••• ••••" />
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="expiry-date">Fecha de expiración</label>
                  <input type="text" id="expiry-date" v-model="newPaymentMethod.expiryDate" class="form-input" placeholder="MM/AA" />
                </div>
                
                <div class="form-group">
                  <label for="cvv">CVV</label>
                  <input type="text" id="cvv" v-model="newPaymentMethod.cvv" class="form-input" placeholder="•••" />
                </div>
              </div>
            </div>
            
            <div v-if="newPaymentMethod.type === 'bank'">
              <div class="form-group">
                <label for="account-holder">Titular de la cuenta</label>
                <input type="text" id="account-holder" v-model="newPaymentMethod.accountHolder" class="form-input" />
              </div>
              
              <div class="form-group">
                <label for="iban">IBAN</label>
                <input type="text" id="iban" v-model="newPaymentMethod.iban" class="form-input" placeholder="ES91 2100 0418 4502 0005 1332" />
              </div>
              
              <div class="form-group">
                <label for="bank-name">Nombre del banco</label>
                <input type="text" id="bank-name" v-model="newPaymentMethod.bankName" class="form-input" />
              </div>
            </div>
            
            <div v-if="newPaymentMethod.type === 'mobile'">
              <div class="form-group">
                <label for="mobile-provider">Proveedor</label>
                <select id="mobile-provider" v-model="newPaymentMethod.mobileProvider" class="form-select">
                  <option value="bizum">Bizum</option>
                  <option value="paypal">PayPal</option>
                  <option value="apple-pay">Apple Pay</option>
                  <option value="google-pay">Google Pay</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="mobile-number">Número de teléfono / Email</label>
                <input type="text" id="mobile-number" v-model="newPaymentMethod.mobileNumber" class="form-input" />
              </div>
            </div>
            
            <div class="form-group checkbox-group">
              <input type="checkbox" id="default-payment" v-model="newPaymentMethod.isDefault" />
              <label for="default-payment">Establecer como método de pago predeterminado</label>
            </div>
            
            <div v-if="paymentError" class="error-message">
              {{ paymentError }}
            </div>
            
            <div class="form-actions">
              <button type="button" class="cancel-button" @click="showPaymentModal = false">Cancelar</button>
              <button type="submit" class="save-button" :disabled="isSavingPayment">
                <span v-if="isSavingPayment">Guardando...</span>
                <span v-else>Guardar</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Modal de confirmación para eliminar cuenta -->
    <div v-if="showDeleteAccountModal" class="modal-overlay" @click="showDeleteAccountModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header delete-header">
          <h3>Eliminar cuenta</h3>
          <button class="close-button" @click="showDeleteAccountModal = false">
            <XIcon class="close-icon" />
          </button>
        </div>
        
        <div class="modal-body">
          <div class="delete-warning">
            <AlertTriangleIcon class="warning-icon" />
            <p>Esta acción es permanente y no se puede deshacer. Se eliminarán todos tus datos, reservas y configuraciones.</p>
          </div>
          
          <div class="form-group">
            <label for="delete-confirm">Para confirmar, escribe "ELIMINAR" en el campo de abajo:</label>
            <input type="text" id="delete-confirm" v-model="deleteConfirmText" class="form-input" />
          </div>
          
          <div class="form-actions">
            <button type="button" class="cancel-button" @click="showDeleteAccountModal = false">Cancelar</button>
            <button 
              type="button" 
              class="delete-button" 
              :disabled="deleteConfirmText !== 'ELIMINAR' || isDeletingAccount"
              @click="deleteAccount"
            >
              <span v-if="isDeletingAccount">Eliminando...</span>
              <span v-else>Eliminar mi cuenta</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { 
  UserIcon, 
  BellIcon, 
  ShieldIcon, 
  CreditCardIcon,
  EditIcon,
  TrashIcon,
  PlusIcon,
  XIcon,
  BuildingIcon,
  SmartphoneIcon,
  AlertTriangleIcon
} from 'lucide-vue-next';
import { useUserStore } from '@/stores/userStore.js';

const userStore = useUserStore();

// Secciones de configuración
const sections = [
  { id: 'account', name: 'Cuenta', icon: UserIcon },
  { id: 'notifications', name: 'Notificaciones', icon: BellIcon },
  { id: 'privacy', name: 'Privacidad', icon: ShieldIcon },
  { id: 'payments', name: 'Pagos', icon: CreditCardIcon }
];

// Estado activo
const activeSection = ref('account');
const isSaving = ref(false);
const isChangingPassword = ref(false);
const isSavingPayment = ref(false);
const isDeletingAccount = ref(false);
const saveMessage = ref(null);
const passwordError = ref('');
const paymentError = ref('');

// Modales
const showPasswordModal = ref(false);
const showPaymentModal = ref(false);
const showDeleteAccountModal = ref(false);
const deleteConfirmText = ref('');

// Datos de usuario
const userData = ref({
  name: '',
  email: '',
  phone: ''
});

// Configuración de notificaciones
const notificationSettings = ref({
  email: true,
  push: false,
  marketing: false
});

// Configuración de privacidad
const privacySettings = ref({
  shareUsageData: true,
  thirdPartyCookies: true
});

// Métodos de pago
const paymentMethods = ref([]);
const editingPaymentIndex = ref(null);
const newPaymentMethod = ref({
  type: 'credit-card',
  cardNumber: '',
  expiryDate: '',
  cvv: '',
  accountHolder: '',
  iban: '',
  bankName: '',
  mobileProvider: 'bizum',
  mobileNumber: '',
  isDefault: false
});

// Datos para cambio de contraseña
const passwordData = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
});

// Cargar datos del usuario al montar el componente
onMounted(async () => {
  try {
    await fetchUserData();
    await fetchNotificationSettings();
    await fetchPrivacySettings();
    await fetchPaymentMethods();
  } catch (error) {
    console.error('Error al cargar los datos:', error);
  }
});

// Métodos para obtener datos
const fetchUserData = async () => {
  try {
    // En una aplicación real, esto sería una llamada a la API
    // const response = await axios.get('/api/user/profile');
    // userData.value = response.data;
    
    // Por ahora, usamos datos de ejemplo o del store
    userData.value = {
      name: userStore.user?.name || 'Usuario',
      email: userStore.user?.email || 'usuario@example.com',
      phone: '+34 600 000 000'
    };
  } catch (error) {
    console.error('Error al obtener datos del usuario:', error);
  }
};

const fetchNotificationSettings = async () => {
  try {
    // En una aplicación real, esto sería una llamada a la API
    // const response = await axios.get('/api/user/notification-settings');
    // notificationSettings.value = response.data;
    
    // Por ahora, usamos datos de ejemplo
    notificationSettings.value = {
      email: true,
      push: false,
      marketing: false
    };
  } catch (error) {
    console.error('Error al obtener configuración de notificaciones:', error);
  }
};

const fetchPrivacySettings = async () => {
  try {
    // En una aplicación real, esto sería una llamada a la API
    // const response = await axios.get('/api/user/privacy-settings');
    // privacySettings.value = response.data;
    
    // Por ahora, usamos datos de ejemplo
    privacySettings.value = {
      shareUsageData: true,
      thirdPartyCookies: true
    };
  } catch (error) {
    console.error('Error al obtener configuración de privacidad:', error);
  }
};

const fetchPaymentMethods = async () => {
  try {
    // En una aplicación real, esto sería una llamada a la API
    // const response = await axios.get('/api/user/payment-methods');
    // paymentMethods.value = response.data;
    
    // Por ahora, usamos datos de ejemplo
    paymentMethods.value = [
      {
        type: 'credit-card',
        cardNumber: '•••• •••• •••• 4242',
        expiryDate: '12/25',
        isDefault: true
      },
      {
        type: 'bank',
        accountHolder: 'Carlos Rodríguez',
        iban: 'ES91 •••• •••• •••• •••• 1332',
        bankName: 'CaixaBank'
      }
    ];
  } catch (error) {
    console.error('Error al obtener métodos de pago:', error);
  }
};

// Métodos para guardar datos
const saveUserData = async () => {
  isSaving.value = true;
  saveMessage.value = null;

  try {
    // PATCH a la API real, asegurándote de enviar el token de autenticación
    await axios.patch(
      '/api/user/profile',
      {
        name: userData.value.name,
        email: userData.value.email,
        phone: userData.value.phone,
      },
      {
        headers: {
          Authorization: `Bearer ${userStore.token}`, // O usa el método que uses para auth
        },
      }
    );

    // Opcional: Actualiza el store
    if (userStore.user) {
      userStore.setUser({
        ...userStore.user,
        name: userData.value.name,
        email: userData.value.email,
        phone: userData.value.phone,
      });
    }

    saveMessage.value = {
      type: 'success',
      text: 'Datos guardados correctamente',
    };

    setTimeout(() => {
      saveMessage.value = null;
    }, 3000);
  } catch (error) {
    console.error('Error al guardar datos del usuario:', error);
    saveMessage.value = {
      type: 'error',
      text: 'Error al guardar los datos. Inténtalo de nuevo.',
    };
  } finally {
    isSaving.value = false;
  }
};

const saveNotificationSettings = async () => {
  isSaving.value = true;
  saveMessage.value = null;
  
  try {
    // En una aplicación real, esto sería una llamada a la API
    // await axios.put('/api/user/notification-settings', notificationSettings.value);
    
    // Simulamos una llamada a la API
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    saveMessage.value = {
      type: 'success',
      text: 'Preferencias de notificación guardadas correctamente'
    };
    
    // Ocultar el mensaje después de 3 segundos
    setTimeout(() => {
      saveMessage.value = null;
    }, 3000);
  } catch (error) {
    console.error('Error al guardar configuración de notificaciones:', error);
    saveMessage.value = {
      type: 'error',
      text: 'Error al guardar las preferencias. Inténtalo de nuevo.'
    };
  } finally {
    isSaving.value = false;
  }
};

const savePrivacySettings = async () => {
  isSaving.value = true;
  saveMessage.value = null;
  
  try {
    // En una aplicación real, esto sería una llamada a la API
    // await axios.put('/api/user/privacy-settings', privacySettings.value);
    
    // Simulamos una llamada a la API
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    saveMessage.value = {
      type: 'success',
      text: 'Configuración de privacidad guardada correctamente'
    };
    
    // Ocultar el mensaje después de 3 segundos
    setTimeout(() => {
      saveMessage.value = null;
    }, 3000);
  } catch (error) {
    console.error('Error al guardar configuración de privacidad:', error);
    saveMessage.value = {
      type: 'error',
      text: 'Error al guardar la configuración. Inténtalo de nuevo.'
    };
  } finally {
    isSaving.value = false;
  }
};

// Métodos para gestionar la contraseña
const changePassword = async () => {
  passwordError.value = '';
  
  // Validar que las contraseñas coincidan
  if (passwordData.value.newPassword !== passwordData.value.confirmPassword) {
    passwordError.value = 'Las contraseñas no coinciden';
    return;
  }
  
  // Validar longitud mínima
  if (passwordData.value.newPassword.length < 8) {
    passwordError.value = 'La contraseña debe tener al menos 8 caracteres';
    return;
  }
  
  isChangingPassword.value = true;
  
  try {
    // En una aplicación real, esto sería una llamada a la API
    // await axios.put('/api/user/change-password', {
    //   currentPassword: passwordData.value.currentPassword,
    //   newPassword: passwordData.value.newPassword
    // });
    
    // Simulamos una llamada a la API
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Limpiar el formulario
    passwordData.value = {
      currentPassword: '',
      newPassword: '',
      confirmPassword: ''
    };
    
    // Cerrar el modal
    showPasswordModal.value = false;
    
    // Mostrar mensaje de éxito
    saveMessage.value = {
      type: 'success',
      text: 'Contraseña cambiada correctamente'
    };
    
    // Ocultar el mensaje después de 3 segundos
    setTimeout(() => {
      saveMessage.value = null;
    }, 3000);
  } catch (error) {
    console.error('Error al cambiar la contraseña:', error);
    passwordError.value = 'Error al cambiar la contraseña. Verifica que la contraseña actual sea correcta.';
  } finally {
    isChangingPassword.value = false;
  }
};

// Métodos para gestionar métodos de pago
const getMethodTitle = (method) => {
  if (method.type === 'credit-card') {
    return `Tarjeta terminada en ${method.cardNumber.slice(-4)}`;
  } else if (method.type === 'bank') {
    return `Cuenta bancaria (${method.bankName})`;
  } else {
    return `${method.mobileProvider}`;
  }
};

const getMethodDescription = (method) => {
  if (method.type === 'credit-card') {
    return `Expira: ${method.expiryDate}${method.isDefault ? ' · Predeterminada' : ''}`;
  } else if (method.type === 'bank') {
    return `IBAN: ${method.iban}${method.isDefault ? ' · Predeterminada' : ''}`;
  } else {
    return `${method.mobileNumber}${method.isDefault ? ' · Predeterminada' : ''}`;
  }
};

const editPaymentMethod = (index) => {
  const method = paymentMethods.value[index];
  newPaymentMethod.value = { ...method };
  editingPaymentIndex.value = index;
  showPaymentModal.value = true;
};

const deletePaymentMethod = async (index) => {
  if (confirm('¿Estás seguro de que quieres eliminar este método de pago?')) {
    try {
      // En una aplicación real, esto sería una llamada a la API
      // await axios.delete(`/api/user/payment-methods/${paymentMethods.value[index].id}`);
      
      // Simulamos una llamada a la API
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // Eliminar el método de pago del array
      paymentMethods.value.splice(index, 1);
      
      // Mostrar mensaje de éxito
      saveMessage.value = {
        type: 'success',
        text: 'Método de pago eliminado correctamente'
      };
      
      // Ocultar el mensaje después de 3 segundos
      setTimeout(() => {
        saveMessage.value = null;
      }, 3000);
    } catch (error) {
      console.error('Error al eliminar método de pago:', error);
      saveMessage.value = {
        type: 'error',
        text: 'Error al eliminar el método de pago. Inténtalo de nuevo.'
      };
    }
  }
};

const savePaymentMethod = async () => {
  paymentError.value = '';
  
  // Validar los datos según el tipo de método de pago
  if (newPaymentMethod.value.type === 'credit-card') {
    if (!newPaymentMethod.value.cardNumber || !newPaymentMethod.value.expiryDate) {
      paymentError.value = 'Por favor, completa todos los campos obligatorios';
      return;
    }
  } else if (newPaymentMethod.value.type === 'bank') {
    if (!newPaymentMethod.value.accountHolder || !newPaymentMethod.value.iban || !newPaymentMethod.value.bankName) {
      paymentError.value = 'Por favor, completa todos los campos obligatorios';
      return;
    }
  } else if (newPaymentMethod.value.type === 'mobile') {
    if (!newPaymentMethod.value.mobileNumber) {
      paymentError.value = 'Por favor, completa todos los campos obligatorios';
      return;
    }
  }
  
  isSavingPayment.value = true;
  
  try {
    // En una aplicación real, esto sería una llamada a la API
    // const response = await axios.post('/api/user/payment-methods', newPaymentMethod.value);
    
    // Simulamos una llamada a la API
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    if (editingPaymentIndex.value !== null) {
      // Actualizar método existente
      paymentMethods.value[editingPaymentIndex.value] = { ...newPaymentMethod.value };
      
      // Si este método se establece como predeterminado, quitar la marca de los demás
      if (newPaymentMethod.value.isDefault) {
        paymentMethods.value.forEach((method, index) => {
          if (index !== editingPaymentIndex.value) {
            method.isDefault = false;
          }
        });
      }
    } else {
      // Añadir nuevo método
      paymentMethods.value.push({ ...newPaymentMethod.value });
      
      // Si este método se establece como predeterminado, quitar la marca de los demás
      if (newPaymentMethod.value.isDefault) {
        paymentMethods.value.forEach((method, index) => {
          if (index !== paymentMethods.value.length - 1) {
            method.isDefault = false;
          }
        });
      }
    }
    
    // Resetear el formulario y cerrar el modal
    newPaymentMethod.value = {
      type: 'credit-card',
      cardNumber: '',
      expiryDate: '',
      cvv: '',
      accountHolder: '',
      iban: '',
      bankName: '',
      mobileProvider: 'bizum',
      mobileNumber: '',
      isDefault: false
    };
    editingPaymentIndex.value = null;
    showPaymentModal.value = false;
    
    // Mostrar mensaje de éxito
    saveMessage.value = {
      type: 'success',
      text: 'Método de pago guardado correctamente'
    };
    
    // Ocultar el mensaje después de 3 segundos
    setTimeout(() => {
      saveMessage.value = null;
    }, 3000);
  } catch (error) {
    console.error('Error al guardar método de pago:', error);
    paymentError.value = 'Error al guardar el método de pago. Inténtalo de nuevo.';
  } finally {
    isSavingPayment.value = false;
  }
};

// Métodos para eliminar cuenta
const confirmDeleteAccount = () => {
  showDeleteAccountModal.value = true;
  deleteConfirmText.value = '';
};

const deleteAccount = async () => {
  if (deleteConfirmText.value !== 'ELIMINAR') {
    return;
  }
  
  isDeletingAccount.value = true;
  
  try {
    // En una aplicación real, esto sería una llamada a la API
    // await axios.delete('/api/user/account');
    
    // Simulamos una llamada a la API
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Cerrar sesión y redirigir
    userStore.logout();
    window.location.href = '/';
  } catch (error) {
    console.error('Error al eliminar la cuenta:', error);
    showDeleteAccountModal.value = false;
    saveMessage.value = {
      type: 'error',
      text: 'Error al eliminar la cuenta. Inténtalo de nuevo.'
    };
  } finally {
    isDeletingAccount.value = false;
  }
};
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
  padding: 1.5rem;
  height: fit-content;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.sidebar-button {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  border-radius: 8px;
  text-align: left;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 0.5rem;
}

.sidebar-button:hover {
  background-color: #f0f7ff;
  color: #0071c2;
}

.sidebar-button.active {
  background-color: #e6f0ff;
  color: #0071c2;
  border-left: 3px solid #0071c2;
  font-weight: 600;
}

.sidebar-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.75rem;
}

.settings-main {
  background-color: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.settings-main .section-title {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
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

.form-input, .form-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: white;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.password-input {
  display: flex;
  gap: 1rem;
}

.change-password-button {
  background-color: #f0f7ff;
  border: 1px solid #cce0ff;
  padding: 0 1rem;
  border-radius: 8px;
  font-weight: 500;
  color: #0071c2;
  cursor: pointer;
  transition: all 0.3s ease;
}

.change-password-button:hover {
  background-color: #e6f0ff;
  border-color: #0071c2;
}

.notification-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
  margin-bottom: 1rem;
}

.option-title {
  font-size: 1.1rem;
  margin: 0 0 0.5rem;
  color: #1e293b;
}

.option-description {
  margin: 0;
  color: #64748b;
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
  background-color: #0071c2;
}

input:focus + .slider {
  box-shadow: 0 0 1px #0071c2;
}

input:checked + .slider:before {
  transform: translateX(26px);
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

.save-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.save-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.delete-account-button {
  margin-top: 2rem;
  background-color: #fff2f0;
  color: #e41c00;
  border: 1px solid #e41c00;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: block;
}

.delete-account-button:hover {
  background-color: #ffe5e2;
  transform: translateY(-2px);
}

.save-message {
  margin-top: 1rem;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 500;
}

.save-message.success {
  background-color: #e6f7ee;
  color: #00703c;
  border: 1px solid rgba(0, 112, 60, 0.2);
}

.save-message.error {
  background-color: #fff2f0;
  color: #e41c00;
  border: 1px solid rgba(228, 28, 0, 0.2);
}

/* Estilos para métodos de pago */
.payment-methods-list {
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
  color: #e41c00;
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

.empty-state {
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

.button-icon {
  width: 18px;
  height: 18px;
}

/* Estilos para modales */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 53, 128, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background-color: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e6f0ff;
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  border-radius: 12px 12px 0 0;
}

.modal-header.delete-header {
  background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%);
}

.modal-header h3 {
  font-size: 1.2rem;
  margin: 0;
  font-weight: 600;
}

.close-button {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.close-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 1.5rem;
}

.password-form, .payment-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1rem;
}

.form-row .form-group {
  flex: 1;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #cce0ff;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover {
  background-color: #f8fafc;
  color: #1e293b;
}

.delete-button {
  background-color: #e41c00;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.delete-button:hover:not(:disabled) {
  background-color: #b91c1c;
}

.delete-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.checkbox-group input {
  width: 18px;
  height: 18px;
  accent-color: #0071c2;
}

.error-message {
  color: #e41c00;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.delete-warning {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background-color: #fff2f0;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.warning-icon {
  width: 24px;
  height: 24px;
  color: #e41c00;
  flex-shrink: 0;
}

.delete-warning p {
  margin: 0;
  color: #1e293b;
  font-size: 0.95rem;
  line-height: 1.5;
}

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
  
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
  
  .form-actions {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .form-actions button {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .settings-section {
    padding: 1rem;
    margin: 1rem;
  }
  
  .notification-option {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .notification-option .switch {
    align-self: flex-start;
  }
  
  .payment-method-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .payment-method-actions {
    align-self: flex-end;
  }
}
</style>