<template>
  <div class="settings-panel">
    <h3 class="panel-title">Pagos</h3>
    
    <div class="payment-methods">
      <h4>Métodos de pago</h4>
      
      <div class="payment-method">
        <div class="payment-info">
          <CreditCardIcon class="payment-icon" />
          <div>
            <h5>Visa terminada en 4242</h5>
            <p>Expira 12/2025</p>
          </div>
        </div>
        <div class="payment-actions">
          <button class="edit-button">Editar</button>
          <button class="delete-button">Eliminar</button>
        </div>
      </div>
      
      <button class="add-payment-button">
        <PlusIcon class="add-icon" />
        Añadir método de pago
      </button>
    </div>
    
    <div class="bank-account">
      <h4>Cuenta bancaria</h4>
      
      <div class="form-group">
        <label for="bank-name">Nombre del banco</label>
        <input id="bank-name" type="text" v-model="payment.bankName" class="form-input" />
      </div>
      
      <div class="form-group">
        <label for="account-holder">Titular de la cuenta</label>
        <input id="account-holder" type="text" v-model="payment.accountHolder" class="form-input" />
      </div>
      
      <div class="form-group">
        <label for="iban">IBAN</label>
        <input id="iban" type="text" v-model="payment.iban" class="form-input" />
      </div>
      
      <div class="form-actions">
        <button class="save-button">Guardar información bancaria</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { CreditCardIcon, PlusIcon } from 'lucide-vue-next';

const payment = ref({
  bankName: 'Banco Santander',
  accountHolder: 'Carlos Rodríguez',
  iban: 'ES91 2100 0418 4502 0005 1332'
});
</script>

<style scoped>
.settings-panel {
  max-width: 600px;
}

.panel-title {
  font-size: 1.2rem;
  color: #003580;
  margin: 0 0 1.5rem;
}

.payment-methods {
  margin-bottom: 2rem;
}

.payment-methods h4, .bank-account h4 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
}

.payment-method {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border: 1px solid #eee;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.payment-info {
  display: flex;
  align-items: center;
}

.payment-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-right: 1rem;
}

.payment-info h5 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
}

.payment-info p {
  font-size: 0.9rem;
  color: #666;
  margin: 0;
}

.payment-actions {
  display: flex;
  gap: 0.5rem;
}

.edit-button, .delete-button {
  background: none;
  border: none;
  font-size: 0.9rem;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.edit-button {
  color: #0071c2;
}

.edit-button:hover {
  background-color: #e6f0ff;
}

.delete-button {
  color: #e41c00;
}

.delete-button:hover {
  background-color: #fff2f0;
}

.add-payment-button {
  display: flex;
  align-items: center;
  background: none;
  border: 1px dashed #ccc;
  width: 100%;
  padding: 0.75rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: border-color 0.3s;
  justify-content: center;
}

.add-payment-button:hover {
  border-color: #0071c2;
}

.add-payment-button .add-icon {
  width: 16px;
  height: 16px;
  margin-right: 0.5rem;
}

.save-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
}

.save-button:hover {
  background-color: #005999;
}
</style>