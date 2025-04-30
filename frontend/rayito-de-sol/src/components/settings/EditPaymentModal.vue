<template>
  <div class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>{{ isEditMode ? 'Editar Método de Pago' : 'Añadir Nuevo Método de Pago' }}</h3>
        <button class="close-button" @click="$emit('close')">
          <XIcon />
        </button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="submitForm" class="payment-form">
          <!-- Tipo de Método de Pago -->
          <div class="form-group">
            <label for="payment-method">Método de Pago</label>
            <select id="payment-method" v-model="paymentData.method" class="form-input" required>
              <option value="credit_card">Tarjeta de Crédito</option>
              <option value="paypal">PayPal</option>
              <option value="bank_transfer">Transferencia Bancaria</option>
            </select>
          </div>

          <!-- Número de Tarjeta (solo si el método es 'credit_card') -->
          <div class="form-group" v-if="paymentData.method === 'credit_card'">
            <label for="card-number">Número de Tarjeta</label>
            <input
              id="card-number"
              v-model="paymentData.cardNumber"
              type="text"
              class="form-input"
              placeholder="Número de tarjeta"
              :required="paymentData.method === 'credit_card'"
            />
          </div>

          <!-- Correo de PayPal (solo si el método es 'paypal') -->
          <div class="form-group" v-if="paymentData.method === 'paypal'">
            <label for="paypal-email">Correo de PayPal</label>
            <input
              id="paypal-email"
              v-model="paymentData.paypalEmail"
              type="email"
              class="form-input"
              placeholder="Correo electrónico de PayPal"
              :required="paymentData.method === 'paypal'"
            />
          </div>

          <!-- Cuenta Bancaria (solo si el método es 'bank_transfer') -->
          <div class="form-group" v-if="paymentData.method === 'bank_transfer'">
            <label for="bank-account">Cuenta Bancaria</label>
            <input
              id="bank-account"
              v-model="paymentData.bankAccount"
              type="text"
              class="form-input"
              placeholder="Número de cuenta bancaria"
              :required="paymentData.method === 'bank_transfer'"
            />
          </div>

          <div class="form-actions">
            <button type="button" class="cancel-button" @click="$emit('close')">Cancelar</button>
            <button type="submit" class="submit-button">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import { XIcon } from 'lucide-vue-next';

// Emitir eventos 'close' y 'update-payment'
const emit = defineEmits(['close', 'update-payment']);

// Recibir los datos del pago y el modo de edición como props
const props = defineProps({
  paymentData: Object,
  isEditMode: Boolean
});

// Crear una referencia reactiva para los datos del pago (local)
const paymentData = ref({ ...props.paymentData });

// Verificar si el pago está en modo de edición
watch(() => props.paymentData, (newData) => {
  paymentData.value = { ...newData };
});

const submitForm = () => {
  // Emitir los datos del formulario al componente padre
  emit('update-payment', { ...paymentData.value });
};
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

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-button {
  background-color: white;
  color: #666;
  border: 1px solid #ccc;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cancel-button:hover {
  background-color: #f5f5f5;
}

.submit-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #005999;
}

@media (max-width: 576px) {
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>
