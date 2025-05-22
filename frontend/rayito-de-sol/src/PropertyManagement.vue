<template>
  <div class="app-wrapper">
    <Header :user="user" :activeTab="activeTab" @changeTab="changeTab" />

    <main class="main-content" role="main" tabindex="-1">
      <DashboardSection v-if="activeTab === 'dashboard'" :properties="properties" :bookings="bookings" />
      <PropertiesSection v-if="activeTab === 'properties'" :properties="properties" :bookings="bookings" />
      <BookingsSection v-if="activeTab === 'bookings'" :bookings="bookings" :properties="properties" />
      <CalendarSection v-if="activeTab === 'calendar'" :properties="properties" />
      <MessagesSection v-if="activeTab === 'messages'" :properties="properties" />
      <SettingsPage v-if="activeTab === 'settings'" />
      <HelpSupportSection v-if="activeTab === 'help'" />
    </main>

    <Footer @changeTab="changeTab" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import Header from './components/layout/Header.vue';
import Footer from './components/layout/Footer.vue';
import DashboardSection from './components/dashboard/DashboardSection.vue';
import PropertiesSection from './components/properties/PropertiesSection.vue';
import BookingsSection from './components/bookings/BookingsSection.vue';
import CalendarSection from './components/calendar/CalendarSection.vue';
import MessagesSection from './components/messages/MessagesSection.vue';
import SettingsPage from './components/settings/SettingsPage.vue';
import HelpSupportSection from './components/help/HelpSupportSection.vue';

// Sample user data
const user = ref({
  name: 'Carlos Rodríguez',
  email: 'carlos@example.com',
  avatar: null
});

// Determine active tab from route
const route = useRoute();
const activeTab = ref(route.path.split('/').pop() || 'dashboard');

// Sample properties data
const properties = ref([
  {
    id: 1,
    name: 'Apartamento Vista Mar',
    location: 'Calahonda, Málaga',
    bedrooms: 2,
    capacity: 4,
    price: 120,
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80',
    description: 'Hermoso apartamento con vistas al mar Mediterráneo. Perfecto para familias o parejas que buscan unas vacaciones tranquiles.',
    amenities: [1, 2, 3, 4],
    status: 'active',
    statusText: 'Activo',
    occupancy: 85,
    revenue: 3200
  },
  {
    id: 2,
    name: 'Villa con Piscina',
    location: 'Marbella, Málaga',
    bedrooms: 3,
    capacity: 6,
    price: 180,
    image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80',
    description: 'Espaciosa villa con piscina privada y jardín. Ideal para grupos grandes o familias que buscan privacidad y lujo.',
    amenities: [1, 2, 5, 6, 7],
    status: 'active',
    statusText: 'Activo',
    occupancy: 72,
    revenue: 2800
  },
  {
    id: 3,
    name: 'Ático de Lujo',
    location: 'Fuengirola, Málaga',
    bedrooms: 2,
    capacity: 4,
    price: 150,
    image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80',
    description: 'Ático moderno con terraza y vistas panorámicas. Ubicado en el centro de la ciudad, cerca de restaurantes y tiendas.',
    amenities: [1, 3, 4, 8],
    status: 'inactive',
    statusText: 'Inactivo',
    occupancy: 65,
    revenue: 2100
  }
]);

// Sample bookings data
const bookings = ref([
  {
    id: 1,
    propertyId: 1,
    guestName: 'Juan Pérez',
    guestEmail: 'juan@example.com',
    guestPhone: '+34 612 345 678',
    guests: 3,
    checkIn: '2023-08-15',
    checkOut: '2023-08-22',
    bookingDate: '2023-07-20',
    total: 890,
    status: 'confirmed',
    paymentStatus: 'paid',
    paymentMethod: 'Tarjeta de crédito',
    notes: 'El huésped ha solicitado una cuna para un bebé de 1 año.',
    history: [
      {
        type: 'created',
        text: 'Reserva creada',
        date: '2023-07-20T10:30:00'
      },
      {
        type: 'confirmed',
        text: 'Reserva confirmada',
        date: '2023-07-20T14:45:00'
      },
      {
        type: 'payment',
        text: 'Pago recibido: €890',
        date: '2023-07-20T14:50:00'
      },
      {
        type: 'message',
        text: 'Mensaje enviado al huésped con detalles de check-in',
        date: '2023-07-21T09:15:00'
      }
    ]
  },
  {
    id: 2,
    propertyId: 2,
    guestName: 'María García',
    guestEmail: 'maria@example.com',
    guestPhone: '+34 623 456 789',
    guests: 5,
    checkIn: '2023-09-05',
    checkOut: '2023-09-12',
    bookingDate: '2023-08-10',
    total: 1260,
    status: 'pending',
    paymentStatus: 'pending',
    paymentMethod: 'Pendiente',
    notes: '',
    history: [
      {
        type: 'created',
        text: 'Reserva creada',
        date: '2023-08-10T16:20:00'
      },
      {
        type: 'message',
        text: 'Solicitud de información adicional enviada',
        date: '2023-08-10T16:25:00'
      }
    ]
  },
  {
    id: 3,
    propertyId: 3,
    guestName: 'Pedro Sánchez',
    guestEmail: 'pedro@example.com',
    guestPhone: '+34 634 567 890',
    guests: 2,
    checkIn: '2023-07-20',
    checkOut: '2023-07-27',
    bookingDate: '2023-06-15',
    total: 1050,
    status: 'completed',
    paymentStatus: 'paid',
    paymentMethod: 'PayPal',
    notes: 'Huésped repetidor, ofrecer descuento en próxima reserva.',
    history: [
      {
        type: 'created',
        text: 'Reserva creada',
        date: '2023-06-15T11:10:00'
      },
      {
        type: 'confirmed',
        text: 'Reserva confirmada',
        date: '2023-06-15T13:30:00'
      },
      {
        type: 'payment',
        text: 'Pago recibido: €1050',
        date: '2023-06-15T13:35:00'
      },
      {
        type: 'message',
        text: 'Mensaje enviado al huésped con detalles de check-in',
        date: '2023-06-16T10:00:00'
      },
      {
        type: 'completed',
        text: 'Estancia completada',
        date: '2023-07-27T12:00:00'
      }
    ]
  },
  {
    id: 4,
    propertyId: 1,
    guestName: 'Ana Martínez',
    guestEmail: 'ana@example.com',
    guestPhone: '+34 645 678 901',
    guests: 4,
    checkIn: '2023-08-25',
    checkOut: '2023-09-01',
    bookingDate: '2023-07-30',
    total: 890,
    status: 'cancelled',
    paymentStatus: 'refunded',
    paymentMethod: 'Tarjeta de crédito',
    notes: 'Cancelación por motivos personales. Reembolso completo realizado.',
    history: [
      {
        type: 'created',
        text: 'Reserva creada',
        date: '2023-07-30T09:45:00'
      },
      {
        type: 'confirmed',
        text: 'Reserva confirmada',
        date: '2023-07-30T14:20:00'
      },
      {
        type: 'payment',
        text: 'Pago recibido: €890',
        date: '2023-07-30T14:25:00'
      },
      {
        type: 'message',
        text: 'Huésped solicita cancelación',
        date: '2023-08-10T16:30:00'
      },
      {
        type: 'cancelled',
        text: 'Reserva cancelada',
        date: '2023-08-10T17:15:00'
      },
      {
        type: 'payment',
        text: 'Reembolso procesado: €890',
        date: '2023-08-11T10:00:00'
      }
    ]
  },
  {
    id: 5,
    propertyId: 2,
    guestName: 'Carlos Rodríguez',
    guestEmail: 'carlos@example.com',
    guestPhone: '+34 656 789 012',
    guests: 6,
    checkIn: '2023-10-10',
    checkOut: '2023-10-17',
    bookingDate: '2023-08-05',
    total: 1260,
    status: 'confirmed',
    paymentStatus: 'paid',
    paymentMethod: 'Transferencia bancaria',
    notes: 'Grupo familiar con niños. Han solicitado información sobre actividades para niños en la zona.',
    history: [
      {
        type: 'created',
        text: 'Reserva creada',
        date: '2023-08-05T15:30:00'
      },
      {
        type: 'message',
        text: 'Solicitud de información sobre pago',
        date: '2023-08-05T15:40:00'
      },
      {
        type: 'message',
        text: 'Datos bancarios enviados',
        date: '2023-08-05T16:00:00'
      },
      {
        type: 'payment',
        text: 'Pago recibido: €1260',
        date: '2023-08-07T11:20:00'
      },
      {
        type: 'confirmed',
        text: 'Reserva confirmada',
        date: '2023-08-07T11:30:00'
      },
      {
        type: 'message',
        text: 'Información sobre actividades para niños enviada',
        date: '2023-08-08T09:15:00'
      }
    ]
  }
]);

// Method to change active tab
const changeTab = (tab) => {
  activeTab.value = tab;
};
</script>

<style>
:root {
  --primary-color: #003580;
  --primary-light: #0071c2;
  --secondary-color: #feba02;
  --text-color: #333;
  --light-gray: #f5f5f5;
  --border-color: #eee;
}

*,
*::before,
*::after {
  box-sizing: border-box;
}

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  background: var(--light-gray);
  color: var(--text-color);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  scroll-behavior: smooth;
}

body {
  min-height: 100vh;
}

.app-wrapper {
  display: flex;
  flex-direction: column;
  background: var(--light-gray);
}

.main-content {
  flex: 1 0 auto;
  width: 100%;
  display: flex;
  flex-direction: column;
  min-height: 0;
  /* No padding/margin aquí: el padding debe estar en el contenido de cada página */
}

footer, .Footer {
  flex-shrink: 0;
  background: #333;
  color: #fff;
  padding: 1rem 0;
  text-align: center;
  width: 100%;
  box-sizing: border-box;
}

header, .Header {
  width: 100%;
  box-sizing: border-box;
}

/* Asegura que el contenido tenga fondo blanco consistente */
.properties-section {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  padding: 2rem;
  min-height: 60vh; /* Ajusta según diseño para evitar fondo blanco "hueco" */
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  display: flex;
  flex-direction: column;
}

section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Responsive adjustments */
@media (max-width: 1280px) {
  .properties-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }
}

@media (max-width: 768px) {
  .properties-section {
    padding: 1rem;
    margin: 1rem;
  }
  .section-title {
    font-size: 1.5rem;
  }
}
</style>