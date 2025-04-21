<script setup>
import { ref } from 'vue';
import BookingPreview from '../../BookingPreview.vue';
import PaymentForm from '../../PaymentForm.vue';

const showPayment = ref(false);

// Example data
const apartment = {
  name: 'Apartamento Vista Mar',
  image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80',
  pricePerNight: 120,
  cleaningFee: 50
};

const booking = {
  checkIn: '2024-03-01',
  checkOut: '2024-03-05',
  nights: 4
};

const handleProceedToPayment = () => {
  showPayment.value = true;
};
</script>

<template>
  <div class="booking-test-container">
    <h1>Test de Reserva y Pago</h1>
    
    <div v-if="!showPayment">
      <BookingPreview 
        :apartment="apartment"
        :booking="booking"
        @proceed-to-payment="handleProceedToPayment"
      />
    </div>
    
    <div v-else>
      <button 
        @click="showPayment = false"
        class="back-button"
      >
        ← Volver al resumen
      </button>
      <PaymentForm />
    </div>
  </div>
</template>

<style scoped>
.booking-test-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.back-button {
  margin-bottom: 20px;
  padding: 8px 16px;
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.back-button:hover {
  background-color: #e9ecef;
}
</style>