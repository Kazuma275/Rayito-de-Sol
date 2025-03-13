<script setup>
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';

const stripe = ref(null);
const elements = ref(null);
const card = ref(null);
const errorMessage = ref('');
const loading = ref(false);

// Using a test public key from Stripe
const stripePublicKey = 'pk_live_51R2DAqGR90uW5lzlX2UNwjYFHQ7gC1nCzdNVTHo6hxlEK8VguEIzO5gcexDRLpCtbIDpaNYrf8OOsAvbnC5SUDYL00QEsl1f1v';

onMounted(async () => {
  try {
    stripe.value = await loadStripe(stripePublicKey);
    if (!stripe.value) {
      throw new Error('Stripe failed to initialize');
    }
    
    elements.value = stripe.value.elements();
    card.value = elements.value.create('card');
    card.value.mount('#card-element');

    card.value.addEventListener('change', (event) => {
      errorMessage.value = event.error ? event.error.message : '';
    });
  } catch (err) {
    errorMessage.value = 'Error initializing payment system';
    console.error('Stripe initialization error:', err);
  }
});

const handleSubmit = async () => {
  loading.value = true;
  errorMessage.value = '';

  try {
    const { paymentMethod, error } = await stripe.value.createPaymentMethod({
      type: 'card',
      card: card.value,
    });

    if (error) {
      errorMessage.value = error.message;
      return;
    }

    // Here you would typically send the paymentMethod.id to your backend
    console.log('Payment Method:', paymentMethod);

    // Example of how to handle the payment with your backend:
    /*
    const response = await axios.post('/api/process-payment', {
      paymentMethodId: paymentMethod.id,
      amount: 1000, // amount in cents
      currency: 'eur'
    });
    */

  } catch (err) {
    errorMessage.value = 'Ha ocurrido un error al procesar el pago.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="payment-form">
    <h2>Pago Seguro</h2>
    
    <form @submit.prevent="handleSubmit">
      <div class="form-row">
        <div id="card-element" class="card-element"></div>
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
      </div>

      <button 
        type="submit" 
        :disabled="loading" 
        class="payment-button"
      >
        {{ loading ? 'Procesando...' : 'Pagar' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.payment-form {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
}

.card-element {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  margin-bottom: 20px;
  background: white;
}

.error-message {
  color: #dc3545;
  margin-top: 10px;
  font-size: 14px;
}

.payment-button {
  width: 100%;
  padding: 12px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

.payment-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}
</style>