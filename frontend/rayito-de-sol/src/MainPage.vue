<template>
  <div class="main-container">
    <!-- Video de playa 4K como hero section -->
    <div class="hero-video-section">
      <video
        ref="heroVideo"
        class="hero-video"
        autoplay
        muted
        loop
        playsinline
        preload="metadata"
      >
        <source src="/assets/video/beach-video.mp4" type="video/mp4" />
        Tu navegador no soporta el elemento video.
      </video>
      <div class="video-overlay">
        <div class="welcome-header">
          <h1>¡Bienvenido al Portal de Propietarios!</h1>
          <p class="welcome-subtitle">
            Gestiona tus propiedades, reservas y ganancias desde un solo lugar
          </p>
        </div>
      </div>
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
        Gracias por utilizar nuestro portal de gestión de propiedades. Estamos
        aquí para ayudarte a maximizar tus ingresos y simplificar la gestión de
        tus alquileres vacacionales.
      </p>
      <p>
        Si necesitas ayuda, no dudes en contactar con nuestro equipo de soporte.
      </p>
    </div>

    <!-- Resumen de propiedades y reservas si están disponibles -->
    <div
      v-if="properties.length > 0 || bookings.length > 0"
      class="summary-section"
    >
      <div v-if="properties.length > 0" class="summary-card">
        <h3>Tus Propiedades</h3>
        <p>Tienes {{ properties.length }} propiedades registradas</p>
        <button class="view-all-button" @click="goToProperties">
          Ver todas
        </button>
      </div>

      <div v-if="bookings.length > 0" class="summary-card">
        <h3>Tus Reservas</h3>
        <p>Tienes {{ bookings.length }} reservas activas</p>
        <button class="view-all-button" @click="goToBookings">Ver todas</button>
      </div>
    </div>

    <!-- Canvas interactivo con estadísticas -->
    <div class="canvas-section">
      <h2>Estadísticas de Ocupación</h2>
      <BarChart :reservations="reservations" />
    </div>

    <!-- SVG con iconos decorativos -->

    <div class="svg-section">
      <svg
        class="decorative-svg"
        viewBox="0 0 1200 200"
        xmlns="http://www.w3.org/2000/svg"
        preserveAspectRatio="none"
      >
        <defs>
          <linearGradient id="waveGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color: #0071c2; stop-opacity: 0.3" />
            <stop offset="50%" style="stop-color: #40a9ff; stop-opacity: 0.5" />
            <stop
              offset="100%"
              style="stop-color: #0071c2; stop-opacity: 0.3"
            />
          </linearGradient>
        </defs>
        <!-- OLA: ajustada para conectar ambos lados y cubrir hasta el fondo -->
        <path
          d="M0,100 Q300,50 600,100 T1200,100 L1200,200 L0,200 Z"
          fill="url(#waveGradient)"
        />
        <!-- CÍRCULOS ANIMADOS: encima de la ola -->
        <circle cx="200" cy="80" r="12" fill="#0071c2" opacity="0.7">
          <animate
            attributeName="cy"
            values="80;60;80"
            dur="3s"
            repeatCount="indefinite"
          />
        </circle>
        <circle cx="600" cy="120" r="9" fill="#40a9ff" opacity="0.8">
          <animate
            attributeName="cy"
            values="120;100;120"
            dur="2.5s"
            repeatCount="indefinite"
          />
        </circle>
        <circle cx="1000" cy="90" r="14" fill="#0071c2" opacity="0.6">
          <animate
            attributeName="cy"
            values="90;70;90"
            dur="3.5s"
            repeatCount="indefinite"
          />
        </circle>
      </svg>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";
import { useRouter } from "vue-router";
import { HomeIcon, CalendarIcon, KeyIcon } from "lucide-vue-next";
import axios from "axios";
import { getItem } from "@/helpers/storage";
import { apiHeaders } from "@/../utils/api";
import BarChart from "@/BarChart.vue";

const reservations = ref([]);
const router = useRouter();
const properties = ref([]);
const bookings = ref([]);
const heroVideo = ref(null);
const statsCanvas = ref(null);


// Datos para el canvas (puedes reemplazarlo con datos reales si tienes)
const chartData = ref([65, 80, 45, 90, 75, 60, 85]);
const chartLabels = ref(["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul"]);

// Tooltip reactivo para el canvas
const tooltip = ref({
  visible: false,
  x: 0,
  y: 0,
  label: "",
  value: 0,
});

// Cargar datos al montar el componente
onMounted(async () => {
  try {
    // Cargar propiedades
    const propertiesResponse = await axios.get(
      "http://localhost:8000/api/properties",
      apiHeaders()
    );
    properties.value = propertiesResponse.data || [];

    const bookingsRes = await axios.get(
      "http://localhost:8000/api/owner/bookings",
      apiHeaders()
    );
    reservations.value = bookingsRes.data;

    // Cargar reservas
    const bookingsResponse = await axios.get(
      "http://localhost:8000/api/owner/bookings",
      apiHeaders()
    );
    bookings.value = bookingsResponse.data || [];
  } catch (error) {
    console.error("Error al cargar datos:", error);
  }

  await nextTick();
  drawStatsChart();
});

// Navegación
const goToProperties = () => {
  router.push("/manage/properties");
};
const goToBookings = () => {
  router.push("/manage/bookings");
};
const goToPayments = () => {
  router.push("/manage/payments");
};

// Dibuja el gráfico de barras y resalta si hay tooltip
const drawStatsChart = (hoverIndex = null) => {
  if (!statsCanvas.value) return;
  const canvas = statsCanvas.value;
  const ctx = canvas.getContext("2d");
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  const data = chartData.value;
  const labels = chartLabels.value;

  const barWidth = 80;
  const barSpacing = 20;
  const maxHeight = 200;
  const maxValue = Math.max(...data);

  // Dibujar barras
  data.forEach((value, index) => {
    const barHeight = (value / maxValue) * maxHeight;
    const x = index * (barWidth + barSpacing) + 50;
    const y = canvas.height - barHeight - 50;

    // Gradiente para las barras
    const gradient = ctx.createLinearGradient(0, y, 0, y + barHeight);
    gradient.addColorStop(0, "#40a9ff");
    gradient.addColorStop(1, "#0071c2");

    ctx.save();
    if (index === hoverIndex) {
      ctx.shadowColor = "#0071c2";
      ctx.shadowBlur = 20;
      ctx.globalAlpha = 0.9;
      ctx.strokeStyle = "#40a9ff";
      ctx.lineWidth = 4;
      ctx.strokeRect(x - 2, y - 2, barWidth + 4, barHeight + 4);
    }
    ctx.fillStyle = gradient;
    ctx.fillRect(x, y, barWidth, barHeight);
    ctx.restore();

    // Etiquetas
    ctx.fillStyle = "#333";
    ctx.font = "14px Arial";
    ctx.textAlign = "center";
    ctx.fillText(labels[index], x + barWidth / 2, canvas.height - 20);
    ctx.fillText(value + "%", x + barWidth / 2, y - 10);
  });

  // Título
  ctx.fillStyle = "#0071c2";
  ctx.font = "bold 16px Arial";
  ctx.textAlign = "left";
  ctx.fillText("Ocupación Mensual (%)", 50, 30);
};

// Manejar movimiento del mouse en canvas (tooltip interactivo)
const handleCanvasMouseMove = (event) => {
  const canvas = statsCanvas.value;
  const rect = canvas.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;

  const barWidth = 80;
  const barSpacing = 20;
  const topMargin = 50;
  const barAreaHeight = 200;

  let found = false;
  chartData.value.forEach((value, index) => {
    const barHeight = (value / Math.max(...chartData.value)) * barAreaHeight;
    const barX = index * (barWidth + barSpacing) + 50;
    const barY = canvas.height - barHeight - topMargin;
    if (
      x >= barX &&
      x <= barX + barWidth &&
      y >= barY &&
      y <= barY + barHeight
    ) {
      found = true;
      tooltip.value.visible = true;
      tooltip.value.x = barX + barWidth / 2 - 40; // ajusta -40 para centrar el tooltip
      tooltip.value.y = barY - 38; // encima de la barra
      tooltip.value.label = chartLabels.value[index];
      tooltip.value.value = value;
      drawStatsChart(index);
    }
  });

  if (!found) {
    tooltip.value.visible = false;
    drawStatsChart();
  }
};

const clearTooltip = () => {
  tooltip.value.visible = false;
  drawStatsChart();
};
</script>

<style scoped>
.main-container {
  max-width: 1200px;
  margin: 0 auto;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
}

/* Estilos para el video hero */
.hero-video-section {
  position: relative;
  height: 400px;
  overflow: hidden;
  border-radius: 16px 16px 0 0;
}
.hero-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.3),
    rgba(0, 0, 0, 0.6)
  );
  display: flex;
  align-items: center;
  justify-content: center;
}
.welcome-header {
  text-align: center;
  color: white;
}
.welcome-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}
.welcome-subtitle {
  font-size: 1.2rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Canvas estilos */
.canvas-section {
  padding: 2rem;
  background: #f8fafc;
  text-align: center;
}
.canvas-section h2 {
  color: var(--primary-color, #0071c2);
  font-size: 1.5rem;
  margin-bottom: 1rem;
}
.stats-canvas {
  max-width: 100%;
  height: auto;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: box-shadow 0.2s;
}

/* Tooltip canvas (usa el inline style, pero puedes ajustar aquí si quieres) */

/* SVG decorativo */
.svg-section {
  margin: 0;
  height: 200px;
  overflow: hidden;
}
.decorative-svg {
  width: 100%;
  height: 100%;
  display: block;
}

/* Quick actions y otros estilos */
.quick-actions {
  padding: 2rem;
}
.quick-actions h2 {
  color: var(--primary-color, #0071c2);
  font-size: 1.5rem;
  margin-bottom: 1rem;
  border-bottom: 2px solid var(--primary-light, #40a9ff);
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
  color: var(--primary-light, #40a9ff);
  margin-bottom: 1rem;
}
.action-button span {
  font-weight: 600;
  color: var(--primary-color, #0071c2);
}
.welcome-message {
  background: #f8fafc;
  border-left: 4px solid var(--primary-light, #40a9ff);
  padding: 1.5rem;
  margin: 0 2rem 2rem 2rem;
  border-radius: 8px;
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
  padding: 0 2rem 2rem 2rem;
}
.summary-card {
  background: linear-gradient(to bottom, #f0f7ff, #e6f0ff);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 8px rgba(0, 53, 128, 0.05);
}
.summary-card h3 {
  color: var(--primary-color, #0071c2);
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}
.summary-card p {
  color: #64748b;
  margin-bottom: 1rem;
}
.view-all-button {
  background: var(--primary-light, #40a9ff);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.3s ease;
}
.view-all-button:hover {
  background: var(--primary-color, #0071c2);
}

@media (max-width: 768px) {
  .hero-video-section {
    height: 300px;
  }
  .welcome-header h1 {
    font-size: 2rem;
  }
  .welcome-subtitle {
    font-size: 1rem;
  }
  .action-buttons {
    grid-template-columns: 1fr;
  }
  .canvas-section {
    padding: 1rem;
  }
  .stats-canvas {
    width: 100%;
  }
}
</style>