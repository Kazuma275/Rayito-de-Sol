<template>
  <div v-if="visible" class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>{{ isEditMode ? 'Editar Método de Pago' : 'Añadir Nuevo Método de Pago' }}</h3>
        <button class="close-button" @click="$emit('close')">
          <XIcon />
        </button>
      </div>

      <div class="modal-body">
        <form @submit.prevent="handleSubmit" class="payment-form">
          <div class="form-group">
            <label for="payment-type">Tipo de Método de Pago</label>
            <select v-model="localPayment.method" id="payment-type" class="form-input" required>
              <option value="credit-card">Tarjeta de Crédito</option>
              <option value="paypal">PayPal</option>
              <option value="apple-pay">Apple Pay</option>
              <option value="google-pay">Google Pay</option>
              <option value="bank-transfer">Transferencia Bancaria</option>
            </select>
          </div>

          <div v-if="localPayment.method === 'credit-card'">
            <div class="form-group">
              <label for="card-number">Número de tarjeta</label>
              <input
                id="card-number"
                type="text"
                v-model="localPayment.cardNumber"
                required
                class="form-input"
                placeholder="Número de tarjeta"
                pattern="^[0-9]{16}$"
                title="El número de tarjeta debe tener 16 dígitos"
              />
              <small v-if="!isValidCardNumber && formSubmitted" class="error-message">Número de tarjeta inválido</small>
            </div>
            <div class="form-group">
              <label for="expiry-date">Fecha de expiración</label>
              <input
                id="expiry-date"
                type="month"
                v-model="localPayment.expiryDate"
                required
                class="form-input"
                min="2025-01"
              />
            </div>
            <div class="form-group">
              <label for="cvv">CVV</label>
              <input
                id="cvv"
                type="text"
                v-model="localPayment.cvv"
                required
                class="form-input"
                placeholder="CVV"
                pattern="^[0-9]{3,4}$"
                title="El CVV debe ser de 3 o 4 dígitos"
              />
            </div>
          </div>

          <div v-if="localPayment.method === 'paypal'">
            <div class="form-group">
              <label for="paypal-email">Correo electrónico de PayPal</label>
              <input
                id="paypal-email"
                type="email"
                v-model="localPayment.paypalEmail"
                required
                class="form-input"
                placeholder="Correo electrónico asociado a PayPal"
                :class="{'input-error': !isValidPaypalEmail && formSubmitted}"
              />
              <small v-if="!isValidPaypalEmail && formSubmitted" class="error-message">Correo electrónico inválido</small>
            </div>
          </div>

          <div v-if="localPayment.method === 'apple-pay'">
            <div class="form-group">
              <label for="apple-id">ID de Apple</label>
              <input
                id="apple-id"
                type="email"
                v-model="localPayment.appleId"
                required
                class="form-input"
                placeholder="Correo de Apple ID"
                :class="{'input-error': !isValidAppleId && formSubmitted}"
              />
              <small v-if="!isValidAppleId && formSubmitted" class="error-message">Correo electrónico inválido</small>
            </div>
          </div>

          <div v-if="localPayment.method === 'google-pay'">
            <div class="form-group">
              <label for="google-email">Correo electrónico de Google Pay</label>
              <input
                id="google-email"
                type="email"
                v-model="localPayment.googleEmail"
                required
                class="form-input"
                placeholder="Correo asociado a Google Pay"
                :class="{'input-error': !isValidGoogleEmail && formSubmitted}"
              />
              <small v-if="!isValidGoogleEmail && formSubmitted" class="error-message">Correo electrónico inválido</small>
            </div>
          </div>

          <div v-if="localPayment.method === 'bank-transfer'">
            <div class="form-group">
              <label for="bank-account">Número de cuenta bancaria</label>
              <input
                id="bank-account"
                type="text"
                v-model="localPayment.bankAccount"
                required
                class="form-input"
                placeholder="Número de cuenta bancaria"
              />
            </div>
            <div class="form-group">
              <label for="bank-iban">IBAN</label>
              <input
                id="bank-iban"
                type="text"
                v-model="localPayment.bankIban"
                required
                class="form-input"
                placeholder="Código IBAN"
                pattern="^[A-Z0-9]{2}[A-Z0-9]{4}[A-Z0-9]{10}$"
                title="El IBAN debe tener el formato correcto"
              />
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="cancel-button" @click="$emit('close')">Cancelar</button>
            <button type="submit" class="submit-button" :disabled="isSubmitDisabled">{{ isEditMode ? 'Guardar Cambios' : 'Añadir Método de Pago' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { XIcon } from 'lucide-vue-next'

const props = defineProps({
  visible: Boolean,
  isEditMode: Boolean
})

const emit = defineEmits(['submit', 'close'])

const localPayment = ref({
  method: 'credit-card',
  cardNumber: '',
  expiryDate: '',
  cvv: '',
  paypalEmail: '',
  appleId: '',
  googleEmail: '',
  bankAccount: '',
  bankIban: ''
})

const formSubmitted = ref(false)

// Validaciones
const isValidCardNumber = computed(() => /^[0-9]{16}$/.test(localPayment.value.cardNumber))
const isValidPaypalEmail = computed(() => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(localPayment.value.paypalEmail))
const isValidAppleId = computed(() => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(localPayment.value.appleId))
const isValidGoogleEmail = computed(() => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(localPayment.value.googleEmail))

const isSubmitDisabled = computed(() => !(isValidCardNumber && isValidPaypalEmail && isValidAppleId && isValidGoogleEmail))

const handleSubmit = () => {
  formSubmitted.value = true
  emit('submit', { ...localPayment.value })
}
</script>

<style scoped>
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
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.3s;
}

.close-button:hover {
  background-color: #f5f5f5;
}

.modal-body {
  padding: 1.5rem;
}

.payment-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group {
  flex: 1;
}

.form-group label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.form-input, .form-textarea {
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
}

.form-textarea {
  resize: vertical;
}

.error-message {
  color: #f44336;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.submit-button,
.cancel-button {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
}

.submit-button {
  background-color: #0071c2;
  color: white;
  border: none;
}

.cancel-button {
  background-color: white;
  color: #666;
  border: 1px solid #ccc;
}

.submit-button:hover {
  background-color: #005fa3;
}

.cancel-button:hover {
  background-color: #f5f5f5;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
}
</style>
