<script setup>
import { ref } from 'vue';

const props = defineProps({
  apartment: {
    type: Object,
    required: true
  },
  booking: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['proceed-to-payment']);

const handleProceedToPayment = () => {
  emit('proceed-to-payment');
};
</script>

<template>
  <div class="booking-preview">
    <h2>Resumen de Reserva</h2>
    
    <div class="booking-details">
      <div class="property-info">
        <h3>{{ apartment.name }}</h3>
        <img :src="apartment.image" :alt="apartment.name" class="property-image">
      </div>

      <div class="dates-info">
        <div class="info-row">
          <span>Entrada:</span>
          <strong>{{ new Date(booking.checkIn).toLocaleDateString('es-ES') }}</strong>
        </div>
        <div class="info-row">
          <span>Salida:</span>
          <strong>{{ new Date(booking.checkOut).toLocaleDateString('es-ES') }}</strong>
        </div>
        <div class="info-row">
          <span>Noches:</span>
          <strong>{{ booking.nights }}</strong>
        </div>
      </div>

      <div class="price-breakdown">
        <div class="info-row">
          <span>Precio por noche:</span>
          <strong>{{ apartment.pricePerNight }}€</strong>
        </div>
        <div class="info-row">
          <span>Total noches:</span>
          <strong>{{ apartment.pricePerNight * booking.nights }}€</strong>
        </div>
        <div class="info-row">
          <span>Limpieza:</span>
          <strong>{{ apartment.cleaningFee }}€</strong>
        </div>
        <hr>
        <div class="info-row total">
          <span>Total:</span>
          <strong>{{ (apartment.pricePerNight * booking.nights) + apartment.cleaningFee }}€</strong>
        </div>
      </div>

      <button 
        @click="handleProceedToPayment"
        class="proceed-button"
      >
        Continuar al Pago
      </button>
    </div>
  </div>
</template>

<style scoped>
.booking-preview {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.booking-details {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.property-info {
  text-align: center;
}

.property-image {
  width: 100%;
  max-width: 400px;
  height: auto;
  border-radius: 8px;
  margin-top: 10px;
}

.dates-info, .price-breakdown {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 6px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.total {
  font-size: 1.2em;
  color: #2c3e50;
  margin-top: 10px;
}

hr {
  border: none;
  border-top: 1px solid #dee2e6;
  margin: 15px 0;
}

.proceed-button {
  width: 100%;
  padding: 12px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.proceed-button:hover {
  background-color: #45a049;
}
</style>