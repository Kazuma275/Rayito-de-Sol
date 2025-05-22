<template>
  <div class="settings-panel">
    <h3 class="panel-title">Pagos</h3>
    <div class="payment-methods">
      <h4>Métodos de pago</h4>
      <div v-for="(payment, index) in paymentMethods" :key="index" class="payment-method">
        <div class="payment-info">
          <CreditCardIcon class="payment-icon" />
          <div>
            <h5>{{ payment.cardType }} terminada en {{ payment.last4 }}</h5>
            <p>Expira {{ payment.expiryDate }}</p>
          </div>
        </div>
        <div class="payment-actions">
          <button class="edit-button" @click="$emit('edit-method', index)">Editar</button>
          <button class="delete-button" @click="$emit('delete-method', index)">Eliminar</button>
        </div>
      </div>
      <button class="add-payment-button" @click="$emit('add-method')">
        <PlusIcon class="add-icon" />
        Añadir método de pago
      </button>
    </div>
    <div class="bank-account">
      <h4>Cuenta bancaria</h4>
      <form @submit.prevent="onSaveBank">
        <div class="form-group">
          <label for="bank-name">Nombre del banco</label>
          <input id="bank-name" type="text" v-model="localBankAccount.bankName" class="form-input" />
        </div>
        <div class="form-group">
          <label for="account-holder">Titular de la cuenta</label>
          <input id="account-holder" type="text" v-model="localBankAccount.accountHolder" class="form-input" />
        </div>
        <div class="form-group">
          <label for="iban">IBAN</label>
          <input id="iban" type="text" v-model="localBankAccount.iban" class="form-input" />
        </div>
        <div class="form-actions">
          <button class="save-button" type="submit">Guardar información bancaria</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { CreditCardIcon, PlusIcon } from 'lucide-vue-next'

const props = defineProps({
  paymentMethods: Array,
  bankAccount: Object
})
const emit = defineEmits(['add-method', 'edit-method', 'delete-method', 'save-bank'])
const localBankAccount = ref({ ...props.bankAccount })
watch(
  () => props.bankAccount,
  (val) => { localBankAccount.value = { ...val } }
)
function onSaveBank() {
  emit('save-bank', localBankAccount.value)
}
</script>