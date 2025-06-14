  <template>
  <div class="renters-portal">
    <RentersHeader :user="user" :activeTab="activeTab" @changeTab="changeTab" />

    <main class="main-content">
      <RentersDashboardSection
        v-if="activeTab === 'dashboard'"
        :user="user"
        :active-bookings="activeBookings"
        :next-booking-date="nextBookingDate"
        :unread-messages="unreadMessages"
        :favorites="favorites"
        :recent-messages="recentMessages"
        :recommended-properties="recommendedProperties"
        :format-date="formatDate"
        :format-date-time="formatDateTime"
        :get-status-text="getStatusText"
        :is-favorite="isFavorite"
        :toggle-favorite="toggleFavorite"
        @change-tab="changeTab"
        @view-property="viewProperty"
      />

      <SearchSection
        v-if="activeTab === 'search'"
        :properties="filteredProperties"
        :search-query="searchQuery"
        :filters="filters"
        :show-advanced-filters="showAdvancedFilters"
        :amenities="amenities"
        :today="today"
        :is-loading="isLoading"
        :isFavorite="isFavorite"
        :toggleFavorite="toggleFavorite"
        @update-search="searchQuery = $event"
        @update-filters="filters = { ...filters, ...$event }"
        @toggle-advanced="toggleAdvancedFilters"
        @clear-filters="clearFilters"
        @search="searchProperties"
        @apply-filters="applyFilters"
        @view-property="viewProperty"
      />

      <RentersBookingsSection
        v-if="activeTab === 'bookings'"
        :bookings="filteredBookings"
        :booking-filters="bookingFilters"
        :active-filter="activeFilter"
        :is-loading="isLoading"
        :show-booking-details="showBookingDetails"
        :selected-booking="selectedBooking"
        :format-date="formatDate"
        :format-date-time="formatDateTime"
        :get-status-text="getStatusText"
        :can-cancel="canCancel"
        :calculate-nights="calculateNights"
        :get-event-icon="getEventIcon"
        @change-filter="handleFilterChange"
        @view-details="handleViewDetails"
        @cancel-booking="handleCancelBooking"
        @contact-owner="handleContactOwner"
        @close-details="handleCloseDetails"
        @view-property="handleViewProperty"
        @change-tab="handleChangeTab"
      />

<PropertyDetailSection
    v-if="activeTab === 'property-detail'"
    :property="selectedProperty"
    :property-id="propertyId"
    :booking-data="bookingData"
    :amenities="amenities"
    :today="today"
    :cleaning-fee="cleaningFee"
    :can-book="canBook"
    :isFavorite="isFavorite"
    :toggleFavorite="toggleFavorite"
    :calculate-nights="calculateNights"
    :calculate-subtotal="calculateSubtotal"
    :calculate-service-fee="calculateServiceFee"
    :calculate-total="calculateTotal"
    :get-amenity-name="getAmenityName"
    @go-back="goBack"
    @update-booking-data="bookingData = { ...bookingData, ...$event }"
    @book-property="bookProperty"
    @contact-owner="contactOwner"
  />

      <RentersMessagesSection
        v-if="activeTab === 'messages'"
        :conversations="conversations"
        :active-conversation="activeConversation"
        @select-conversation="activeConversation = $event"
        @send-message="sendMessage"
      />

      <RentersFavoritesSection
        v-if="activeTab === 'favorites'"
        :favorites="favoriteProperties"
        :is-loading="isLoading"
        :isFavorite="isFavorite"
        :toggleFavorite="toggleFavorite"
        :formatDate="formatDate"
        :calculateRating="calculateRating"
        :search-query="searchQuery"
        :sort-by="sortBy"
        :filter-by="filterBy"
        @update-search="searchQuery = $event"
        @update-sort="sortBy = $event"
        @update-filter="filterBy = $event"
        @remove-favorite="toggleFavorite"
        @clear-all-favorites="clearAllFavorites"
        @change-tab="currentTab = $event"
      />

      <ProfileSection
        v-if="activeTab === 'profile'"
        :profile-tabs="profileTabs"
        :active-tab="activeProfileTab"
        :profile-data="profileData"
        :preview-image="previewImage"
        :payment-methods="paymentMethods"
        :notification-settings="notificationSettings"
        @change-tab="activeProfileTab = $event"
        @trigger-file-input="triggerFileInput"
        @handle-avatar-change="handleAvatarChange"
        @save-profile="saveProfile"
        @add-payment-method="addPaymentMethod"
        @edit-payment="editPayment"
        @delete-payment="deletePayment"
        @save-notification-settings="saveNotificationSettings"
      />
    </main>

    <RentersFooter @changeTab="changeTab" />
  </div>
</template>

  <script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import { apiHeaders } from "@/../utils/api";

// Componentes
import RentersHeader from "./components/layout/RentersHeader.vue";
import RentersFooter from "./components/layout/RentersFooter.vue";
import RentersDashboardSection from "./components/dashboard/RentersDashboardSection.vue";
import SearchSection from "./components/search/SearchSection.vue";
import RentersBookingsSection from "./components/bookings/RentersBookingsSection.vue";
import PropertyDetailSection from "./components/property/PropertyDetailSection.vue";
import RentersMessagesSection from "./components/messages/RentersMessagesSection.vue";
import ProfileSection from "./components/profile/ProfileSection.vue";
import RentersFavoritesSection from "./components/favorites/RentersFavoritesSection.vue";
import { useFavorites } from "@/composables/useFavorites";

const { favoriteIds, isFavorite, toggleFavorite } = useFavorites();

const router = useRouter();
const route = useRoute();

// Estado principal
const user = ref({
  id: 1,
  name: "Ana",
  lastname: "García",
  email: "ana.garcia@example.com",
  phone: "+34 612 345 678",
  avatar: null,
});

const API_BASE_URL = "http://localhost:8000/api";
const activeFilter = ref("all");
const allProperties = ref([]);
const activeTab = ref("dashboard");
const isLoading = ref(false);
const properties = ref([]);
const bookings = ref([]);
const conversations = ref([]);
const propertyId = computed(() => {
  return selectedProperty.value?.id || null;
});

// Booking filters configuration
const bookingFilters = ref([
  { id: "all", name: "Todas" },
  { id: "confirmed", name: "Confirmadas" },
  { id: "pending", name: "Pendientes" },
  { id: "active", name: "Activas" },
  { id: "completed", name: "Completadas" },
  { id: "cancelled", name: "Canceladas" },
]);

// Generar las propiedades favoritas con más info
const favoriteProperties = computed(() =>
  allProperties.value.filter((p) => favoriteIds.value.includes(p.id))
);

// Tabs de navegación
const tabs = [
  { id: "dashboard", name: "Inicio", path: "/renters/dashboard" },
  { id: "search", name: "Buscar", path: "/renters/search" },
  { id: "bookings", name: "Reservas", path: "/renters/bookings" },
  { id: "favorites", name: "Favoritos", path: "/renters/favorites" },
  { id: "messages", name: "Mensajes", path: "/renters/messages" },
  { id: "profile", name: "Perfil", path: "/renters/profile" },
];

// Funciones de navegación
const changeTab = (tabId) => {
  activeTab.value = tabId;
  const tab = tabs.find((t) => t.id === tabId);
  if (tab) {
    router.push(tab.path);
  }
};

const updateActiveTabFromRoute = async () => {
  const path = route.path;

  if (path.includes("/renters/dashboard")) {
    activeTab.value = "dashboard";
  } else if (path.includes("/renters/search")) {
    activeTab.value = "search";
  } else if (path.includes("/renters/bookings")) {
    activeTab.value = "bookings";
  } else if (path.includes("/renters/favorites")) {
    activeTab.value = "favorites";
  } else if (path.includes("/renters/messages")) {
    activeTab.value = "messages";
  } else if (path.includes("/renters/profile")) {
    activeTab.value = "profile";
  } else if (path.includes("/renters/property/")) {
    const propertyId = parseInt(path.split("/").pop());
    if (propertyId) {
      console.log("Route contains property ID:", propertyId);
      await viewProperty(propertyId);
    }
  }
};

// Datos y estado para búsqueda
const searchQuery = ref("");
const showAdvancedFilters = ref(false);
const filters = ref({
  checkIn: "",
  checkOut: "",
  guests: 2,
  maxPrice: 200,
  bedrooms: [],
  amenities: [],
});

// Datos para reservas
const activeBookingFilter = ref("all");
const showBookingDetails = ref(false);
const selectedBooking = ref(null);

// Datos para detalles de propiedad
const selectedProperty = ref(null);
const bookingData = ref({
  checkIn: "",
  checkOut: "",
  guests: 2,
});

// Datos para perfil
const profileTabs = [
  { id: "personal", name: "Información Personal" },
  { id: "payment", name: "Métodos de Pago" },
  { id: "notifications", name: "Notificaciones" },
];

const activeProfileTab = ref("personal");
const previewImage = ref(null);
const profileData = ref({
  name: user.value.name,
  lastname: user.value.lastname,
  email: user.value.email,
  phone: user.value.phone,
  avatar: user.value.avatar,
});

const paymentMethods = ref([
  { cardType: "Visa", last4: "4242", expiryDate: "12/2025" },
]);

const notificationSettings = ref({
  bookingConfirmationEmail: true,
  newMessageEmail: true,
  specialOffersEmail: false,
  bookingConfirmationSMS: false,
});

// Favoritos
const favorites = ref([1, 3]);

// Variables para favoritos y ordenamiento
const sortBy = ref("date-desc");
const filterBy = ref("all");

// Amenities
const amenities = [
  { id: "wifi", name: "WiFi" },
  { id: "pool", name: "Piscina" },
  { id: "parking", name: "Aparcamiento" },
  { id: "ac", name: "Aire acondicionado" },
  { id: "gym", name: "Gimnasio" },
  { id: "kitchen", name: "Cocina equipada" },
  { id: "tv", name: "TV" },
  { id: "washer", name: "Lavadora" },
];

// Constantes para reservas
const cleaningFee = 30;
const serviceFeePercentage = 0.1;

// Computed properties
const today = computed(() => {
  const date = new Date();
  return date.toISOString().split("T")[0];
});

const filteredProperties = computed(() => {
  let result = [...properties.value];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (property) =>
        property.name.toLowerCase().includes(query) ||
        property.location.toLowerCase().includes(query)
    );
  }

  if (filters.value.maxPrice) {
    result = result.filter(
      (property) => property.price <= filters.value.maxPrice
    );
  }

  if (filters.value.guests) {
    result = result.filter(
      (property) => property.capacity >= filters.value.guests
    );
  }

  if (filters.value.bedrooms.length > 0) {
    result = result.filter((property) => {
      if (filters.value.bedrooms.includes(5) && property.bedrooms >= 5) {
        return true;
      }
      return filters.value.bedrooms.includes(property.bedrooms);
    });
  }

  if (filters.value.amenities.length > 0) {
    result = result.filter((property) =>
      filters.value.amenities.every((amenity) =>
        property.amenities.includes(amenity)
      )
    );
  }

  return result;
});

// Computed properties para reservas
const filteredBookings = computed(() => {
  if (activeFilter.value === "all") {
    return bookings.value;
  }
  return bookings.value.filter((booking) => {
    const status = booking.details?.status || booking.status;
    return status === activeFilter.value;
  });
});

const activeBookings = computed(() => {
  return bookings.value.filter(
    (booking) => booking.status === "confirmed" || booking.status === "pending"
  );
});

const nextBookingDate = computed(() => {
  const upcoming = bookings.value
    .filter(
      (booking) =>
        booking.status === "confirmed" && new Date(booking.checkIn) > new Date()
    )
    .sort((a, b) => new Date(a.checkIn) - new Date(b.checkIn));

  if (upcoming.length > 0) {
    return formatDate(upcoming[0].checkIn);
  }

  return "No hay reservas próximas";
});

const unreadMessages = computed(() => {
  return 1; // Mock value
});

const recentMessages = computed(() => {
  return conversations.value
    .map((c) => ({
      id: c.id,
      sender: c.name,
      property: c.property,
      text: c.lastMessage.text,
      createdAt: new Date().toISOString(),
    }))
    .slice(0, 3);
});

const recommendedProperties = computed(() => {
  return properties.value
    .filter((p) => p.id !== selectedProperty.value?.id)
    .sort(() => 0.5 - Math.random())
    .slice(0, 3);
});

const availableProperties = computed(() => {
  const reservedIds = bookings.value.map((b) => b.propertyId);
  return properties.value.filter((p) => !reservedIds.includes(p.id));
});

const canBook = computed(() => {
  return (
    bookingData.value.checkIn &&
    bookingData.value.checkOut &&
    calculateNights() > 0 &&
    bookingData.value.guests > 0 &&
    bookingData.value.guests <= selectedProperty.value?.capacity
  );
});

// Funciones de utilidad
const formatDate = (dateString) => {
  if (!dateString) return "";
  const options = { day: "numeric", month: "short", year: "numeric" };
  return new Date(dateString).toLocaleDateString("es-ES", options);
};

const formatDateTime = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  const today = new Date();

  if (date.toDateString() === today.toDateString()) {
    return date.toLocaleTimeString("es-ES", {
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  if (date.toDateString() === yesterday.toDateString()) {
    return "Ayer";
  }

  return date.toLocaleDateString("es-ES", { day: "numeric", month: "short" });
};

const getStatusText = (status) => {
  const statusTexts = {
    pending: "Pendiente",
    confirmed: "Confirmada",
    active: "Activa",
    completed: "Completada",
    cancelled: "Cancelada",
  };
  return statusTexts[status] || status;
};

const getAmenityName = (id) => {
  const amenity = amenities.find((a) => a.id === id);
  return amenity ? amenity.name : "";
};

// Función para calcular rating (placeholder)
const calculateRating = (property) => {
  return 4.5; // Mock rating
};

// Funciones de búsqueda
const toggleAdvancedFilters = () => {
  showAdvancedFilters.value = !showAdvancedFilters.value;
};

const clearFilters = () => {
  filters.value = {
    checkIn: "",
    checkOut: "",
    guests: 2,
    maxPrice: 200,
    bedrooms: [],
    amenities: [],
  };
  searchQuery.value = "";
};

async function searchProperties() {
  isLoading.value = true;

  // Construye solo los parámetros válidos
  const params = {};
  if (searchQuery.value) params.search = searchQuery.value;
  if (filters.value.checkIn) params.checkIn = filters.value.checkIn;
  if (filters.value.checkOut) params.checkOut = filters.value.checkOut;
  if (filters.value.guests) params.guests = filters.value.guests;
  if (filters.value.maxPrice) params.maxPrice = filters.value.maxPrice;
  if (filters.value.bedrooms) params.bedrooms = filters.value.bedrooms;
  if (filters.value.bathrooms) params.bathrooms = filters.value.bathrooms;
  if (filters.value.propertyTypes && filters.value.propertyTypes.length)
    params.propertyTypes = filters.value.propertyTypes.join(",");
  if (filters.value.amenities && filters.value.amenities.length)
    params.amenities = filters.value.amenities.join(",");

  try {
    const response = await axios.get(
      "http://localhost:8000/api/properties/search",
      {
        params,
        ...apiHeaders(),
      }
    );
    properties.value = response.data;
    console.log("properties:", properties.value);
  } catch (error) {
    console.error("Error al buscar propiedades:", error);
    properties.value = [];
  } finally {
    isLoading.value = false;
  }
}

const applyFilters = () => {
  searchProperties();
  showAdvancedFilters.value = false;
};

// Funciones de propiedades
const viewProperty = async (id) => {
  console.log("Viewing property with ID:", id);
  
  try {
    isLoading.value = true;
    
    // Buscar primero en las propiedades ya cargadas
    let property = properties.value.find(p => p.id === id);
    
    // Si no se encuentra, intentar obtenerla directamente de la API
    if (!property) {
      console.log("Property not found in cache, fetching from API");
      const response = await axios.get(`${API_BASE_URL}/properties/${id}`, apiHeaders());
      property = response.data;
      
      // Normalizar la propiedad para asegurar que tiene la estructura correcta
      if (property) {
        let amenities = [];
        if (Array.isArray(property.amenities)) {
          amenities = property.amenities;
        } else if (typeof property.amenities === "string" && property.amenities.length > 0) {
          try {
            amenities = JSON.parse(property.amenities);
          } catch (e) {
            amenities = property.amenities.split(",").map(a => a.trim());
          }
        }
        
        property = {
          ...property,
          amenities,
          price: Number(property.price || 0),
          bedrooms: Number(property.bedrooms || 0),
          capacity: Number(property.capacity || 0),
          image: property.image || "/placeholder.svg?height=300&width=500",
          reviews: property.reviews || []
        };
      }
    }
    
    if (property) {
      console.log("Setting selected property:", property);
      selectedProperty.value = property;
      activeTab.value = "property-detail";
      router.push(`/renters/property/${id}`);
    } else {
      console.error("Property not found");
      alert("No se pudo encontrar la propiedad");
    }
  } catch (error) {
    console.error("Error fetching property:", error);
    alert("Error al cargar los detalles de la propiedad");
  } finally {
    isLoading.value = false;
  }
};

const goBack = () => {
  router.back();
};

// Funciones de reservas
const calculateNights = (booking = null) => {
  const data = booking || bookingData.value;
  if (!data.checkIn || !data.checkOut) return 0;

  const checkIn = new Date(data.checkIn);
  const checkOut = new Date(data.checkOut);
  const diffTime = checkOut.getTime() - checkIn.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return diffDays > 0 ? diffDays : 0;
};

const calculateSubtotal = () => {
  const nights = calculateNights();
  return nights * selectedProperty.value.price;
};

const calculateServiceFee = () => {
  const subtotal = calculateSubtotal();
  return Math.round(subtotal * serviceFeePercentage);
};

const calculateTotal = () => {
  const subtotal = calculateSubtotal();
  const serviceFee = calculateServiceFee();
  return subtotal + cleaningFee + serviceFee;
};

const bookProperty = () => {
  if (!canBook.value) return;

  const newBooking = {
    id: `B${Date.now().toString().substring(9)}`,
    propertyId: selectedProperty.value.id,
    property: selectedProperty.value,
    checkIn: bookingData.value.checkIn,
    checkOut: bookingData.value.checkOut,
    guests: bookingData.value.guests,
    total: calculateTotal(),
    status: "pending",
    createdAt: new Date().toISOString(),
  };

  bookings.value.push(newBooking);
  alert(
    "¡Reserva realizada con éxito! Pendiente de confirmación por el propietario."
  );
  changeTab("bookings");
};

const viewBookingDetails = (id) => {
  const booking = bookings.value.find((b) => b.id === id);
  if (booking) {
    selectedBooking.value = booking;
    showBookingDetails.value = true;
  }
};

const closeBookingDetails = () => {
  showBookingDetails.value = false;
  selectedBooking.value = null;
};

const canCancel = (booking) => {
  const now = new Date();
  const checkIn = new Date(booking.checkIn);
  const status = booking.status;

  // Can cancel if booking is confirmed or pending and check-in is more than 24 hours away
  return (
    (status === "confirmed" || status === "pending") &&
    checkIn > new Date(now.getTime() + 24 * 60 * 60 * 1000)
  );
};

const cancelBooking = (id) => {
  const index = bookings.value.findIndex((b) => b.id === id);
  if (index !== -1) {
    bookings.value[index].status = "cancelled";
    alert("Reserva cancelada con éxito.");
  }
};

const getEventIcon = (type) => {
  // Retorna el componente de icono basado en el tipo de evento
  return "CheckIcon"; // Placeholder
};

// Funciones de mensajes
const contactOwner = () => {
  const existingConversation = conversations.value.findIndex(
    (c) => c.property.id === selectedProperty.value.id
  );

  if (existingConversation === -1) {
    conversations.value.push({
      id: Date.now(),
      name: `Propietario de ${selectedProperty.value.name}`,
      property: selectedProperty.value,
      bookingId: null,
      lastMessage: {
        text: "",
        time: "Ahora",
      },
      messages: [],
    });

    activeConversation.value = conversations.value.length - 1;
  } else {
    activeConversation.value = existingConversation;
  }

  changeTab("messages");
};

const contactOwnerForBooking = (bookingId) => {
  const booking = bookings.value.find((b) => b.id === bookingId);
  if (!booking) return;

  const existingConversation = conversations.value.findIndex(
    (c) => c.bookingId === bookingId
  );

  if (existingConversation === -1) {
    conversations.value.push({
      id: Date.now(),
      name: `Propietario de ${booking.property.name}`,
      property: booking.property,
      bookingId: booking.id,
      lastMessage: {
        text: "",
        time: "Ahora",
      },
      messages: [],
    });

    activeConversation.value = conversations.value.length - 1;
  } else {
    activeConversation.value = existingConversation;
  }

  changeTab("messages");
};

const activeConversation = ref(null);

const sendMessage = (message) => {
  if (activeConversation.value !== null && message.trim()) {
    const conversation = conversations.value[activeConversation.value];
    conversation.messages.push({
      text: message,
      time: new Date().toLocaleTimeString("es-ES", {
        hour: "2-digit",
        minute: "2-digit",
      }),
      sent: true,
    });
    conversation.lastMessage = {
      text: message,
      time: "Ahora",
    };
  }
};

// Funciones de perfil
const triggerFileInput = () => {
  // Implementar lógica para abrir selector de archivos
};

const handleAvatarChange = (event) => {
  const file = event.target.files[0];
  if (file && file.type.startsWith("image/")) {
    profileData.value.avatar = file;
    previewImage.value = URL.createObjectURL(file);
  }
};

const saveProfile = () => {
  user.value = {
    ...user.value,
    name: profileData.value.name,
    lastname: profileData.value.lastname,
    email: profileData.value.email,
    phone: profileData.value.phone,
    avatar: profileData.value.avatar,
  };

  alert("Perfil actualizado correctamente");
};

const addPaymentMethod = () => {
  console.log("Add payment method");
};

const editPayment = (index) => {
  console.log("Edit payment method", index);
};

const deletePayment = (index) => {
  paymentMethods.value.splice(index, 1);
};

const saveNotificationSettings = () => {
  alert("Preferencias de notificaciones guardadas correctamente");
};

// Función para limpiar todos los favoritos
const clearAllFavorites = () => {
  if (confirm("¿Estás seguro de que quieres eliminar todos los favoritos?")) {
    favoriteIds.value.splice(0);
  }
};

// Carga inicial de datos
const fetchProperties = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(
      "http://localhost:8000/api/properties",
      apiHeaders()
    );
    const fetchedProperties = response.data.map((prop) => {
      let image = prop.image;
      if (!image) {
        image = "/placeholder.svg?height=300&width=500";
      }

      let amenities = [];
      if (Array.isArray(prop.amenities)) {
        amenities = prop.amenities;
      } else if (
        typeof prop.amenities === "string" &&
        prop.amenities.length > 0
      ) {
        try {
          amenities = JSON.parse(prop.amenities);
        } catch (e) {
          amenities = prop.amenities.split(",").map((a) => a.trim());
        }
      }

      let price = Number(prop.price);
      if (isNaN(price)) price = 0;

      return {
        id: prop.id,
        name: prop.name || "Sin nombre",
        location: prop.location || "",
        bedrooms: Number(prop.bedrooms) || 0,
        capacity: Number(prop.capacity) || 0,
        price,
        image,
        description: prop.description || "",
        amenities,
        status: prop.status || "",
        statusText: prop.statusText || "",
        created_at: prop.created_at,
        updated_at: prop.updated_at,
      };
    });

    properties.value = fetchedProperties;
    allProperties.value = fetchedProperties; // Para favoritos
  } catch (error) {
    console.error("Error cargando propiedades:", error);
    properties.value = [];
    allProperties.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Funciones para obtener reservas
const fetchBookings = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`${API_BASE_URL}/bookings`, apiHeaders());

    // Transform the data to match the component's expected format
    bookings.value = response.data.map((booking) => ({
      id: booking.id,
      property: {
        id: booking.property_id,
        name: booking.property?.name || "Propiedad",
        location: booking.property?.location || "Ubicación no disponible",
        image: booking.property?.image || "/img/placeholder.jpg",
        bedrooms: booking.property?.bedrooms || 1,
        maxGuests: booking.property?.capacity || 2, // Usar 'capacity' en lugar de 'max_guests'
        owner: {
          name: booking.property?.owner?.name || "Propietario",
        },
      },
      status: booking.details?.status || "confirmed",
      checkIn: booking.details?.check_in || booking.reservation_date,
      checkOut: booking.details?.check_out || booking.reservation_date,
      guests: booking.details?.guests || 1,
      totalPrice: booking.details?.total_price || 0,
      subtotal: booking.details?.subtotal || 0,
      serviceFee: booking.details?.service_fee || 0,
      taxes: booking.details?.taxes || 0,
      cleaningFee: booking.details?.cleaning_fee || 0,
      pricePerNight: booking.property?.price || 0, // Usar 'price' en lugar de 'price_per_night'
      createdAt: booking.created_at,
      guestName: booking.details?.guest_name || "",
      guestEmail: booking.details?.guest_email || "",
      guestPhone: booking.details?.guest_phone || "",
      bookingReference: booking.details?.booking_reference || `BK${booking.id}`,
      paymentIntentId: booking.details?.payment_intent_id || "",
      hasReview: false, // You can implement this later
    }));

    console.log("Reservas cargadas:", bookings.value);
  } catch (error) {
    console.error("Error fetching bookings:", error);
    // Handle error - maybe show a toast notification
  } finally {
    isLoading.value = false;
  }
};

// Event handlers para reservas
const handleFilterChange = (filterId) => {
  activeFilter.value = filterId;
};

const handleViewDetails = (booking) => {
  selectedBooking.value = booking;
  showBookingDetails.value = true;
};

const handleCancelBooking = async (booking) => {
  if (confirm("¿Estás seguro de que quieres cancelar esta reserva?")) {
    try {
      // Update booking status to cancelled using the new cancel endpoint
      await axios.post(
        `${API_BASE_URL}/bookings/${booking.id}/cancel`,
        {
          reason: "Cancelado por el usuario",
        },
        apiHeaders()
      );

      // Refresh bookings
      await fetchBookings();

      // Close details modal if it's open
      if (
        showBookingDetails.value &&
        selectedBooking.value?.id === booking.id
      ) {
        showBookingDetails.value = false;
        selectedBooking.value = null;
      }

      alert("Reserva cancelada exitosamente");
    } catch (error) {
      console.error("Error cancelling booking:", error);
      alert("Error al cancelar la reserva");
    }
  }
};

const handleContactOwner = (booking) => {
  // Implement contact functionality
  alert(`Contactar con el propietario de ${booking.property.name}`);
};

const handleCloseDetails = () => {
  showBookingDetails.value = false;
  selectedBooking.value = null;
};

const handleViewProperty = (property) => {
  viewProperty(property.id);
};

const handleChangeTab = (tab) => {
  changeTab(tab);
};

// Watchers y lifecycle
const updateTab = () => {
  updateActiveTabFromRoute();
};

watch(() => route.path, updateTab, { immediate: true });

onMounted(async () => {
  isLoading.value = true;
  await fetchProperties();
  await fetchBookings();
  isLoading.value = false;
  searchProperties();
});
</script>

  <style scoped>
.renters-portal {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
    Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  color: #333;
  background-color: #f5f5f5;
  min-height: 100vh;
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}
</style>
