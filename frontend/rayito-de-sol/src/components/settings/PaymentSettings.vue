<template>
  <div class="settings-panel">
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
        <select id="default-currency" v-model="paymentPreferences.defaultCurrency" class="form-select">
          <option value="EUR">EUR (€)</option>
          <option value="USD">USD ($)</option>
          <option value="GBP">GBP (£)</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="payment-schedule">Programación de pagos</label>
        <select id="payment-schedule" v-model="paymentPreferences.paymentSchedule" class="form-select">
          <option value="immediate">Inmediato</option>
          <option value="weekly">Semanal</option>
          <option value="monthly">Mensual</option>
        </select>
      </div>
      
      <div class="form-group checkbox-group">
        <input type="checkbox" id="auto-payout" v-model="paymentPreferences.autoPayout" />
        <label for="auto-payout">Habilitar pagos automáticos</label>
      </div>
      
      <button class="save-preferences-button">Guardar preferencias</button>
    </div>
    
    <!-- Modal para añadir/editar método de pago -->
    <div v-if="showAddPaymentModal" class="payment-modal-overlay" @click="showAddPaymentModal = false">
      <div class="payment-modal" @click.stop>
        <div class="modal-header">
          <h3>{{ editingPaymentIndex !== null ? 'Editar método de pago' : 'Añadir método de pago' }}</h3>
          <button class="close-modal-button" @click="showAddPaymentModal = false">
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
            
            <div class="form-actions">
              <button type="button" class="cancel-button" @click="showAddPaymentModal = false">Cancelar</button>
              <button type="submit" class="save-button">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { 
  CreditCardIcon, 
  BuildingIcon, 
  SmartphoneIcon, 
  EditIcon, 
  TrashIcon, 
  PlusIcon,
  XIcon
} from 'lucide-vue-next';

// Métodos de pago guardados
const paymentMethods = ref([
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
]);

// Preferencias de pago
const paymentPreferences = ref({
  defaultCurrency: 'EUR',
  paymentSchedule: 'immediate',
  autoPayout: true
});

// Estado del modal
const showAddPaymentModal = ref(false);
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

// Métodos
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
  showAddPaymentModal.value = true;
};

const deletePaymentMethod = (index) => {
  if (confirm('¿Estás seguro de que quieres eliminar este método de pago?')) {
    paymentMethods.value.splice(index, 1);
  }
};

const savePaymentMethod = () => {
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
  showAddPaymentModal.value = false;
};
</script>

<style scoped>
.settings-panel {
  max-width: 800px;
}

.panel-title {
  font-size: 1.4rem;
  color: #003580;
  margin: 0 0 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e6f0ff;
}

.payment-methods {
  margin-bottom: 2rem;
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

.button-icon {
  width: 18px;
  height: 18px;
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

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #1e293b;
}

.form-select, .form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #cce0ff;
  border-radius: 8px;
  font-size: 1rem;
  background-color: white;
  transition: all 0.3s ease;
}

.form-select:focus, .form-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.form-row {
  display: flex;
  gap: 1rem;
}

.form-row .form-group {
  flex: 1;
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

.save-preferences-button {
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

.save-preferences-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

/* Modal styles */
.payment-modal-overlay {
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

.payment-modal {
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

.modal-header h3 {
  font-size: 1.2rem;
  margin: 0;
  font-weight: 600;
}

.close-modal-button {
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

.close-modal-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 1.5rem;
}

.payment-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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
  
  .form-actions {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .form-actions button {
    width: 100%;
  }
}
</style>