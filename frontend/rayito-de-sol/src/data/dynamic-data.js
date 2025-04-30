// Importación de Axios o Fetch para llamadas a API
import axios from 'axios';

/**
 * Ejemplo de configuración para datos dinámicos.
 * Ajusta las URLs según tus necesidades (pueden ser APIs internas o externas).
 */

// 1. Propiedades (fetch)
const fetchProperties = async () => {
  try {
    const response = await axios.get('/api/properties'); // ENDPOINT dinámico
    properties.value = response.data;
  } catch (error) {
    console.error("Error al cargar propiedades:", error);
  }
};

// 2. Reservas Activas (filtrado dinámico)
const activeBookings = computed(() =>
  allBookings.value.filter(booking => booking.status === 'confirmed').length
);

// 3. Tasa de Ocupación (cálculo dinámico)
const occupancyRate = computed(() => {
  const totalDays = 30; // Ejemplo: 30 días del mes
  const bookedDays = allBookings.value.reduce((acc, booking) => {
    const checkIn = new Date(booking.checkIn);
    const checkOut = new Date(booking.checkOut);
    return acc + Math.min(totalDays, (checkOut - checkIn) / (1000 * 60 * 60 * 24)); // Días reservados
  }, 0);
  const totalCapacity = properties.value.length * totalDays;
  return ((bookedDays / totalCapacity) * 100).toFixed(2); // % Ocupación
});

// 4. Ingresos (últimos 30 días)
const monthlyRevenue = computed(() => {
  const last30Days = new Date();
  last30Days.setDate(last30Days.getDate() - 30);
  return allBookings.value
    .filter(booking => new Date(booking.checkOut) > last30Days)
    .reduce((total, booking) => total + booking.total, 0);
});

// 5. Mensajes Recientes
const fetchMessages = async () => {
  try {
    const response = await axios.get('/api/messages'); // ENDPOINT dinámico
    messages.value = response.data;
  } catch (error) {
    console.error("Error al cargar mensajes:", error);
  }
};

// 6. Fechas del Calendario
const calendarDays = computed(() => {
  const days = [];
  const firstDay = new Date(currentYear.value, currentMonth.value, 1);
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);

  // Llenar con días vacíos al inicio
  let firstDayOfWeek = firstDay.getDay() - 1;
  if (firstDayOfWeek < 0) firstDayOfWeek = 6;

  for (let i = 0; i < firstDayOfWeek; i++) days.push({ date: null });

  for (let i = 1; i <= lastDay.getDate(); i++) {
    const isToday =
      new Date().toDateString() ===
      new Date(currentYear.value, currentMonth.value, i).toDateString();

    days.push({ date: i, available: Math.random() > 0.3, booked: Math.random() < 0.2, isToday });
  }

  return days;
});

// 7. Configuración de Usuario (fetch dinámico)
const fetchUserSettings = async () => {
  try {
    const response = await axios.get('/api/user/settings'); // ENDPOINT dinámico
    settings.value = response.data;
  } catch (error) {
    console.error("Error al cargar configuración del usuario:", error);
  }
};

// Llamar automáticamente cuando el componente se monte
onMounted(() => {
  fetchProperties();
  fetchMessages();
  fetchUserSettings();
});