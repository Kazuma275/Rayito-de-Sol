<template>
  <div class="booking-modal-overlay" @click="$emit('close')">
    <div class="booking-modal-content" @click.stop>
      <!-- Header -->
      <div class="booking-modal-header">
        <h2>Reservar Propiedad</h2>
        <button class="close-button" @click="$emit('close')">
          <XIcon class="close-icon" />
        </button>
      </div>

      <!-- Content -->
      <div class="booking-modal-body">
        <!-- Property Summary -->
        <div class="property-summary">
          <div class="property-image">
            <img
              :src="property?.image || '/img/placeholder.jpg'"
              :alt="property?.name"
              @error="handleImageError"
            />
          </div>
          <div class="property-info">
            <h3>{{ property?.name }}</h3>
            <div class="property-location">
              <MapPinIcon class="location-icon" />
              <span>{{ property?.location }}</span>
            </div>
            <div class="property-price">
              <span class="price-amount"
                >€{{ property?.pricePerNight || property?.price }}</span
              >
              <span class="price-period">/ noche</span>
            </div>
          </div>
        </div>

        <!-- Booking Form -->
        <form @submit.prevent="handleSubmit" class="booking-form">
          <!-- Date Selection -->
          <div class="form-section">
            <h4>Fechas de estancia</h4>
            <div class="date-inputs">
              <div class="date-input-group">
                <label for="checkin">Check-in</label>
                <input
                  id="checkin"
                  type="date"
                  v-model="bookingData.checkinDate"
                  :min="minDate"
                  required
                  class="date-input"
                />
              </div>
              <div class="date-input-group">
                <label for="checkout">Check-out</label>
                <input
                  id="checkout"
                  type="date"
                  v-model="bookingData.checkoutDate"
                  :min="minCheckoutDate"
                  required
                  class="date-input"
                />
              </div>
            </div>
          </div>

          <!-- Guests -->
          <div class="form-section">
            <h4>Huéspedes</h4>
            <div class="guests-selector">
              <label for="guests">Número de huéspedes</label>
              <select
                id="guests"
                v-model="bookingData.guests"
                required
                class="guests-select"
              >
                <option
                  v-for="num in property?.maxGuests || 4"
                  :key="num"
                  :value="num"
                >
                  {{ num }} {{ num === 1 ? "huésped" : "huéspedes" }}
                </option>
              </select>
            </div>
          </div>

          <!-- Guest Information -->
          <div class="form-section">
            <h4>Información del huésped principal</h4>
            <div class="guest-info">
              <div class="input-row">
                <div class="input-group">
                  <label for="firstName">Nombre</label>
                  <input
                    id="firstName"
                    type="text"
                    v-model="bookingData.guestInfo.firstName"
                    required
                    class="text-input"
                  />
                </div>
                <div class="input-group">
                  <label for="lastName">Apellidos</label>
                  <input
                    id="lastName"
                    type="text"
                    v-model="bookingData.guestInfo.lastName"
                    required
                    class="text-input"
                  />
                </div>
              </div>
              <div class="input-row">
                <div class="input-group">
                  <label for="email">Email</label>
                  <input
                    id="email"
                    type="email"
                    v-model="bookingData.guestInfo.email"
                    required
                    class="text-input"
                  />
                </div>
                <div class="input-group">
                  <label for="phone">Teléfono</label>
                  <input
                    id="phone"
                    type="tel"
                    v-model="bookingData.guestInfo.phone"
                    required
                    class="text-input"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Price Breakdown -->
          <div class="price-breakdown" v-if="totalNights > 0">
            <h4>Resumen de precios</h4>
            <div class="price-details">
              <div class="price-line">
                <span
                  >€{{ property?.pricePerNight || property?.price }} ×
                  {{ totalNights }}
                  {{ totalNights === 1 ? "noche" : "noches" }}</span
                >
                <span>€{{ subtotal }}</span>
              </div>
              <div class="price-line">
                <span>Tasas de servicio</span>
                <span>€{{ serviceFee }}</span>
              </div>
              <div class="price-line">
                <span>Impuestos</span>
                <span>€{{ taxes }}</span>
              </div>
              <div class="price-line total">
                <span>Total</span>
                <span>€{{ totalPrice }}</span>
              </div>
            </div>
          </div>

          <!-- Payment Section -->
          <div class="form-section" v-if="totalNights > 0">
            <h4>Información de pago</h4>
            <div class="payment-section">
              <!-- Loading state for Stripe -->
              <div v-if="!stripeReady" class="stripe-loading">
                <div class="loading-spinner"></div>
                <span>Cargando sistema de pagos...</span>
              </div>

              <!-- Stripe Elements will be mounted here -->
              <div
                v-show="stripeReady"
                ref="cardElementContainer"
                class="card-element-container"
              >
                <!-- Stripe Elements will create form elements here -->
              </div>

              <!-- Error display -->
              <div v-if="cardError" class="card-errors">
                {{ cardError }}
              </div>
            </div>
          </div>

          <!-- Terms and Conditions -->
          <div class="form-section">
            <div class="terms-checkbox">
              <input
                id="terms"
                type="checkbox"
                v-model="bookingData.acceptTerms"
                required
                class="checkbox-input"
              />
              <label for="terms">
                Acepto los
                <a href="#" @click.prevent="showTerms = true"
                  >términos y condiciones</a
                >
                y la
                <a href="#" @click.prevent="showPrivacy = true"
                  >política de privacidad</a
                >
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="form-actions">
            <button
              type="button"
              @click="$emit('close')"
              class="cancel-button"
              :disabled="isProcessing"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="confirm-booking-button"
              :disabled="!canSubmit || isProcessing"
            >
              <div v-if="isProcessing" class="processing-spinner"></div>
              {{
                isProcessing
                  ? "Procesando..."
                  : `Confirmar reserva (€${totalPrice})`
              }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Terms Modal -->
    <div
      v-if="showTerms"
      class="terms-modal-overlay"
      @click="showTerms = false"
    >
      <div class="terms-modal-content" @click.stop>
        <div class="terms-header">
          <h3>Términos y Condiciones</h3>
          <button @click="showTerms = false" class="close-button">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="terms-body">
          <p>Al realizar esta reserva, aceptas los siguientes términos:</p>
          <ul>
            <li>La reserva está sujeta a disponibilidad</li>
            <li>El pago se procesará inmediatamente</li>
            <li>
              Las cancelaciones deben realizarse con 24 horas de antelación
            </li>
            <li>Se requiere identificación válida en el check-in</li>
            <li>No se permiten fiestas o eventos sin autorización previa</li>
          </ul>
        </div>
        <div class="terms-footer">
          <button @click="showTerms = false" class="accept-terms-button">
            Entendido
          </button>
        </div>
      </div>
    </div>

    <!-- Privacy Modal -->
    <div
      v-if="showPrivacy"
      class="terms-modal-overlay"
      @click="showPrivacy = false"
    >
      <div class="terms-modal-content" @click.stop>
        <div class="terms-header">
          <h3>Política de Privacidad</h3>
          <button @click="showPrivacy = false" class="close-button">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="terms-body">
          <p>Tu privacidad es importante para nosotros:</p>
          <ul>
            <li>
              Tus datos personales se utilizan únicamente para procesar la
              reserva
            </li>
            <li>
              No compartimos tu información con terceros sin tu consentimiento
            </li>
            <li>Los datos de pago son procesados de forma segura por Stripe</li>
            <li>
              Puedes solicitar la eliminación de tus datos en cualquier momento
            </li>
          </ul>
        </div>
        <div class="terms-footer">
          <button @click="showPrivacy = false" class="accept-terms-button">
            Entendido
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from "vue";
import { XIcon, MapPinIcon } from "lucide-vue-next";
import axios from "axios";
import { apiHeaders } from "@/../utils/api";

// Props
const props = defineProps({
  property: {
    type: Object,
    required: true,
  },
});

// Emits
const emit = defineEmits(["close", "booking-success"]);

// Template refs
const cardElementContainer = ref(null);

// Reactive state
const bookingData = ref({
  checkinDate: "",
  checkoutDate: "",
  guests: 1,
  guestInfo: {
    firstName: "",
    lastName: "",
    email: "",
    phone: "",
  },
  acceptTerms: false,
});

const isProcessing = ref(false);
const cardError = ref("");
const showTerms = ref(false);
const showPrivacy = ref(false);
const stripeReady = ref(false);
const stripeLoaded = ref(false);
const initializationAttempts = ref(0);

// Stripe variables
let stripe = null;
let elements = null;
let cardElement = null;

// API Base URL
const API_BASE_URL = "http://localhost:8000/api";

// Computed properties
const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split("T")[0];
});

const minCheckoutDate = computed(() => {
  if (!bookingData.value.checkinDate) return minDate.value;
  const checkin = new Date(bookingData.value.checkinDate);
  checkin.setDate(checkin.getDate() + 1);
  return checkin.toISOString().split("T")[0];
});

const totalNights = computed(() => {
  if (!bookingData.value.checkinDate || !bookingData.value.checkoutDate)
    return 0;

  const checkin = new Date(bookingData.value.checkinDate);
  const checkout = new Date(bookingData.value.checkoutDate);
  const diffTime = checkout - checkin;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return diffDays > 0 ? diffDays : 0;
});

const subtotal = computed(() => {
  return (
    totalNights.value *
    (props.property?.pricePerNight || props.property?.price || 0)
  );
});

const serviceFee = computed(() => {
  return Math.round(subtotal.value * 0.1); // 10% service fee
});

const taxes = computed(() => {
  return Math.round(subtotal.value * 0.08); // 8% taxes
});

const totalPrice = computed(() => {
  return subtotal.value + serviceFee.value + taxes.value;
});

const canSubmit = computed(() => {
  return (
    bookingData.value.checkinDate &&
    bookingData.value.checkoutDate &&
    bookingData.value.guests > 0 &&
    bookingData.value.guestInfo.firstName &&
    bookingData.value.guestInfo.lastName &&
    bookingData.value.guestInfo.email &&
    bookingData.value.guestInfo.phone &&
    bookingData.value.acceptTerms &&
    totalNights.value > 0 &&
    stripeReady.value &&
    !isProcessing.value
  );
});

// Methods
const handleImageError = (event) => {
  event.target.src = "/img/placeholder.jpg";
};

const loadStripeScript = () => {
  return new Promise((resolve, reject) => {
    if (window.Stripe) {
      stripeLoaded.value = true;
      resolve(window.Stripe);
      return;
    }

    const script = document.createElement("script");
    script.src = "https://js.stripe.com/v3/";
    script.onload = () => {
      stripeLoaded.value = true;
      resolve(window.Stripe);
    };
    script.onerror = () => reject(new Error("Failed to load Stripe script"));
    document.head.appendChild(script);
  });
};

const destroyStripeElement = () => {
  if (cardElement) {
    try {
      console.log("Destroying existing card element");
      cardElement.destroy();
    } catch (error) {
      console.warn("Error destroying card element:", error);
    }
    cardElement = null;
  }
  stripeReady.value = false;
};

const initializeStripe = async () => {
  try {
    console.log(
      "Inicializando Stripe... Intento:",
      initializationAttempts.value + 1
    );
    initializationAttempts.value++;

    // Destroy existing element if any
    destroyStripeElement();

    // Cargar el script de Stripe si no está disponible
    const StripeConstructor = await loadStripeScript();

    if (!StripeConstructor) {
      throw new Error("Stripe constructor not available");
    }

    // Initialize Stripe with your publishable key
    /*     stripe = StripeConstructor('pk_live_51R2DAqGR90uW5lzlX2UNwjYFHQ7gC1nCzdNVTHo6hxlEK8VguEIzO5gcexDRLpCtbIDpaNYrf8OOsAvbnC5SUDYL00QEsl1f1v'); */
    stripe = StripeConstructor(
      "pk_test_51RYruY2fjdRS7dCHgYPmn683UJD1YfJWScjQlmu2bxXLY8qdWM9hzwX2ac5fRvSQP8v0GzazemAP2LoF0oy7qnHg00ZAskG1Xi"
    ); // Test key for development

    if (!stripe) {
      throw new Error("Failed to initialize Stripe");
    }

    console.log("Stripe inicializado correctamente");

    // Esperar a que el DOM esté listo
    await nextTick();

    // Verificar que el contenedor existe
    if (!cardElementContainer.value) {
      throw new Error("Card element container ref not available");
    }

    console.log("Contenedor encontrado, creando elementos...");

    // Create elements
    elements = stripe.elements();

    // Create card element
    cardElement = elements.create("card", {
      style: {
        base: {
          fontSize: "16px",
          color: "#424770",
          fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
          fontSmoothing: "antialiased",
          "::placeholder": {
            color: "#aab7c4",
          },
        },
        invalid: {
          color: "#9e2146",
        },
      },
    });

    console.log("Montando card element en contenedor...");

    // Mount card element directly to the ref
    cardElement.mount(cardElementContainer.value);

    // Listen for real-time validation errors
    cardElement.on("change", (event) => {
      console.log("Card element change:", event);
      cardError.value = event.error ? event.error.message : "";
    });

    // Listen for ready event
    cardElement.on("ready", () => {
      console.log("Card element ready");
      stripeReady.value = true;
      cardError.value = "";
    });

    // Listen for focus event
    cardElement.on("focus", () => {
      console.log("Card element focused");
    });

    // Listen for blur event
    cardElement.on("blur", () => {
      console.log("Card element blurred");
    });

    // Listen for escape event (when element is destroyed)
    cardElement.on("escape", () => {
      console.log("Card element escaped");
      stripeReady.value = false;
    });
  } catch (error) {
    console.error("Error initializing Stripe:", error);
    cardError.value =
      "Error al cargar el sistema de pagos. Por favor, recarga la página.";
    stripeReady.value = false;

    // Retry logic (max 3 attempts)
    if (initializationAttempts.value < 3) {
      console.log("Reintentando inicialización de Stripe en 2 segundos...");
      setTimeout(() => {
        initializeStripe();
      }, 2000);
    }
  }
};

const createPaymentIntent = async () => {
  try {
    console.log("Creando payment intent con datos:", {
      amount: totalPrice.value * 100,
      currency: "eur",
      property_id: props.property.id,
      booking_data: bookingData.value,
    });

    const response = await axios.post(
      `${API_BASE_URL}/create-payment-intent`,
      {
        amount: totalPrice.value * 100,
        currency: "eur",
        property_id: props.property.id,
        booking_data: bookingData.value,
      },
      {
        withCredentials: false,
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      }
    );

    console.log("Payment intent creado:", response.data);
    return response.data.client_secret;
  } catch (error) {
    console.error("Error creating payment intent:", error);

    if (error.response && error.response.data) {
      console.error("Server error details:", error.response.data);
      throw new Error(
        error.response.data.message || "Error al crear la intención de pago"
      );
    } else {
      throw new Error(
        "Error de conexión. Por favor, verifica tu conexión a internet."
      );
    }
  }
};

const validateStripeElement = () => {
  if (!stripe) {
    throw new Error("Stripe no está inicializado");
  }

  if (!cardElement) {
    throw new Error("Card element no está disponible");
  }

  if (!stripeReady.value) {
    throw new Error("Stripe no está listo");
  }

  // Verificar que el elemento sigue montado
  try {
    // Intentar acceder a una propiedad del elemento para verificar que sigue activo
    const elementContainer = cardElementContainer.value;
    if (!elementContainer || !elementContainer.isConnected) {
      throw new Error("Card element container no está conectado al DOM");
    }
  } catch (error) {
    throw new Error("Card element no está montado correctamente");
  }
};

const handleSubmit = async () => {
  if (!canSubmit.value) {
    console.log("Cannot submit - validation failed");
    return;
  }

  console.log("Starting payment process...");
  isProcessing.value = true;
  cardError.value = "";

  try {
    // Validar que Stripe esté correctamente inicializado
    validateStripeElement();

    console.log("Stripe validation passed, creating payment intent...");

    // Create payment intent
    const clientSecret = await createPaymentIntent();

    if (!clientSecret) {
      throw new Error("No se pudo obtener el client secret");
    }

    console.log("Payment intent created, confirming payment...");

    // Validar nuevamente antes de confirmar el pago
    validateStripeElement();

    // Confirm payment with Stripe
    const { error, paymentIntent } = await stripe.confirmCardPayment(
      clientSecret,
      {
        payment_method: {
          card: cardElement,
          billing_details: {
            name: `${bookingData.value.guestInfo.firstName} ${bookingData.value.guestInfo.lastName}`,
            email: bookingData.value.guestInfo.email,
            phone: bookingData.value.guestInfo.phone,
          },
        },
      }
    );

    if (error) {
      console.error("Stripe payment error:", error);
      cardError.value = error.message;
      return;
    }

    console.log("Payment confirmed:", paymentIntent);

    if (paymentIntent.status === "succeeded") {
      console.log("Payment succeeded, creating booking...");

      // Create booking record
      const bookingResponse = await axios.post(
        `${API_BASE_URL}/bookings`,
        {
          property_id: props.property.id,
          payment_intent_id: paymentIntent.id,
          checkin_date: bookingData.value.checkinDate,
          checkout_date: bookingData.value.checkoutDate,
          guests: bookingData.value.guests,
          guest_name: `${bookingData.value.guestInfo.firstName} ${bookingData.value.guestInfo.lastName}`,
          guest_email: bookingData.value.guestInfo.email,
          guest_phone: bookingData.value.guestInfo.phone,
          total_price: totalPrice.value,
          total_nights: totalNights.value,
          subtotal: subtotal.value,
          service_fee: serviceFee.value,
          taxes: taxes.value,
        },
        apiHeaders() // Aquí se incluyen las cabeceras de autorización
      );

      const booking = bookingResponse.data;
      console.log("Booking created:", booking);

      // Emit success event
      emit("booking-success", {
        bookingId: booking.id,
        paymentIntentId: paymentIntent.id,
        totalPrice: totalPrice.value,
      });
    }
  } catch (error) {
    console.error("Error processing payment:", error);
    cardError.value =
      error.message ||
      "Error procesando el pago. Por favor, inténtalo de nuevo.";

    // Si el error es relacionado con el elemento, intentar reinicializar
    if (
      error.message.includes("Element") ||
      error.message.includes("mounted")
    ) {
      console.log("Reintentando inicialización debido a error de elemento...");
      setTimeout(() => {
        initializeStripe();
      }, 1000);
    }
  } finally {
    isProcessing.value = false;
  }
};

// Watch for checkout date changes to ensure it's after checkin
watch(
  () => bookingData.value.checkinDate,
  (newCheckin) => {
    if (newCheckin && bookingData.value.checkoutDate) {
      const checkin = new Date(newCheckin);
      const checkout = new Date(bookingData.value.checkoutDate);

      if (checkout <= checkin) {
        const newCheckout = new Date(checkin);
        newCheckout.setDate(newCheckout.getDate() + 1);
        bookingData.value.checkoutDate = newCheckout
          .toISOString()
          .split("T")[0];
      }
    }
  }
);

// Watch for when we have total nights > 0 to initialize Stripe
watch(
  () => totalNights.value,
  async (newValue) => {
    if (newValue > 0 && stripeLoaded.value && !stripeReady.value) {
      console.log("Total nights > 0, initializing Stripe...");
      await initializeStripe();
    }
  }
);

// Lifecycle
onMounted(async () => {
  console.log("BookingModal mounted");
  document.body.style.overflow = "hidden";

  // Load Stripe script first
  try {
    await loadStripeScript();
    console.log("Stripe script loaded");

    // If we already have total nights > 0, initialize immediately
    if (totalNights.value > 0) {
      await initializeStripe();
    }
  } catch (error) {
    console.error("Error loading Stripe script:", error);
    cardError.value =
      "Error al cargar el sistema de pagos. Por favor, recarga la página.";
  }
});

onUnmounted(() => {
  console.log("BookingModal unmounted");
  document.body.style.overflow = "auto";

  // Cleanup Stripe elements
  destroyStripeElement();

  stripe = null;
  elements = null;
});
</script>

<style scoped>
.booking-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 53, 128, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1500;
  backdrop-filter: blur(4px);
}

.booking-modal-content {
  background-color: white;
  border-radius: 16px;
  width: 95%;
  max-width: 800px;
  max-height: 95vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.booking-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e9ecef;
  position: sticky;
  top: 0;
  background-color: white;
  z-index: 10;
}

.booking-modal-header h2 {
  font-size: 1.5rem;
  margin: 0;
  font-weight: 600;
  color: #1a1a1a;
}

.close-button {
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.3s;
}

.close-button:hover {
  background-color: #f8f9fa;
}

.close-icon {
  width: 20px;
  height: 20px;
}

.booking-modal-body {
  padding: 2rem;
}

/* Property Summary */
.property-summary {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-radius: 12px;
  margin-bottom: 2rem;
}

.property-image {
  width: 120px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.property-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.property-info h3 {
  font-size: 1.2rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 0.5rem 0;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.location-icon {
  width: 14px;
  height: 14px;
}

.property-price {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
}

.price-amount {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0071c2;
}

.price-period {
  font-size: 0.875rem;
  color: #6c757d;
}

/* Form Sections */
.booking-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section h4 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 1rem 0;
}

/* Date Inputs */
.date-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.date-input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.date-input-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #495057;
}

.date-input {
  padding: 0.75rem;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.date-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

/* Guests Selector */
.guests-selector {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.guests-selector label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #495057;
}

.guests-select {
  padding: 0.75rem;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  background-color: white;
  cursor: pointer;
  transition: all 0.3s ease;
}

.guests-select:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

/* Guest Information */
.guest-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #495057;
}

.text-input {
  padding: 0.75rem;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.text-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 2px rgba(0, 113, 194, 0.1);
}

/* Price Breakdown */
.price-breakdown {
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-radius: 12px;
}

.price-breakdown h4 {
  margin-bottom: 1rem;
}

.price-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.price-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  color: #495057;
}

.price-line.total {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
  padding-top: 0.75rem;
  border-top: 1px solid #dee2e6;
}

/* Payment Section */
.payment-section {
  padding: 1rem;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  background-color: #fafbfc;
  min-height: 60px;
}

.stripe-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem;
  color: #6c757d;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #e9ecef;
  border-top: 2px solid #0071c2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.card-element-container {
  padding: 0.75rem;
  background-color: white;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  min-height: 40px;
}

.card-errors {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.5rem;
  padding: 0.5rem;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 4px;
}

/* Terms and Conditions */
.terms-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.checkbox-input {
  margin-top: 0.25rem;
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.terms-checkbox label {
  font-size: 0.875rem;
  color: #495057;
  line-height: 1.5;
  cursor: pointer;
}

.terms-checkbox a {
  color: #0071c2;
  text-decoration: none;
}

.terms-checkbox a:hover {
  text-decoration: underline;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
}

.cancel-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #d1d5db;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover:not(:disabled) {
  background-color: #f9fafb;
  color: #374151;
}

.cancel-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.confirm-booking-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.confirm-booking-button:hover:not(:disabled) {
  background-color: #005999;
  transform: translateY(-2px);
}

.confirm-booking-button:disabled {
  background-color: #cbd5e1;
  cursor: not-allowed;
  transform: none;
}

.processing-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Terms Modal */
.terms-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.terms-modal-content {
  background-color: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.terms-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e9ecef;
}

.terms-header h3 {
  font-size: 1.2rem;
  margin: 0;
  font-weight: 600;
  color: #1a1a1a;
}

.terms-body {
  padding: 1.5rem;
}

.terms-body p {
  margin: 0 0 1rem 0;
  color: #495057;
  line-height: 1.6;
}

.terms-body ul {
  margin: 0;
  padding-left: 1.5rem;
  color: #495057;
  line-height: 1.6;
}

.terms-body li {
  margin-bottom: 0.5rem;
}

.terms-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e9ecef;
  display: flex;
  justify-content: flex-end;
}

.accept-terms-button {
  background-color: #0071c2;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.accept-terms-button:hover {
  background-color: #005999;
}

/* Responsive Design */
@media (max-width: 768px) {
  .booking-modal-content {
    width: 98%;
    margin: 1rem;
  }

  .booking-modal-header {
    padding: 1rem 1.5rem;
  }

  .booking-modal-body {
    padding: 1.5rem;
  }

  .property-summary {
    flex-direction: column;
    text-align: center;
  }

  .property-image {
    width: 100%;
    height: 150px;
    align-self: center;
  }

  .date-inputs {
    grid-template-columns: 1fr;
  }

  .input-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .terms-modal-content {
    width: 95%;
    margin: 2rem 1rem;
  }
}

@media (max-width: 576px) {
  .booking-modal-header h2 {
    font-size: 1.2rem;
  }

  .property-summary {
    padding: 1rem;
  }

  .booking-form {
    gap: 1.5rem;
  }

  .price-breakdown {
    padding: 1rem;
  }
}
</style>