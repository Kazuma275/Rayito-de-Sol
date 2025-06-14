<template>
  <Bar 
    :data="computedData"
    :options="computedOptions"
    :height="height"
    :width="width"
  />
</template>

<script setup>
import { computed } from "vue"
import { Bar } from "vue-chartjs"
import {
  Chart,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
  Title
} from "chart.js"

Chart.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend, Title)

// Props: recibe reservas (el array que diste de ejemplo)
const props = defineProps({
  reservations: {
    type: Array,
    required: true
  },
  height: {
    type: Number,
    default: 300
  },
  width: {
    type: Number,
    default: 800
  },
  title: {
    type: String,
    default: "Ocupación por Propiedad (%)"
  }
})

// Lógica para calcular ocupación anual por propiedad
const propertiesOccupancy = computed(() => {
  // Agrupa reservas activas o confirmadas por propiedad
  const map = new Map();
  const year = new Date().getFullYear();
  const yearStart = new Date(year, 0, 1);
  const yearEnd = new Date(year + 1, 0, 1);

  props.reservations.forEach(r => {
    // Filtrar solo reservas activas o confirmadas
    const status = r.status || r.details?.status || "";
    if (!["confirmed", "active"].includes(status)) return;

    const prop = r.property;
    if (!prop) return;
    const key = prop.id;
    if (!map.has(key)) {
      map.set(key, {
        id: prop.id,
        name: prop.name || `Propiedad ${prop.id}`,
        image: prop.image,
        totalNights: 0,
        totalPossibleNights: 365, // Por propiedad por año
      });
    }
    // Calcular noches reservadas dentro del año
    const checkIn = new Date(
      r.checkIn || r.details?.check_in
    );
    const checkOut = new Date(
      r.checkOut || r.details?.check_out
    );
    // Si la reserva no pisa el año actual, ignorar
    let start = checkIn < yearStart ? yearStart : checkIn;
    let end = checkOut > yearEnd ? yearEnd : checkOut;
    if (end > start) {
      const nights = (end - start) / (1000 * 60 * 60 * 24);
      map.get(key).totalNights += nights;
    }
  });

  // Devuelve array con ocupación en porcentaje
  return Array.from(map.values()).map(p => ({
    ...p,
    occupancy: Math.round((p.totalNights / p.totalPossibleNights) * 100)
  }));
})

// Colores alternos para las barras
const barColors = computed(() =>
  propertiesOccupancy.value.map((_, i) =>
    i % 2 === 0
      ? "rgba(64, 169, 255, 0.8)"
      : "rgba(0, 113, 194, 0.8)"
  )
)

// Data para Chart.js
const computedData = computed(() => ({
  labels: propertiesOccupancy.value.map(p => p.name),
  datasets: [
    {
      label: "Ocupación (%)",
      data: propertiesOccupancy.value.map(p => p.occupancy),
      backgroundColor: barColors.value,
      borderRadius: 8,
      borderSkipped: false
    }
  ]
}))

const computedOptions = computed(() => ({
  responsive: true,
  plugins: {
    legend: { display: false },
    tooltip: {
      enabled: true,
      callbacks: {
        label: ctx => ` ${ctx.raw}%`
      }
    },
    title: {
      display: true,
      text: props.title,
      color: "#0071c2",
      font: { size: 18, weight: "bold" }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      max: 100,
      ticks: { color: "#333", font: { size: 14 } },
      grid: { color: "#e2e8f0" }
    },
    x: {
      ticks: { color: "#333", font: { size: 14 } },
      grid: { display: false }
    }
  }
}))
</script>