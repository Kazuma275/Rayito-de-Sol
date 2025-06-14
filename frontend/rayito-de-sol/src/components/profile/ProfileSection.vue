<template>
  <div class="settings-section">
    <h2 class="section-title">Configuración</h2>
    <div class="settings-container">
      <!-- Sidebar de navegación -->
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

      <!-- Contenido principal -->
      <div class="settings-main">
        <!-- Sección de cuenta -->
        <div v-if="activeSection === 'account'" class="settings-content">
          <h2 class="section-title">Configuración de cuenta</h2>
          <form @submit.prevent="saveUserData" class="settings-form">
            <div class="form-group">
              <label for="name">Nombre completo</label>
              <input
                type="text"
                id="name"
                v-model="userData.name"
                class="form-input"
                :placeholder="userData.name || 'Tu nombre completo'"
                required
              />
            </div>

            <div class="form-group">
              <label for="email">Correo electrónico</label>
              <input
                type="email"
                id="email"
                v-model="userData.email"
                class="form-input"
                :placeholder="userData.email || 'tu@email.com'"
                required
              />
            </div>

            <div class="form-group">
              <label for="password">Contraseña</label>
              <div class="password-input">
                <input
                  type="password"
                  id="password"
                  value="••••••••"
                  class="form-input"
                  disabled
                />
                <button
                  type="button"
                  class="change-password-button"
                  @click="showPasswordModal = true"
                >
                  Cambiar
                </button>
              </div>
            </div>

            <div class="form-group">
              <label for="phone">Teléfono</label>
              <input
                type="tel"
                id="phone"
                v-model="userData.phone"
                class="form-input"
                :placeholder="userData.phone || '+34 600 000 000'"
              />
            </div>

            <button type="submit" class="save-button" :disabled="isSaving">
              <span v-if="isSaving">Guardando...</span>
              <span v-else>Guardar cambios</span>
            </button>
          </form>

          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
        </div>

        <!-- Sección de notificaciones -->
        <div v-if="activeSection === 'notifications'" class="settings-content">
          <h2 class="section-title">Notificaciones</h2>
          <form
            @submit.prevent="saveNotificationSettings"
            class="settings-form"
          >
            <div class="notification-option">
              <div>
                <h3 class="option-title">Notificaciones por email</h3>
                <p class="option-description">
                  Recibe emails sobre tus reservas, mensajes y actualizaciones
                </p>
              </div>
              <label class="switch">
                <input type="checkbox" v-model="notificationSettings.email" />
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-option">
              <div>
                <h3 class="option-title">Notificaciones push</h3>
                <p class="option-description">
                  Recibe notificaciones en tiempo real en tu dispositivo
                </p>
              </div>
              <label class="switch">
                <input type="checkbox" v-model="notificationSettings.push" />
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-option">
              <div>
                <h3 class="option-title">Notificaciones de marketing</h3>
                <p class="option-description">
                  Recibe ofertas especiales y promociones exclusivas
                </p>
              </div>
              <label class="switch">
                <input
                  type="checkbox"
                  v-model="notificationSettings.marketing"
                />
                <span class="slider"></span>
              </label>
            </div>

            <button type="submit" class="save-button" :disabled="isSaving">
              <span v-if="isSaving">Guardando...</span>
              <span v-else>Guardar preferencias</span>
            </button>
          </form>

          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
        </div>

        <!-- Sección de privacidad -->
        <div v-if="activeSection === 'privacy'" class="settings-content">
          <h2 class="section-title">Privacidad y seguridad</h2>
          <form @submit.prevent="savePrivacySettings" class="settings-form">
            <div class="notification-option">
              <div>
                <h3 class="option-title">Compartir datos de uso</h3>
                <p class="option-description">
                  Ayúdanos a mejorar la plataforma compartiendo datos anónimos
                  de uso
                </p>
              </div>
              <label class="switch">
                <input
                  type="checkbox"
                  v-model="privacySettings.shareUsageData"
                />
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-option">
              <div>
                <h3 class="option-title">Cookies de terceros</h3>
                <p class="option-description">
                  Permitir cookies de terceros para personalizar tu experiencia
                </p>
              </div>
              <label class="switch">
                <input
                  type="checkbox"
                  v-model="privacySettings.thirdPartyCookies"
                />
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-option">
              <div>
                <h3 class="option-title">Perfil público</h3>
                <p class="option-description">
                  Permitir que otros usuarios vean tu perfil básico
                </p>
              </div>
              <label class="switch">
                <input
                  type="checkbox"
                  v-model="privacySettings.publicProfile"
                />
                <span class="slider"></span>
              </label>
            </div>

            <button type="submit" class="save-button" :disabled="isSaving">
              <span v-if="isSaving">Guardando...</span>
              <span v-else>Guardar configuración</span>
            </button>

            <button
              type="button"
              class="delete-account-button"
              @click="confirmDeleteAccount"
            >
              Eliminar mi cuenta
            </button>
          </form>

          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
        </div>

        <!-- Sección de pagos -->
        <!-- <div v-if="activeSection === 'payments'" class="settings-content">
          <h2 class="section-title">Métodos de pago</h2>

          <div v-if="paymentMethods.length > 0" class="payment-methods-list">
            <div
              v-for="(method, index) in paymentMethods"
              :key="method.id || index"
              class="payment-method-card"
            >
              <div class="payment-method-icon" :class="method.type">
                <CreditCardIcon
                  v-if="method.type === 'credit-card'"
                  class="method-icon"
                />
                <BuildingIcon
                  v-else-if="method.type === 'bank'"
                  class="method-icon"
                />
                <SmartphoneIcon v-else class="method-icon" />
              </div>
              <div class="payment-method-details">
                <h4>{{ getMethodTitle(method) }}</h4>
                <p>{{ getMethodDescription(method) }}</p>
              </div>
              <div class="payment-method-actions">
                <button
                  type="button"
                  class="edit-button"
                  @click="editPaymentMethod(index)"
                  title="Editar método"
                >
                  <EditIcon class="action-icon" />
                </button>
                <button
                  type="button"
                  class="delete-button"
                  @click="deletePaymentMethod(index)"
                  title="Eliminar método"
                >
                  <TrashIcon class="action-icon" />
                </button>
              </div>
            </div>
          </div>

          <div v-else class="empty-state">
            <CreditCardIcon class="empty-icon" />
            <p>No tienes métodos de pago guardados</p>
            <small
              >Añade un método de pago para realizar transacciones más
              rápido</small
            >
          </div>

          <button
            type="button"
            class="add-payment-button"
            @click="showPaymentModal = true"
          >
            <PlusIcon class="button-icon" />
            Añadir método de pago
          </button>

          <div v-if="saveMessage" :class="['save-message', saveMessage.type]">
            {{ saveMessage.text }}
          </div>
        </div> -->
      </div>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div
      v-if="showPasswordModal"
      class="modal-overlay"
      @click="closePasswordModal"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Cambiar contraseña</h3>
          <button
            type="button"
            class="close-button"
            @click="closePasswordModal"
          >
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="changePassword" class="password-form">
            <div class="form-group">
              <label for="current-password">Contraseña actual</label>
              <input
                type="password"
                id="current-password"
                v-model="passwordData.currentPassword"
                class="form-input"
                required
                placeholder="Ingresa tu contraseña actual"
              />
            </div>
            <div class="form-group">
              <label for="new-password">Nueva contraseña</label>
              <input
                type="password"
                id="new-password"
                v-model="passwordData.newPassword"
                class="form-input"
                required
                placeholder="Mínimo 8 caracteres"
              />
            </div>
            <div class="form-group">
              <label for="confirm-password">Confirmar nueva contraseña</label>
              <input
                type="password"
                id="confirm-password"
                v-model="passwordData.confirmPassword"
                class="form-input"
                required
                placeholder="Repite la nueva contraseña"
              />
            </div>

            <div v-if="passwordError" class="error-message">
              {{ passwordError }}
            </div>

            <div class="form-actions">
              <button
                type="button"
                class="cancel-button"
                @click="closePasswordModal"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="save-button"
                :disabled="isChangingPassword"
              >
                <span v-if="isChangingPassword">Cambiando...</span>
                <span v-else>Cambiar contraseña</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal para añadir/editar método de pago -->
    <div
      v-if="showPaymentModal"
      class="modal-overlay"
      @click="closePaymentModal"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>
            {{
              editingPaymentIndex !== null
                ? "Editar método de pago"
                : "Añadir método de pago"
            }}
          </h3>
          <button type="button" class="close-button" @click="closePaymentModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="savePaymentMethod" class="payment-form">
            <div class="form-group">
              <label for="payment-type">Tipo de método de pago</label>
              <select
                id="payment-type"
                v-model="newPaymentMethod.type"
                class="form-select"
                required
              >
                <option value="credit-card">Tarjeta de crédito/débito</option>
                <option value="bank">Cuenta bancaria</option>
                <option value="mobile">Pago móvil</option>
              </select>
            </div>

            <!-- Campos para tarjeta de crédito -->
            <div v-if="newPaymentMethod.type === 'credit-card'">
              <div class="form-group">
                <label for="card-number">Número de tarjeta</label>
                <input
                  type="text"
                  id="card-number"
                  v-model="newPaymentMethod.cardNumber"
                  class="form-input"
                  placeholder="1234 5678 9012 3456"
                  required
                />
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label for="expiry-date">Fecha de expiración</label>
                  <input
                    type="text"
                    id="expiry-date"
                    v-model="newPaymentMethod.expiryDate"
                    class="form-input"
                    placeholder="MM/AA"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="cvv">CVV</label>
                  <input
                    type="text"
                    id="cvv"
                    v-model="newPaymentMethod.cvv"
                    class="form-input"
                    placeholder="123"
                    required
                  />
                </div>
              </div>
              <div class="form-group">
                <label for="card-holder">Nombre del titular</label>
                <input
                  type="text"
                  id="card-holder"
                  v-model="newPaymentMethod.cardHolder"
                  class="form-input"
                  placeholder="Como aparece en la tarjeta"
                  required
                />
              </div>
            </div>

            <!-- Campos para cuenta bancaria -->
            <div v-if="newPaymentMethod.type === 'bank'">
              <div class="form-group">
                <label for="account-holder">Titular de la cuenta</label>
                <input
                  type="text"
                  id="account-holder"
                  v-model="newPaymentMethod.accountHolder"
                  class="form-input"
                  required
                />
              </div>
              <div class="form-group">
                <label for="iban">IBAN</label>
                <input
                  type="text"
                  id="iban"
                  v-model="newPaymentMethod.iban"
                  class="form-input"
                  placeholder="ES91 2100 0418 4502 0005 1332"
                  required
                />
              </div>
              <div class="form-group">
                <label for="bank-name">Nombre del banco</label>
                <input
                  type="text"
                  id="bank-name"
                  v-model="newPaymentMethod.bankName"
                  class="form-input"
                  required
                />
              </div>
            </div>

            <!-- Campos para pago móvil -->
            <div v-if="newPaymentMethod.type === 'mobile'">
              <div class="form-group">
                <label for="mobile-provider">Proveedor</label>
                <select
                  id="mobile-provider"
                  v-model="newPaymentMethod.mobileProvider"
                  class="form-select"
                  required
                >
                  <option value="bizum">Bizum</option>
                  <option value="paypal">PayPal</option>
                  <option value="apple-pay">Apple Pay</option>
                  <option value="google-pay">Google Pay</option>
                </select>
              </div>
              <div class="form-group">
                <label for="mobile-number">{{ getMobileFieldLabel() }}</label>
                <input
                  type="text"
                  id="mobile-number"
                  v-model="newPaymentMethod.mobileNumber"
                  class="form-input"
                  :placeholder="getMobileFieldPlaceholder()"
                  required
                />
              </div>
            </div>

            <div class="form-group checkbox-group">
              <input
                type="checkbox"
                id="default-payment"
                v-model="newPaymentMethod.isDefault"
              />
              <label for="default-payment"
                >Establecer como método predeterminado</label
              >
            </div>

            <div v-if="paymentError" class="error-message">
              {{ paymentError }}
            </div>

            <div class="form-actions">
              <button
                type="button"
                class="cancel-button"
                @click="closePaymentModal"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="save-button"
                :disabled="isSavingPayment"
              >
                <span v-if="isSavingPayment">Guardando...</span>
                <span v-else>{{
                  editingPaymentIndex !== null ? "Actualizar" : "Guardar"
                }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación para eliminar cuenta -->
    <div
      v-if="showDeleteAccountModal"
      class="modal-overlay"
      @click="closeDeleteAccountModal"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header delete-header">
          <h3>Eliminar cuenta permanentemente</h3>
          <button
            type="button"
            class="close-button"
            @click="closeDeleteAccountModal"
          >
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <div class="delete-warning">
            <AlertTriangleIcon class="warning-icon" />
            <div>
              <p><strong>Esta acción es irreversible.</strong></p>
              <p>
                Se eliminarán permanentemente todos tus datos, reservas,
                configuraciones y no podrás recuperar tu cuenta.
              </p>
            </div>
          </div>

          <form @submit.prevent="deleteAccount">
            <div class="form-group">
              <label for="delete-confirm"
                >Para confirmar, escribe <strong>"ELIMINAR"</strong> en el
                campo:</label
              >
              <input
                type="text"
                id="delete-confirm"
                v-model="deleteConfirmText"
                class="form-input"
                placeholder="Escribe ELIMINAR"
                required
              />
            </div>

            <div class="form-actions">
              <button
                type="button"
                class="cancel-button"
                @click="closeDeleteAccountModal"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="delete-button"
                :disabled="
                  deleteConfirmText !== 'ELIMINAR' || isDeletingAccount
                "
              >
                <span v-if="isDeletingAccount">X</span>
                <span v-else>X</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from "vue";
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
  AlertTriangleIcon,
} from "lucide-vue-next";
import { useUserStore } from "@/stores/userStore";
import { getItem } from "@/helpers/storage";
import { useRouter } from 'vue-router'

const userStore = useUserStore();
const router = useRouter()

// Props (si necesitas recibir datos del componente padre)
const props = defineProps({
  initialUserData: {
    type: Object,
    default: () => ({}),
  },
});

// Emits para comunicar con el componente padre
const emit = defineEmits([
  'change-tab',
  'change-avatar',
  'save-profile',
  'save-notifications',
  'add-payment-method',
  'edit-payment-method',
  'delete-payment-method',
  'save-bank'
]);

function onTabChange(tab) {
  emit('change-tab', tab);
}

function onSaveProfile(profileData) {
  emit('save-profile', profileData);
}

// Secciones de configuración
const sections = ref([
  { id: "account", name: "Cuenta", icon: UserIcon },
  { id: "notifications", name: "Notificaciones", icon: BellIcon },
  { id: "privacy", name: "Privacidad", icon: ShieldIcon },
  /* { id: "payments", name: "Pagos", icon: CreditCardIcon }, */
]);

// Estado reactivo
const activeSection = ref("account");
const isSaving = ref(false);
const isChangingPassword = ref(false);
const isSavingPayment = ref(false);
const isDeletingAccount = ref(false);
const saveMessage = ref(null);
const passwordError = ref("");
const paymentError = ref("");

// Modales
const showPasswordModal = ref(false);
const showPaymentModal = ref(false);
const showDeleteAccountModal = ref(false);
const deleteConfirmText = ref("");

// Datos de usuario
const userData = reactive({
  name: "",
  email: "",
  phone: "",
  ...props.initialUserData,
});

// Configuración de notificaciones
const notificationSettings = reactive({
  email: true,
  push: false,
  marketing: false,
});

// Configuración de privacidad
const privacySettings = reactive({
  shareUsageData: true,
  thirdPartyCookies: true,
  publicProfile: false,
});

// Métodos de pago
const paymentMethods = ref([
  {
    id: 1,
    type: "credit-card",
    cardNumber: "•••• •••• •••• 4242",
    cardHolder: "Juan Pérez",
    expiryDate: "12/25",
    isDefault: true,
  },
  {
    id: 2,
    type: "bank",
    accountHolder: "Juan Pérez",
    iban: "ES91 •••• •••• •••• •••• 1332",
    bankName: "CaixaBank",
    isDefault: false,
  },
]);

const editingPaymentIndex = ref(null);
const newPaymentMethod = reactive({
  type: "credit-card",
  cardNumber: "",
  cardHolder: "",
  expiryDate: "",
  cvv: "",
  accountHolder: "",
  iban: "",
  bankName: "",
  mobileProvider: "bizum",
  mobileNumber: "",
  isDefault: false,
});

// Datos para cambio de contraseña
const passwordData = reactive({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

// Métodos utilitarios
const clearMessage = () => {
  setTimeout(() => {
    saveMessage.value = null;
  }, 3000);
};

const showMessage = (type, text) => {
  saveMessage.value = { type, text };
  clearMessage();
};

const resetPasswordForm = () => {
  passwordData.currentPassword = "";
  passwordData.newPassword = "";
  passwordData.confirmPassword = "";
  passwordError.value = "";
};

const resetPaymentForm = () => {
  Object.assign(newPaymentMethod, {
    type: "credit-card",
    cardNumber: "",
    cardHolder: "",
    expiryDate: "",
    cvv: "",
    accountHolder: "",
    iban: "",
    bankName: "",
    mobileProvider: "bizum",
    mobileNumber: "",
    isDefault: false,
  });
  editingPaymentIndex.value = null;
  paymentError.value = "";
};

// Métodos para modales
const closePasswordModal = () => {
  showPasswordModal.value = false;
  resetPasswordForm();
};

const closePaymentModal = () => {
  showPaymentModal.value = false;
  resetPaymentForm();
};

const closeDeleteAccountModal = () => {
  showDeleteAccountModal.value = false;
  deleteConfirmText.value = "";
};

// Métodos principales
// Guardar datos del usuario
const saveUserData = async () => {
  isSaving.value = true;
  saveMessage.value = null;
  try {
    const token = getItem("auth_token", true) || getItem("auth_token");
    if (!token) throw new Error("No hay token guardado");
    await axios.patch(
      "http://localhost:8000/api/user/profile",
      {
        name: userData.name,
        email: userData.email,
        phone: userData.phone,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    userStore.setUser({
      ...userStore.user,
      name: userData.name,
      email: userData.email,
      phone: userData.phone,
    });
    showMessage("success", "Datos guardados correctamente");
  } catch (error) {
    console.error("Error al guardar usuario:", error);
    let msg = "Error al guardar los datos. Inténtalo de nuevo.";
    if (error.response?.data?.message) {
      msg = error.response.data.message;
    }
    showMessage("error", msg);
  }
};

const changePassword = async () => {
  passwordError.value = "";

  if (passwordData.newPassword !== passwordData.confirmPassword) {
    passwordError.value = "Las contraseñas no coinciden";
    return;
  }
  if (passwordData.newPassword.length < 8) {
    passwordError.value = "La contraseña debe tener al menos 8 caracteres";
    return;
  }
  isChangingPassword.value = true;
  try {
    const token = getItem("auth_token", true) || getItem("auth_token");
    if (!token) throw new Error("No hay token guardado");
    // Asegúrate de que esta URL y payload coincidan con tu backend
    await axios.post(
      "http://localhost:8000/api/user/change-password",
      {
        current_password: passwordData.currentPassword,
        new_password: passwordData.newPassword,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    showMessage("success", "Contraseña cambiada correctamente");
    closePasswordModal();
  } catch (error) {
    passwordError.value =
      error.response?.data?.message ||
      "Error al cambiar la contraseña. Verifica la contraseña actual.";
  } finally {
    isChangingPassword.value = false;
  }
};

const saveNotificationSettings = async () => {
  isSaving.value = true;
  saveMessage.value = null;

  try {
    // Guarda en localStorage
    localStorage.setItem(
      "notification_settings",
      JSON.stringify(notificationSettings)
    );

    // Simulación de delay y emisión
    await new Promise((resolve) => setTimeout(resolve, 1000));
    emit("notifications-updated", { ...notificationSettings });

    showMessage(
      "success",
      "Preferencias de notificación guardadas correctamente"
    );
  } catch (error) {
    showMessage(
      "error",
      "Error al guardar las preferencias. Inténtalo de nuevo."
    );
  } finally {
    isSaving.value = false;
  }
};

const savePrivacySettings = async () => {
  isSaving.value = true;
  saveMessage.value = null;

  try {
    // Guarda en localStorage
    localStorage.setItem("privacy_settings", JSON.stringify(privacySettings));

    // Simulación de delay y emisión
    await new Promise((resolve) => setTimeout(resolve, 1000));
    emit("privacy-updated", { ...privacySettings });

    showMessage(
      "success",
      "Configuración de privacidad guardada correctamente"
    );
  } catch (error) {
    showMessage(
      "error",
      "Error al guardar la configuración. Inténtalo de nuevo."
    );
  } finally {
    isSaving.value = false;
  }
};

// Métodos para métodos de pago
const getMethodTitle = (method) => {
  if (method.type === "credit-card") {
    return `Tarjeta terminada en ${method.cardNumber?.slice(-4) || "****"}`;
  } else if (method.type === "bank") {
    return `Cuenta bancaria (${method.bankName || "Banco"})`;
  } else {
    return `${method.mobileProvider || "Pago móvil"}`;
  }
};

const getMethodDescription = (method) => {
  if (method.type === "credit-card") {
    return `Expira: ${method.expiryDate || "N/A"}${
      method.isDefault ? " • Predeterminada" : ""
    }`;
  } else if (method.type === "bank") {
    return `IBAN: ${method.iban || "N/A"}${
      method.isDefault ? " • Predeterminada" : ""
    }`;
  } else {
    return `${method.mobileNumber || "N/A"}${
      method.isDefault ? " • Predeterminada" : ""
    }`;
  }
};

const getMobileFieldLabel = () => {
  switch (newPaymentMethod.mobileProvider) {
    case "bizum":
      return "Número de teléfono";
    case "paypal":
      return "Email de PayPal";
    case "apple-pay":
      return "Apple ID";
    case "google-pay":
      return "Email de Google";
    default:
      return "Número de teléfono / Email";
  }
};

const getMobileFieldPlaceholder = () => {
  switch (newPaymentMethod.mobileProvider) {
    case "bizum":
      return "+34 600 000 000";
    case "paypal":
      return "tu@email.com";
    case "apple-pay":
      return "tu@icloud.com";
    case "google-pay":
      return "tu@gmail.com";
    default:
      return "Ingresa tu información";
  }
};

const editPaymentMethod = (index) => {
  const method = paymentMethods.value[index];
  if (method) {
    Object.assign(newPaymentMethod, { ...method });
    editingPaymentIndex.value = index;
    showPaymentModal.value = true;
  }
};

const deletePaymentMethod = async (index) => {
  const method = paymentMethods.value[index];
  const methodType =
    method.type === "credit-card"
      ? "tarjeta de crédito"
      : method.type === "bank"
      ? "cuenta bancaria"
      : "método de pago móvil";

  if (confirm(`¿Estás seguro de que quieres eliminar esta ${methodType}?`)) {
    try {
      // Simular llamada a API para eliminar
      await new Promise((resolve) => setTimeout(resolve, 500));

      const deletedMethod = paymentMethods.value[index];
      paymentMethods.value.splice(index, 1);

      // Si era el método predeterminado y quedan métodos, hacer predeterminado el primero
      if (deletedMethod.isDefault && paymentMethods.value.length > 0) {
        paymentMethods.value[0].isDefault = true;
      }

      emit("payment-deleted", deletedMethod);

      showMessage(
        "success",
        `${
          methodType.charAt(0).toUpperCase() + methodType.slice(1)
        } eliminada correctamente`
      );
    } catch (error) {
      showMessage(
        "error",
        "Error al eliminar el método de pago. Inténtalo de nuevo."
      );
    }
  }
};

const savePaymentMethod = async () => {
  paymentError.value = "";

  // Validaciones básicas
  if (newPaymentMethod.type === "credit-card") {
    if (
      !newPaymentMethod.cardNumber ||
      !newPaymentMethod.expiryDate ||
      !newPaymentMethod.cardHolder
    ) {
      paymentError.value = "Por favor, completa todos los campos obligatorios";
      return;
    }
  } else if (newPaymentMethod.type === "bank") {
    if (
      !newPaymentMethod.accountHolder ||
      !newPaymentMethod.iban ||
      !newPaymentMethod.bankName
    ) {
      paymentError.value = "Por favor, completa todos los campos obligatorios";
      return;
    }
  } else if (newPaymentMethod.type === "mobile") {
    if (!newPaymentMethod.mobileNumber) {
      paymentError.value = "Por favor, completa todos los campos obligatorios";
      return;
    }
  }

  isSavingPayment.value = true;

  try {
    await new Promise((resolve) => setTimeout(resolve, 1000));

    const methodToSave = { ...newPaymentMethod };

    if (editingPaymentIndex.value !== null) {
      // Editar método existente
      paymentMethods.value[editingPaymentIndex.value] = methodToSave;
      emit("payment-updated", methodToSave);
    } else {
      // Añadir nuevo método
      methodToSave.id = Date.now();
      paymentMethods.value.push(methodToSave);
      emit("payment-added", methodToSave);
    }

    // Si se marca como predeterminado, desmarcar los demás
    if (newPaymentMethod.isDefault) {
      paymentMethods.value.forEach((method, index) => {
        if (editingPaymentIndex.value === null) {
          if (index !== paymentMethods.value.length - 1) {
            method.isDefault = false;
          }
        } else {
          if (index !== editingPaymentIndex.value) {
            method.isDefault = false;
          }
        }
      });
    }

    showMessage("success", "Método de pago guardado correctamente");
    closePaymentModal();
  } catch (error) {
    paymentError.value =
      "Error al guardar el método de pago. Inténtalo de nuevo.";
  } finally {
    isSavingPayment.value = false;
  }
};

const confirmDeleteAccount = () => {
  showDeleteAccountModal.value = true;
  deleteConfirmText.value = "";
};

const deleteAccount = async () => {
  if (deleteConfirmText.value !== 'ELIMINAR') return;
  isDeletingAccount.value = true;
  try {
    const token = getItem('auth_token', true) || getItem('auth_token');
    if (!token) throw new Error('No hay token guardado');

    await axios.delete('http://localhost:8000/api/user/profile', {
      headers: { Authorization: `Bearer ${token}` }
    });

    userStore.logout();
    showMessage('success', 'Cuenta eliminada correctamente');
    // Redirige al portal
    router.push({ name: 'PortalLanding' }); 
  } catch (error) {
    showMessage('error', error.response?.data?.message || 'Error al eliminar la cuenta. Inténtalo de nuevo.');
  } finally {
    isDeletingAccount.value = false;
  }
};

onMounted(async () => {
  // 1. Recuperar preferencias de notificaciones guardadas (localStorage)
  const savedSettings = localStorage.getItem("notification_settings");
  if (savedSettings) {
    Object.assign(notificationSettings, JSON.parse(savedSettings));
  }

  // Recuperar preferencias de privacidad guardadas (localStorage)
  const savedPrivacy = localStorage.getItem("privacy_settings");
  if (savedPrivacy) {
    Object.assign(privacySettings, JSON.parse(savedPrivacy));
  }

  // 2. Cargar datos iniciales del store si están disponibles
  if (userStore.user) {
    userData.name = userStore.user.name || "";
    userData.email = userStore.user.email || "";
    userData.phone = userStore.user.phone || "";
  }

  try {
    // Usa el helper para buscar primero en sessionStorage y luego en localStorage
    const token = getItem("auth_token", true) || getItem("auth_token");
    if (!token) throw new Error("No hay token guardado");
    if (token) {
      const response = await axios.get(
        "http://localhost:8000/api/user/profile",
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );

      if (response.data.user) {
        userStore.setUser(response.data.user);
        // Actualizar los datos locales con la respuesta de la API
        userData.name = response.data.user.name || "";
        userData.email = response.data.user.email || "";
        userData.phone = response.data.user.phone || "";
      }
    }
  } catch (error) {
    console.error("Error al obtener el perfil de usuario", error);
    // Si hay error, mantener los datos del store si existen
    if (userStore.user) {
      userData.name = userStore.user.name || "";
      userData.email = userStore.user.email || "";
      userData.phone = userStore.user.phone || "";
    }
  }

  // 3. Datos de ejemplo para métodos de pago
  paymentMethods.value = [
    {
      id: 1,
      type: "credit-card",
      cardNumber: "•••• •••• •••• 4242",
      expiryDate: "12/25",
      isDefault: true,
    },
    {
      id: 2,
      type: "bank",
      accountHolder: "Carlos Rodríguez",
      iban: "ES91 •••• •••• •••• •••• 1332",
      bankName: "CaixaBank",
      isDefault: false,
    },
  ];
});

// Observar cambios en el store del usuario
let hasInitializedUserData = false;
watch(
  () => userStore.user,
  (newUser) => {
    if (newUser && !hasInitializedUserData) {
      userData.name = newUser.name || "";
      userData.email = newUser.email || "";
      userData.phone = newUser.phone || "";
      hasInitializedUserData = true;
    }
  },
  { immediate: true }
);
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
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(
      circle at 20% 150%,
      rgba(0, 113, 194, 0.05) 0%,
      transparent 50%
    ),
    radial-gradient(
      circle at 80% -50%,
      rgba(0, 53, 128, 0.03) 0%,
      transparent 60%
    );
  z-index: 0;
  border-radius: 16px;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 2rem;
  position: relative;
  z-index: 1;
  font-weight: 700;
}

.section-title::after {
  content: "";
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

.settings-content .section-title {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
}

.settings-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.form-input,
.form-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: white;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.form-input:disabled {
  background-color: #f8fafc;
  color: #64748b;
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
  white-space: nowrap;
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
  font-weight: 600;
}

.option-description {
  margin: 0;
  color: #64748b;
  font-size: 0.9rem;
  line-height: 1.4;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
  flex-shrink: 0;
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
  transition: 0.4s;
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
  transition: 0.4s;
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
  align-self: flex-start;
}

.save-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.save-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
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
  align-self: flex-start;
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
  font-weight: 600;
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

.edit-button,
.delete-button {
  background: none;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 6px;
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
  padding: 3rem 2rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
  margin-bottom: 1.5rem;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #cce0ff;
  margin: 0 auto 1rem;
}

.empty-state p {
  color: #64748b;
  font-size: 1.1rem;
  font-weight: 500;
  margin: 0 0 0.5rem;
}

.empty-state small {
  color: #94a3b8;
  font-size: 0.9rem;
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

.password-form,
.payment-form {
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

.checkbox-group label {
  font-size: 0.95rem;
  cursor: pointer;
}

.error-message {
  color: #e41c00;
  font-size: 0.9rem;
  background-color: #fff2f0;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  border: 1px solid rgba(228, 28, 0, 0.2);
}

.delete-warning {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background-color: #fff2f0;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 1px solid rgba(228, 28, 0, 0.2);
}

.warning-icon {
  width: 24px;
  height: 24px;
  color: #e41c00;
  flex-shrink: 0;
  margin-top: 2px;
}

.delete-warning p {
  margin: 0 0 0.5rem;
  color: #1e293b;
  font-size: 0.95rem;
  line-height: 1.5;
}

.delete-warning p:last-child {
  margin-bottom: 0;
}

/* Responsive Design */
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

  .password-input {
    flex-direction: column;
    gap: 0.75rem;
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

  .modal-content {
    width: 95%;
    margin: 1rem;
  }
}
</style>