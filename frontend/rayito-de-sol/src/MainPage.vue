<template>
  <div class="main-container">
    <div class="welcome-header">
      <h1>¡Bienvenido al Portal de Propietarios!</h1>
      <p class="welcome-subtitle">
        Gestiona tus propiedades, reservas y ganancias desde un solo lugar
      </p>
    </div>

    <!-- Acciones rápidas con navegación -->
    <div class="quick-actions">
      <h2>Acciones Rápidas</h2>
      <div class="action-buttons">
        <button class="action-button" @click="goToProperties">
          <HomeIcon class="action-icon" />
          <span>Añadir Propiedad</span>
        </button>
        <button class="action-button" @click="goToBookings">
          <CalendarIcon class="action-icon" />
          <span>Gestionar Reservas</span>
        </button>
        <button class="action-button" @click="goToPayments">
          <KeyIcon class="action-icon" />
          <span>Ver Pagos</span>
        </button>
      </div>
    </div>

    <div class="welcome-message">
      <p>
        Gracias por utilizar nuestro portal de gestión de propiedades. Estamos aquí para ayudarte a maximizar tus ingresos y simplificar la gestión de tus alquileres vacacionales.
      </p>
      <p>
        Si necesitas ayuda, no dudes en contactar con nuestro equipo de soporte.
      </p>
    </div>
    
    <!-- Resumen de propiedades y reservas si están disponibles -->
    <div v-if="properties.length > 0 || bookings.length > 0" class="summary-section">
      <div v-if="properties.length > 0" class="summary-card">
        <h3>Tus Propiedades</h3>
        <p>Tienes {{ properties.length }} propiedades registradas</p>
        <button class="view-all-button" @click="goToProperties">Ver todas</button>
      </div>
      
      <div v-if="bookings.length > 0" class="summary-card">
        <h3>Tus Reservas</h3>
        <p>Tienes {{ bookings.length }} reservas activas</p>
        <button class="view-all-button" @click="goToBookings">Ver todas</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { HomeIcon, CalendarIcon, KeyIcon } from 'lucide-vue-next';
import axios from 'axios';
import { getItem } from "@/helpers/storage";
import { apiHeaders } from "@/../utils/api";

const router = useRouter();
const properties = ref([]);
const bookings = ref([]);

// Funciones de navegación
const goToProperties = () => {
  router.push('/manage/properties');
};

const goToBookings = () => {
  router.push('/manage/bookings');
};

const goToPayments = () => {
  router.push('/manage/payments');
};

// Cargar datos al montar el componente
onMounted(async () => {
  try {
    // Cargar propiedades
    const propertiesResponse = await axios.get("http://localhost:8000/api/properties", apiHeaders());
    properties.value = propertiesResponse.data || [];
    
    // Cargar reservas
    const bookingsResponse = await axios.get("http://localhost:8000/api/owner/bookings", apiHeaders());
    bookings.value = bookingsResponse.data || [];
  } catch (error) {
    console.error("Error al cargar datos:", error);
  }
});
</script>

<style scoped>
.main-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
}

.welcome-header {
  text-align: center;
  margin-bottom: 2rem;
}

.welcome-header h1 {
  color: var(--primary-color);
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.welcome-subtitle {
  color: #64748b;
  font-size: 1.1rem;
}

.quick-actions {
  margin-bottom: 2rem;
}

.quick-actions h2 {
  color: var(--primary-color);
  font-size: 1.5rem;
  margin-bottom: 1rem;
  border-bottom: 2px solid var(--primary-light);
  padding-bottom: 0.5rem;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.action-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  background: linear-gradient(to bottom, #f0f7ff, #e6f0ff);
  border: 1px solid #e6f0ff;
  border-radius: 12px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.action-button:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 113, 194, 0.15);
}

.action-icon {
  width: 32px;
  height: 32px;
  color: var(--primary-light);
  margin-bottom: 1rem;
}

.action-button span {
  font-weight: 600;
  color: var(--primary-color);
}

.welcome-message {
  background: #f8fafc;
  border-left: 4px solid var(--primary-light);
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.welcome-message p {
  color: #475569;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.welcome-message p:last-child {
  margin-bottom: 0;
}

.summary-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.summary-card {
  background: linear-gradient(to bottom, #f0f7ff, #e6f0ff);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 8px rgba(0, 53, 128, 0.05);
}

.summary-card h3 {
  color: var(--primary-color);
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.summary-card p {
  color: #64748b;
  margin-bottom: 1rem;
}

.view-all-button {
  background: var(--primary-light);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.3s ease;
}

.view-all-button:hover {
  background: var(--primary-color);
}

@media (max-width: 768px) {
  .main-container {
    padding: 1.5rem;
    margin: 1rem;
  }
  
  .welcome-header h1 {
    font-size: 1.75rem;
  }
  
  .action-buttons {
    grid-template-columns: 1fr;
  }
}
</style>