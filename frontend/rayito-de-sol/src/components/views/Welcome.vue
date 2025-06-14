<template>
  <div class="welcome-container">
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
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { HomeIcon, CalendarIcon, KeyIcon } from 'lucide-vue-next';
import { useAuth } from '@/router/auth-guard';

const router = useRouter();
const { requireAuth } = useAuth();

// Verificar autenticación al montar el componente
onMounted(() => {
  requireAuth(); // Esto redirigirá al login si no hay sesión
});

const goToProperties = () => {
  router.push('/manage/properties');
};

const goToBookings = () => {
  router.push('/manage/bookings');
};

const goToPayments = () => {
  router.push('/manage/payments');
};
</script>

<style scoped>
.welcome-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
}

.welcome-header {
  text-align: center;
  margin-bottom: 3rem;
}

h1 {
  color: var(--primary-color);
  margin-bottom: 0.5rem;
  font-size: 2.2rem;
  font-weight: 700;
}

.welcome-subtitle {
  color: #64748b;
  font-size: 1.2rem;
}

.stats-overview {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 53, 128, 0.1);
}

.stat-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.stat-content h3 {
  font-size: 1.8rem;
  color: #003580;
  margin: 0 0 0.25rem;
  font-weight: 700;
}

.stat-content p {
  color: #64748b;
  margin: 0;
  font-size: 0.95rem;
}

.quick-actions {
  margin-bottom: 3rem;
}

.quick-actions h2 {
  font-size: 1.5rem;
  color: #003580;
  margin-bottom: 1.5rem;
  position: relative;
}

.quick-actions h2::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 50px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.action-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 1.5rem;
  background: white;
  border: 1px solid #e6f0ff;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-button:hover {
  background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.1);
}

.action-icon {
  width: 28px;
  height: 28px;
  color: #0071c2;
}

.action-button span {
  font-weight: 600;
  color: #003580;
}

.welcome-message {
  background-color: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  border-left: 4px solid #0071c2;
}

.welcome-message p {
  color: #1e293b;
  margin: 0 0 1rem;
  line-height: 1.6;
}

.welcome-message p:last-child {
  margin-bottom: 0;
}

@media (max-width: 768px) {
  .welcome-container {
    padding: 1.5rem;
  }
  
  h1 {
    font-size: 1.8rem;
  }
  
  .welcome-subtitle {
    font-size: 1rem;
  }
  
  .stats-overview {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  }
  
  .stat-card {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .action-buttons {
    grid-template-columns: 1fr;
  }
}
</style>