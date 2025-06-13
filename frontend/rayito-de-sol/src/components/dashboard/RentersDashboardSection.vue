<template>
  <div class="dashboard-section">
    <div class="section-header">
      <h2 class="section-title">Panel de Control</h2>
      <div class="welcome-message">
        <p>
          Bienvenido,
          <span class="user-name">{{
            computedUserSummary?.name || "Usuario"
          }}</span>
        </p>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <CalendarIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">
            {{ computedUserSummary?.totalBookings || 0 }}
          </p>
          <h3 class="stat-title">Reservas Totales</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <MapPinIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">
            {{ computedUserSummary?.citiesVisited || 0 }}
          </p>
          <h3 class="stat-title">Ciudades Visitadas</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <HeartIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ favoriteIds.length || 0 }}</p>
          <h3 class="stat-title">Favoritos</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrapper">
          <EuroIcon class="stat-icon" />
          <div class="stat-icon-glow"></div>
        </div>
        <div class="stat-content">
          <p class="stat-value">{{ computedUserSummary?.totalSpent || 0 }}€</p>
          <h3 class="stat-title">Total Gastado</h3>
        </div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="dashboard-column">
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Próximos Viajes</h3>
            <button class="view-all-button" @click="navigateTo('bookings')">
              Ver todos
              <ChevronRightIcon class="button-icon" />
            </button>
          </div>

          <div
            v-if="upcomingBookings && upcomingBookings.length > 0"
            class="upcoming-bookings"
          >
            <div
              v-for="booking in upcomingBookings.slice(0, 3)"
              :key="booking.id"
              class="booking-item"
            >
              <div class="booking-property">
                <img
                  :src="booking.property?.image || '/img/placeholder.jpg'"
                  class="booking-image"
                  @error="handleImageError"
                />
                <div>
                  <h4>{{ booking.property?.name || "Propiedad" }}</h4>
                  <p class="booking-location">
                    {{ booking.property?.location }}
                  </p>
                  <p class="booking-dates">
                    {{ formatDate(booking.check_in) }} -
                    {{ formatDate(booking.check_out) }}
                  </p>
                </div>
              </div>
              <div class="booking-status">
                <span class="status-badge" :class="booking.status">
                  {{ getStatusText(booking.status) }}
                </span>
                <p class="booking-guests">
                  {{ booking.guests || 1 }} huéspedes
                </p>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <CalendarOffIcon class="empty-icon" />
            <p>No tienes viajes próximos</p>
            <button class="action-button" @click="navigateTo('search')">
              Buscar alojamientos
            </button>
          </div>
        </div>

        <div class="dashboard-card">
          <div class="card-header">
            <h3>Propiedades Favoritas</h3>
            <button class="view-all-button" @click="navigateTo('favorites')">
              Ver todas
              <ChevronRightIcon class="button-icon" />
            </button>
          </div>

          <div v-if="favoriteSummaries.length > 0" class="favorites-list">
            <div
              v-for="property in favoriteSummaries.slice(0, 3)"
              :key="property.id"
              class="favorite-item"
              @click="viewProperty(property.id)"
            >
              <div class="favorite-info">
                <img
                  :src="property.image || '/img/placeholder.jpg'"
                  alt="Property"
                  class="favorite-thumbnail"
                  @error="handleImageError"
                />
                <div>
                  <h4>{{ property.name }}</h4>
                  <p class="favorite-location">{{ property.location }}</p>
                  <p class="favorite-price">{{ property.price }}€/noche</p>
                </div>
              </div>
              <div class="favorite-actions">
                <button
                  @click.stop="toggleFavorite(property.id)"
                  class="favorite-button"
                  :class="{ active: isFavorite(property.id) }"
                >
                  <HeartIcon
                    class="heart-icon"
                    :class="{ filled: isFavorite(property.id) }"
                  />
                </button>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <HeartIcon class="empty-icon" />
            <p>No tienes propiedades favoritas</p>
            <button class="action-button" @click="navigateTo('search')">
              Explorar propiedades
            </button>
          </div>
        </div>
      </div>

      <div class="dashboard-column">
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Mensajes Recientes</h3>
            <button class="view-all-button" @click="navigateTo('messages')">
              Ver todos
              <ChevronRightIcon class="button-icon" />
            </button>
          </div>

          <div
            v-if="Array.isArray(conversations) && conversations.length > 0"
            class="messages-list"
          >
            <div
              v-for="conversation in conversations.slice(0, 5)"
              :key="conversation.id"
              class="message-item"
              :class="{ unread: conversation.unread }"
              @click="openConversation(conversation.id)"
            >
              <div class="message-sender">
                <UserIcon v-if="!conversation.avatar" class="message-icon" />
                <img
                  v-else
                  :src="conversation.avatar"
                  alt="Avatar"
                  class="message-avatar"
                />
                <div>
                  <h4 class="sender-name">
                    {{ conversation.ownerName || conversation.name }}
                  </h4>
                  <p class="message-property">
                    {{ conversation.property?.name || "Propiedad" }}
                  </p>
                </div>
              </div>
              <p class="message-preview">
                {{ conversation.lastMessage?.text?.substring(0, 60) || "" }}
                <span
                  v-if="
                    conversation.lastMessage?.text &&
                    conversation.lastMessage.text.length > 60
                  "
                  >...</span
                >
              </p>
              <p class="message-time">
                {{ formatDateTime(conversation.lastMessage?.timestamp) }}
              </p>
            </div>
          </div>

          <div v-else class="empty-state">
            <MailIcon class="empty-icon" />
            <p>No hay mensajes nuevos</p>
          </div>
        </div>

        <div class="dashboard-card">
          <div class="card-header">
            <h3>Historial Reciente</h3>
          </div>

          <div
            v-if="recentBookings && recentBookings.length > 0"
            class="history-list"
          >
            <div
              v-for="booking in recentBookings.slice(0, 4)"
              :key="booking.id"
              class="history-item"
            >
              <div class="history-property">
                <img
                  :src="booking.property?.image || '/img/placeholder.jpg'"
                  class="history-image"
                  @error="handleImageError"
                />
                <div>
                  <h4>{{ booking.property?.name }}</h4>
                  <p class="history-location">
                    {{ booking.property?.location }}
                  </p>
                  <p class="history-dates">
                    {{ formatDate(booking.check_in) }} -
                    {{ formatDate(booking.check_out) }}
                  </p>
                </div>
              </div>
              <div class="history-actions">
                <button
                  class="review-button"
                  v-if="!booking.reviewed"
                  @click="openReviewModal(booking)"
                >
                  <StarIcon class="star-icon" />
                  Reseñar
                </button>
                <button
                  class="rebook-button"
                  @click="rebookProperty(booking.property?.id)"
                >
                  <RefreshCwIcon class="refresh-icon" />
                  Reservar de nuevo
                </button>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <ClockIcon class="empty-icon" />
            <p>No hay historial reciente</p>
          </div>
        </div>

        <div class="dashboard-card quick-actions">
          <div class="card-header">
            <h3>Acciones Rápidas</h3>
          </div>

          <div class="actions-grid">
            <button class="action-button" @click="navigateTo('search')">
              <SearchIcon class="action-icon" />
              <span>Buscar Alojamiento</span>
            </button>

            <button class="action-button" @click="navigateTo('bookings')">
              <CalendarIcon class="action-icon" />
              <span>Mis Reservas</span>
            </button>

            <button class="action-button" @click="navigateTo('favorites')">
              <HeartIcon class="action-icon" />
              <span>Mis Favoritos</span>
            </button>

            <button class="action-button" @click="navigateTo('profile')">
              <UserIcon class="action-icon" />
              <span>Mi Perfil</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Reseña -->
    <div v-if="showReviewModal" class="modal-overlay" @click="closeReviewModal">
      <div class="modal-content review-modal" @click.stop>
        <div class="modal-header">
          <h3>Escribir Reseña</h3>
          <button class="close-button" @click="closeReviewModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <div class="review-property-info">
            <img
              :src="selectedBooking?.property?.image || '/img/placeholder.jpg'"
              class="review-property-image"
              @error="handleImageError"
            />
            <div>
              <h4>{{ selectedBooking?.property?.name }}</h4>
              <p class="review-property-dates">
                {{ formatDate(selectedBooking?.check_in) }} -
                {{ formatDate(selectedBooking?.check_out) }}
              </p>
            </div>
          </div>

          <div class="review-rating">
            <p class="rating-label">¿Cómo valorarías tu estancia?</p>
            <div class="rating-stars">
              <button
                v-for="star in 5"
                :key="star"
                @click="reviewRating = star"
                class="star-button"
              >
                <StarIcon
                  class="star-icon"
                  :class="{ filled: star <= reviewRating }"
                />
              </button>
            </div>
          </div>

          <div class="review-comment">
            <label for="review-comment">Comentarios (opcional)</label>
            <textarea
              id="review-comment"
              v-model="reviewComment"
              placeholder="Comparte tu experiencia con otros huéspedes..."
              rows="4"
              class="review-textarea"
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="cancel-button" @click="closeReviewModal">
            Cancelar
          </button>
          <button
            class="submit-button"
            :disabled="reviewRating === 0"
            @click="submitReview"
          >
            Enviar Reseña
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import {
  CalendarIcon,
  MapPinIcon,
  HeartIcon,
  EuroIcon,
  UserIcon,
  CalendarOffIcon,
  MailIcon,
  SearchIcon,
  StarIcon,
  RefreshCwIcon,
  ClockIcon,
  ChevronRightIcon,
  XIcon,
} from "lucide-vue-next";
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import { getItem } from "@/helpers/storage";
import { apiHeaders } from "@/../utils/api";
import { useFavorites } from "@/helpers/favoriteManager";

// Usar el composable de favoritos
const { favoriteIds, isFavorite, toggleFavorite, reloadFavorites } =
  useFavorites();

const { favoriteSummaries } = useFavorites();
const router = useRouter();
const conversations = ref([]);
const favoriteProperties = ref([]);
const userSummary = ref(null);
const loadingUser = ref(true);
const userError = ref(null);
const bookings = ref([]);

// Estado para el modal de reseñas
const showReviewModal = ref(false);
const selectedBooking = ref(null);
const reviewRating = ref(0);
const reviewComment = ref("");

// Computed properties
const upcomingBookings = computed(() => {
  if (!bookings.value || bookings.value.length === 0) return [];

  const now = new Date();
  return bookings.value
    .filter((booking) => {
      // Usar la estructura correcta de datos de las reservas
      const checkInDate = booking.details?.check_in || booking.checkIn;
      if (!checkInDate) return false;

      const checkIn = new Date(checkInDate);
      const status = booking.details?.status || booking.status;

      // Solo mostrar reservas confirmadas o pendientes que sean futuras
      return checkIn >= now && (status === "confirmed" || status === "pending");
    })
    .sort((a, b) => {
      const dateA = new Date(a.details?.check_in || a.checkIn);
      const dateB = new Date(b.details?.check_in || b.checkIn);
      return dateA - dateB;
    })
    .map((booking) => ({
      ...booking,
      // Normalizar la estructura de datos
      check_in: booking.details?.check_in || booking.checkIn,
      check_out: booking.details?.check_out || booking.checkOut,
      guests: booking.details?.guests || booking.guests || 1,
      status: booking.details?.status || booking.status,
    }));
});

const recentBookings = computed(() => {
  if (!bookings.value || bookings.value.length === 0) return [];

  const now = new Date();
  return bookings.value
    .filter((booking) => {
      const checkOutDate = booking.details?.check_out || booking.checkOut;
      if (!checkOutDate) return false;

      const checkOut = new Date(checkOutDate);
      const status = booking.details?.status || booking.status;

      // Solo mostrar reservas completadas que sean del pasado
      return (
        checkOut < now && (status === "completed" || status === "confirmed")
      );
    })
    .sort((a, b) => {
      const dateA = new Date(a.details?.check_out || a.checkOut);
      const dateB = new Date(b.details?.check_out || b.checkOut);
      return dateB - dateA; // Más recientes primero
    })
    .map((booking) => ({
      ...booking,
      // Normalizar la estructura de datos
      check_in: booking.details?.check_in || booking.checkIn,
      check_out: booking.details?.check_out || booking.checkOut,
      guests: booking.details?.guests || booking.guests || 1,
      status: booking.details?.status || booking.status,
      reviewed: booking.reviewed || false,
    }));
});

const computedUserSummary = computed(() => {
  const summary = userSummary.value || {};
  return {
    name: summary.name || "Usuario",
    totalBookings:
      typeof summary.totalBookings === "number"
        ? summary.totalBookings
        : bookings.value?.length || 0,
    totalSpent:
      typeof summary.totalSpent === "number"
        ? summary.totalSpent
        : bookings.value?.reduce((sum, booking) => {
            const price =
              booking.details?.total_price || booking.totalPrice || 0;
            return sum + parseFloat(price);
          }, 0) || 0,
    citiesVisited:
      typeof summary.citiesVisited === "number"
        ? summary.citiesVisited
        : new Set(
            bookings.value
              ?.filter(
                (b) =>
                  b.details?.status === "completed" || b.status === "completed"
              )
              ?.map((b) => b.property?.location)
              ?.filter(Boolean)
          ).size || 0,
  };
});

const loadFavoriteProperties = async () => {
  console.log("=== CARGANDO PROPIEDADES FAVORITAS ===");
  console.log("IDs de favoritos actuales:", favoriteIds.value);
  console.log("Número de favoritos:", favoriteIds.value.length);

  if (favoriteIds.value.length === 0) {
    console.log("No hay favoritos para cargar");
    favoriteProperties.value = [];
    return;
  }

  try {
    // Cargar todas las propiedades
    const response = await axios.get(
      "http://localhost:8000/api/properties",
      apiHeaders()
    );
    console.log("Propiedades obtenidas de la API:", response.data.length);

    // Filtrar solo las que están en favoritos
    const filtered = response.data.filter((property) => {
      const isFav = favoriteIds.value.includes(property.id);
      if (isFav) {
        console.log(
          `Propiedad ${property.id} (${property.name}) está en favoritos`
        );
      }
      return isFav;
    });

    console.log("Propiedades filtradas por favoritos:", filtered.length);

    // Normalizar las propiedades
    favoriteProperties.value = filtered.map((property) => ({
      id: property.id,
      name: property.name || "Sin nombre",
      location: property.location || "Ubicación no disponible",
      price: property.price || 0,
      image: property.image || "/img/placeholder.jpg",
      bedrooms: property.bedrooms || 1,
      bathrooms: property.bathrooms || 1,
      maxGuests: property.capacity || 2,
    }));

    console.log("Propiedades favoritas finales:", favoriteProperties.value);
  } catch (error) {
    console.error("Error cargando propiedades favoritas:", error);
    favoriteProperties.value = [];
  }
};

// Observar cambios en favoriteIds para recargar propiedades
watch(
  favoriteIds,
  () => {
    loadFavoriteProperties();
  },
  { deep: true }
);

// Asegurémonos de que se carguen los favoritos al montar el componente
onMounted(async () => {
  // Recargar favoritos explícitamente
  reloadFavorites();

  loadingUser.value = true;
  userError.value = null;

  try {
    // Cargar favoritos primero para asegurar que están disponibles
    await loadFavoriteProperties();

    const token = getItem("auth_token", true) || getItem("auth_token");
    const headers = {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    };

    // Cargar resumen del usuario
    try {
      const userRes = await axios.get(
        "http://localhost:8000/api/user/renter-summary",
        {
          headers,
        }
      );
      userSummary.value = userRes.data;
      console.log("Resumen usuario (inquilino):", userSummary.value);
    } catch (err) {
      console.log(
        "No se pudo cargar el resumen del usuario, usando datos por defecto"
      );
      userSummary.value = { name: "Usuario" };
    }

    // Cargar reservas del inquilino
    const bookingsRes = await axios.get("http://localhost:8000/api/bookings", {
      headers,
    });
    bookings.value = bookingsRes.data.map((booking) => ({
      ...booking,
      check_in:
        booking.details?.check_in ||
        booking.check_in ||
        booking.reservation_date,
      check_out:
        booking.details?.check_out ||
        booking.check_out ||
        booking.reservation_date,
      guests: booking.details?.guests || booking.guests || 1,
      status: booking.details?.status || booking.status || "confirmed",
      property: booking.property || {
        name: "Propiedad",
        location: "",
        image: "/img/placeholder.jpg",
      },
    }));
    console.log("Reservas inquilino:", bookings.value);

    // Cargar propiedades favoritas
    //await loadFavoriteProperties();

    // Cargar conversaciones
    try {
      const conversationsRes = await axios.get(
        "http://localhost:8000/api/conversations",
        {
          headers,
        }
      );
      conversations.value = Array.isArray(conversationsRes.data)
        ? conversationsRes.data
        : [];
      console.log("Conversaciones:", conversations.value);
    } catch (err) {
      console.log("No se pudieron cargar las conversaciones");
      conversations.value = [];
    }
  } catch (err) {
    console.error("ERROR cargando dashboard inquilino:", err);
    userError.value = err.response?.data?.message || err.message;
  } finally {
    loadingUser.value = false;
  }
});

// Methods
const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  try {
    return new Date(dateStr).toLocaleDateString("es-ES", {
      day: "numeric",
      month: "short",
      year: "numeric",
    });
  } catch (e) {
    return "-";
  }
};

const formatDateTime = (dateStr) => {
  if (!dateStr) return "-";
  try {
    return new Date(dateStr).toLocaleString("es-ES", {
      day: "numeric",
      month: "short",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch (e) {
    return "-";
  }
};

const navigateTo = (route) => {
  if (route === "search") {
    router.push("/renters/search");
  } else {
    router.push(`/renters/${route}`);
  }
};

const getStatusText = (status) => {
  const statusMap = {
    confirmed: "Confirmada",
    pending: "Pendiente",
    cancelled: "Cancelada",
    completed: "Completada",
  };
  return statusMap[status] || status;
};

const viewProperty = (propertyId) => {
  router.push(`/renters/property/${propertyId}`);
};

const openConversation = (conversationId) => {
  router.push(`/renters/messages/${conversationId}`);
};

const rebookProperty = (propertyId) => {
  if (propertyId) {
    router.push(`/property/${propertyId}?rebook=true`);
  }
};

const handleImageError = (event) => {
  event.target.src = "/img/placeholder.jpg";
};

// Funciones para el modal de reseñas
const openReviewModal = (booking) => {
  selectedBooking.value = booking;
  reviewRating.value = 0;
  reviewComment.value = "";
  showReviewModal.value = true;
  document.body.style.overflow = "hidden";
};

const closeReviewModal = () => {
  showReviewModal.value = false;
  selectedBooking.value = null;
  document.body.style.overflow = "auto";
};

const submitReview = async () => {
  if (!selectedBooking.value || reviewRating.value === 0) return;

  try {
    // Aquí implementarías la llamada a la API para guardar la reseña
    console.log("Enviando reseña:", {
      bookingId: selectedBooking.value.id,
      propertyId: selectedBooking.value.property?.id,
      rating: reviewRating.value,
      comment: reviewComment.value,
    });

    // Simulamos una llamada exitosa
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Marcar la reserva como reseñada
    const index = bookings.value.findIndex(
      (b) => b.id === selectedBooking.value.id
    );
    if (index !== -1) {
      bookings.value[index].reviewed = true;
    }

    // Cerrar el modal
    closeReviewModal();

    // Mostrar mensaje de éxito
    alert("¡Gracias por tu reseña!");
  } catch (error) {
    console.error("Error al enviar la reseña:", error);
    alert("No se pudo enviar la reseña. Por favor, inténtalo de nuevo.");
  }
};

const clearAllFavorites = () => {
  console.log("Limpiando todos los favoritos");
  favoriteIds.value = [];
  localStorage.removeItem("favorite-properties");
};
</script>

<style scoped>
.dashboard-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 60vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
  position: relative;
  overflow: hidden;
}

.dashboard-section::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(
      circle at 20% 150%,
      rgba(0, 113, 194, 0.05) 0%,
      transparent 50%
    ),
    radial-gradient(
      circle at 80% -50%,
      rgba(0, 53, 128, 0.03) 0%,
      transparent 60%
    );
  z-index: 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  position: relative;
  z-index: 1;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0;
  position: relative;
}

.section-title::after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.welcome-message {
  text-align: right;
}

.welcome-message p {
  margin: 0;
  color: #64748b;
}

.user-name {
  color: #0071c2;
  font-weight: 600;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  position: relative;
  z-index: 1;
}

.stat-card {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  position: relative;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 53, 128, 0.1);
}

.stat-icon-wrapper {
  position: relative;
  width: 60px;
  height: 60px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon {
  width: 30px;
  height: 30px;
  color: #0071c2;
  position: relative;
  z-index: 1;
}

.stat-icon-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(
    circle,
    rgba(0, 113, 194, 0.2) 0%,
    rgba(0, 113, 194, 0) 70%
  );
  border-radius: 12px;
  opacity: 0;
  transition: opacity 0.3s, transform 0.3s;
}

.stat-card:hover .stat-icon-glow {
  opacity: 1;
  transform: scale(1.2);
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #003580;
  margin: 0 0 0.25rem;
}

.stat-title {
  font-size: 1rem;
  color: #64748b;
  margin: 0;
}

/* Dashboard Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 1.5rem;
  position: relative;
  z-index: 1;
}

.dashboard-column {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.dashboard-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.dashboard-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 53, 128, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.card-header h3 {
  font-size: 1.2rem;
  color: #003580;
  margin: 0;
}

.view-all-button {
  background: none;
  border: none;
  color: #0071c2;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  transition: color 0.3s ease;
}

.view-all-button:hover {
  color: #003580;
}

.button-icon {
  width: 16px;
  height: 16px;
}

/* Upcoming Bookings */
.upcoming-bookings {
  padding: 1.5rem;
}

.booking-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e6f0ff;
}

.booking-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
}

.booking-property {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.booking-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
}

.booking-property h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  color: #003580;
}

.booking-location {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0 0 0.25rem;
}

.booking-dates {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}

.booking-status {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.5rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-badge.confirmed {
  background-color: #e6f7ee;
  color: #00703c;
}

.status-badge.pending {
  background-color: #fffbeb;
  color: #b45309;
}

.status-badge.cancelled {
  background-color: #fff2f0;
  color: #e41c00;
}

.booking-guests {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

/* Favorites List */
.favorites-list {
  padding: 1.5rem;
}

.favorite-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e6f0ff;
  cursor: pointer;
  transition: background-color 0.2s;
}

.favorite-item:hover {
  background-color: #f8fafc;
  margin: 0 -1.5rem 1rem;
  padding: 1rem 1.5rem;
  border-radius: 8px;
}

.favorite-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
}

.favorite-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.favorite-thumbnail {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
}

.favorite-info h4 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  color: #003580;
}

.favorite-location {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0 0 0.25rem;
}

.favorite-price {
  font-size: 0.9rem;
  font-weight: 600;
  color: #0071c2;
  margin: 0;
}

.favorite-actions {
  display: flex;
  gap: 0.5rem;
}

.favorite-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.favorite-button:hover {
  background-color: #f1f5f9;
}

.favorite-button.active .heart-icon {
  color: #ef4444;
  fill: currentColor;
}

.heart-icon {
  width: 20px;
  height: 20px;
  color: #e2e8f0;
}

.heart-icon.filled {
  color: #ef4444;
  fill: currentColor;
}

/* Messages List */
.messages-list {
  padding: 0;
}

.message-item {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e6f0ff;
  cursor: pointer;
  transition: background-color 0.2s ease;
  position: relative;
}

.message-item:hover {
  background-color: #f8fafc;
}

.message-item:last-child {
  border-bottom: none;
}

.message-item.unread {
  background-color: #f0f7ff;
  border-left: 3px solid #0071c2;
}

.message-sender {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
}

.message-icon {
  width: 40px;
  height: 40px;
  padding: 8px;
  border-radius: 50%;
  background-color: #e6f0ff;
  color: #0071c2;
  flex-shrink: 0;
}

.message-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e6f0ff;
  flex-shrink: 0;
}

.sender-name {
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0 0 0.25rem;
  color: #1e293b;
}

.message-property {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

.message-preview {
  font-size: 0.9rem;
  color: #475569;
  margin: 0 0 0.5rem;
  line-height: 1.4;
}

.message-time {
  font-size: 0.8rem;
  color: #94a3b8;
  margin: 0;
  text-align: right;
}

/* History List */
.history-list {
  padding: 1.5rem;
}

.history-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e6f0ff;
}

.history-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
}

.history-property {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.history-image {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  object-fit: cover;
}

.history-property h4 {
  font-size: 0.95rem;
  margin: 0 0 0.25rem;
  color: #003580;
}

.history-location {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0 0 0.25rem;
}

.history-dates {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

.history-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.review-button,
.rebook-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #e6f0ff;
  background: white;
  border-radius: 6px;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.2s;
}

.review-button:hover {
  background-color: #fffbeb;
  border-color: #fbbf24;
}

.rebook-button:hover {
  background-color: #f0f7ff;
  border-color: #0071c2;
}

.star-icon,
.refresh-icon {
  width: 14px;
  height: 14px;
}

/* Quick Actions */
.quick-actions .actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  padding: 1.5rem;
}

.action-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
  border: 1px solid #e6f0ff;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.1);
  background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
}

.action-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
}

.action-button span {
  font-size: 0.9rem;
  font-weight: 500;
  color: #1e293b;
  text-align: center;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1.5rem;
  text-align: center;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #ccc;
  margin-bottom: 1rem;
}

.empty-state p {
  color: #64748b;
  margin-bottom: 1.5rem;
}

.empty-state .action-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  flex-direction: row;
}

.empty-state .action-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.2);
}

/* Modal de Reseña */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background-color: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.review-modal {
  width: 90%;
  max-width: 500px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 6px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
}

.close-button:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 1.5rem;
}

.review-property-info {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.review-property-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  object-fit: cover;
}

.review-property-info h4 {
  font-size: 1.1rem;
  margin: 0 0 0.5rem;
  color: #1f2937;
}

.review-property-dates {
  font-size: 0.9rem;
  color: #6b7280;
}

.review-rating {
  margin-bottom: 1.5rem;
}

.rating-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.rating-stars {
  display: flex;
  gap: 0.5rem;
}

.star-button {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  transition: transform 0.2s;
}

.star-button:hover {
  transform: scale(1.2);
}

.star-button .star-icon {
  width: 32px;
  height: 32px;
  color: #d1d5db;
}

.star-button .star-icon.filled {
  color: #fbbf24;
  fill: currentColor;
}

.review-comment {
  margin-bottom: 1rem;
}

.review-comment label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #1f2937;
}

.review-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  resize: vertical;
  font-family: inherit;
  font-size: 0.9rem;
}

.review-textarea:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.cancel-button {
  padding: 0.5rem 1rem;
  background-color: white;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  color: #4b5563;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-button:hover {
  background-color: #f3f4f6;
}

.submit-button {
  padding: 0.5rem 1rem;
  background-color: #0071c2;
  border: none;
  border-radius: 6px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.submit-button:hover:not(:disabled) {
  background-color: #005a9c;
}

.submit-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 1200px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .dashboard-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }

  .booking-item,
  .favorite-item,
  .history-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .booking-status,
  .favorite-actions,
  .history-actions {
    align-self: stretch;
    flex-direction: row;
    justify-content: space-between;
  }

  .review-modal {
    width: 95%;
  }

  .review-property-info {
    flex-direction: column;
  }
}
</style>
