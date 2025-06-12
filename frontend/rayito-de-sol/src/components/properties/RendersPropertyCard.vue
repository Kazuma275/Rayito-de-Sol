<template>
  <div>
    <!-- Card de la propiedad -->
    <div class="property-card">
      <div class="property-image-container">
        <img
          :src="property.image"
          :alt="property.name"
          class="property-image"
        />
        <div class="property-badge" v-if="averageRating > 0">
          {{ averageRating.toFixed(1) }} ★
        </div>
      </div>
      <div class="property-content">
        <h3 class="property-name">{{ property.name }}</h3>
        <div class="property-location">
          <MapPinIcon class="location-icon" />
          <span>{{ property.location }}</span>
        </div>
        <div class="property-details">
          <div class="property-detail">
            <BedIcon class="detail-icon" />
            <span>{{ property.bedrooms }} dormitorios</span>
          </div>
          <div class="property-detail">
            <UsersIcon class="detail-icon" />
            <span>{{ property.capacity }} huéspedes</span>
          </div>
        </div>
        <div
          v-if="showAmenities && parsedAmenities.length > 0"
          class="property-amenities"
        >
          <div
            v-for="(amenity, index) in parsedAmenities.slice(0, 3)"
            :key="index"
            class="property-amenity"
          >
            <CheckIcon class="amenity-icon" />
            <span>{{ amenity }}</span>
          </div>
          <div v-if="parsedAmenities.length > 3" class="property-amenity more">
            <span>+{{ parsedAmenities.length - 3 }} más</span>
          </div>
        </div>
        <div class="property-price">
          <span class="price-value">€{{ property.price }}</span>
          <span class="price-period">noche</span>
        </div>
        <button class="view-property-button" @click="openModal">
          Ver Detalles
        </button>
      </div>
    </div>

    <!-- Modal de detalles -->
    <div v-if="showModal" class="property-modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ property.name }}</h2>
          <button class="close-button" @click="closeModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <div class="property-gallery">
            <img
              :src="property.image"
              :alt="property.name"
              class="main-image"
            />
          </div>

          <div class="property-info">
            <div class="info-row">
              <strong>Precio:</strong> €{{ property.price }}/noche
            </div>
            <div class="info-row">
              <strong>Ubicación:</strong> {{ property.location }}
            </div>
            <div class="info-row">
              <strong>Dormitorios:</strong> {{ property.bedrooms }}
            </div>
            <div class="info-row">
              <strong>Capacidad:</strong> {{ property.capacity }} huéspedes
            </div>
            <div class="info-row">
              <strong>Estado:</strong>
              {{
                property.status === "active" ? "Disponible" : "No disponible"
              }}
            </div>
            <div class="info-row description">
              <strong>Descripción:</strong>
              <p>{{ property.description }}</p>
            </div>

            <!-- Amenidades completas -->
            <div
              v-if="parsedAmenities.length > 0"
              class="info-row amenities-section"
            >
              <strong>Comodidades:</strong>
              <div class="amenities-grid">
                <div
                  v-for="(amenity, index) in parsedAmenities"
                  :key="index"
                  class="amenity-item"
                >
                  <CheckIcon class="amenity-check-icon" />
                  <span>{{ amenity }}</span>
                </div>
              </div>
            </div>

            <!-- Sección de reseñas -->
            <div
              v-if="property.reviews && property.reviews.length > 0"
              class="info-row reviews-section"
            >
              <strong>Opiniones ({{ property.reviews.length }}):</strong>
              <div class="reviews-summary">
                <div class="average-rating">
                  <span class="rating-number">{{
                    averageRating.toFixed(1)
                  }}</span>
                  <div class="stars">
                    <span
                      v-for="n in 5"
                      :key="n"
                      :class="[
                        'star',
                        { filled: n <= Math.round(averageRating) },
                      ]"
                      >★</span
                    >
                  </div>
                </div>
              </div>
              <div class="reviews-list">
                <div
                  v-for="review in property.reviews.slice(0, 3)"
                  :key="review.id"
                  class="review-item"
                >
                  <div class="review-header">
                    <span class="reviewer-name">{{ review.author_name }}</span>
                    <span class="review-date">{{
                      formatDate(review.review_date)
                    }}</span>
                  </div>
                  <div class="review-rating">
                    <span
                      v-for="n in 5"
                      :key="n"
                      :class="['star', { filled: n <= review.rating }]"
                      >★</span
                    >
                  </div>
                  <p class="review-text">{{ review.review }}</p>
                </div>
                <div v-if="property.reviews.length > 3" class="more-reviews">
                  <span>+{{ property.reviews.length - 3 }} opiniones más</span>
                </div>
              </div>
            </div>

            <!-- Información adicional -->
            <div class="info-row">
              <strong>Fecha de registro:</strong>
              {{ formatDate(property.created_at) }}
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="edit-button" @click="openEditModal">
            <EditIcon class="button-icon" />
            Editar propiedad
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de edición -->
    <div v-if="showEditModal" class="property-modal" @click="closeEditModal">
      <div class="modal-content edit-modal" @click.stop>
        <div class="modal-header">
          <h2>Editar Propiedad</h2>
          <button class="close-button" @click="closeEditModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        <form @submit.prevent="updateProperty" class="edit-form">
          <div class="modal-body">
            <div class="form-grid">
              <div class="form-group">
                <label for="name">Nombre de la propiedad</label>
                <input
                  id="name"
                  v-model="editForm.name"
                  type="text"
                  class="form-input"
                  required
                />
              </div>

              <div class="form-group">
                <label for="location">Ubicación</label>
                <input
                  id="location"
                  v-model="editForm.location"
                  type="text"
                  class="form-input"
                  required
                />
              </div>

              <div class="form-group">
                <label for="bedrooms">Dormitorios</label>
                <input
                  id="bedrooms"
                  v-model.number="editForm.bedrooms"
                  type="number"
                  class="form-input"
                  min="1"
                  required
                />
              </div>

              <div class="form-group">
                <label for="capacity">Capacidad</label>
                <input
                  id="capacity"
                  v-model.number="editForm.capacity"
                  type="number"
                  class="form-input"
                  min="1"
                  required
                />
              </div>

              <div class="form-group">
                <label for="price">Precio por noche (€)</label>
                <input
                  id="price"
                  v-model="editForm.price"
                  type="number"
                  step="0.01"
                  class="form-input"
                  min="0"
                  required
                />
              </div>

              <div class="form-group">
                <label for="status">Estado</label>
                <select
                  id="status"
                  v-model="editForm.status"
                  class="form-input"
                  required
                >
                  <option value="active">Disponible</option>
                  <option value="inactive">No disponible</option>
                </select>
              </div>

              <div class="form-group full-width">
                <label for="image">URL de la imagen</label>
                <input
                  id="image"
                  v-model="editForm.image"
                  type="url"
                  class="form-input"
                  required
                />
              </div>

              <div class="form-group full-width">
                <label for="description">Descripción</label>
                <textarea
                  id="description"
                  v-model="editForm.description"
                  class="form-textarea"
                  rows="4"
                  required
                ></textarea>
              </div>

              <div class="form-group full-width">
                <label for="amenities">Comodidades (separadas por comas)</label>
                <textarea
                  id="amenities"
                  v-model="amenitiesText"
                  class="form-textarea"
                  rows="3"
                  placeholder="Ej: garaje privado, vistas, parking, aire acondicionado"
                ></textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <div class="form-actions">
              <button
                type="button"
                class="cancel-button"
                @click="closeEditModal"
              >
                Cancelar
              </button>
              <button type="submit" class="save-button" :disabled="isUpdating">
                <LoaderIcon
                  v-if="isUpdating"
                  class="button-icon animate-spin"
                />
                <SaveIcon v-else class="button-icon" />
                {{ isUpdating ? "Guardando..." : "Guardar cambios" }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  MapPinIcon,
  BedIcon,
  UsersIcon,
  CheckIcon,
  XIcon,
  EditIcon,
  SaveIcon,
  LoaderIcon,
} from "lucide-vue-next";
import { computed, ref, reactive } from "vue";
import axios from "axios";
import { apiHeaders } from "@/../utils/api";

const props = defineProps({
  property: {
    type: Object,
    required: true,
  },
  properties: {
    type: Object,
    required: true, // Si tienes un array de propiedades, puedes pasarlo aquí
  },
  showAmenities: {
    type: Boolean,
    default: true,
  },
  amenities: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["viewProperty", "propertyUpdated"]);

const showModal = ref(false);
const showEditModal = ref(false);
const isUpdating = ref(false);

// Formulario de edición
const editForm = reactive({
  name: "",
  location: "",
  bedrooms: 0,
  capacity: 0,
  price: "",
  image: "",
  description: "",
  status: "active",
});

const amenitiesText = ref("");

// Parseamos las amenidades que vienen como string JSON
const parsedAmenities = computed(() => {
  const amenities = props.property.amenities || "";
  // Si ya es array (por ejemplo, por backend o edición), simplemente retorna
  if (Array.isArray(amenities)) return amenities;
  // Intenta parsear como JSON si empieza con [
  if (typeof amenities === "string" && amenities.trim().startsWith("[")) {
    try {
      return JSON.parse(amenities);
    } catch (error) {
      console.error("Error parsing JSON amenities:", error);
    }
  }
  // Si es un string separado por comas
  if (typeof amenities === "string") {
    return amenities
      .split(",")
      .map((a) => a.trim())
      .filter((a) => !!a);
  }
  return [];
});

async function updateProperty() {
  try {
    isUpdating.value = true;
    const { id, ...fields } = editForm;
    fields.amenities = JSON.stringify(
      amenitiesText.value
        .split(",")
        .map(a => a.trim())
        .filter(a => !!a)
    );

    const response = await axios.put(
      `/api/properties/${id}`,
      fields,
      apiHeaders()
    );

    // Actualiza el array de propiedades del padre, si lo tienes como prop
    if (props.properties && Array.isArray(props.properties.value)) {
      const idx = props.properties.value.findIndex((p) => p.id === id);
      if (idx !== -1) {
        props.properties.value[idx] = {
          ...props.properties.value[idx],
          ...response.data.property || response.data,
        };
      }
    }

    showEditModal.value = false;
    alert("Propiedad actualizada correctamente.");
    emit("propertyUpdated", response.data.property || response.data);
  } catch (error) {
    console.error("Error updating property:", error);
    if (error.response) {
      console.error("Response data:", error.response.data);
      alert("Error: " + (error.response.data.message || error.message));
    } else {
      alert("Error al actualizar la propiedad. Por favor, inténtalo de nuevo.");
    }
  } finally {
    isUpdating.value = false;
  }
}

// Calculamos la valoración promedio
const averageRating = computed(() => {
  if (!props.property.reviews || props.property.reviews.length === 0) {
    return 0;
  }
  const sum = props.property.reviews.reduce(
    (acc, review) => acc + review.rating,
    0
  );
  return sum / props.property.reviews.length;
});

const openModal = () => {
  showModal.value = true;
  emit("viewProperty", props.property.id);
};

const closeModal = () => {
  showModal.value = false;
};

const openEditModal = () => {
  // Llenar el formulario con los datos actuales
  editForm.id = props.property.id; // <-- Asegura que el id está presente
  editForm.name = props.property.name;
  editForm.location = props.property.location;
  editForm.bedrooms = props.property.bedrooms;
  editForm.capacity = props.property.capacity;
  editForm.price = props.property.price;
  editForm.status = props.property.status;
  editForm.image = props.property.image;
  editForm.description = props.property.description;
  amenitiesText.value = props.property.amenities; // o el campo correspondiente
  showEditModal.value = true;

  // Convertir amenidades de array a texto
  amenitiesText.value = parsedAmenities.value.join(", ");

  showEditModal.value = true;
  closeModal(); // Cerrar el modal de detalles
};

const closeEditModal = () => {
  showEditModal.value = false;
};

const getAmenityName = (id) => {
  const amenity = props.amenities.find((a) => a.id === id);
  return amenity ? amenity.name : "";
};

// Función para formatear fechas
const formatDate = (dateString) => {
  if (!dateString) return "";

  try {
    const date = new Date(dateString);
    return date.toLocaleDateString("es-ES", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  } catch (error) {
    console.error("Error formatting date:", error);
    return dateString;
  }
};
</script>

<style scoped>
.property-card {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 53, 128, 0.15);
}

.property-image-container {
  position: relative;
}

.property-image {
  width: 100%;
  height: 220px;
  object-fit: cover;
}

.property-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(0, 53, 128, 0.2);
}

.property-content {
  padding: 1.5rem;
}

.property-name {
  font-size: 1.2rem;
  margin: 0 0 0.5rem;
  color: #003580;
  font-weight: 600;
}

.property-location {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  color: #64748b;
}

.location-icon {
  width: 16px;
  height: 16px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.property-details {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.property-detail {
  display: flex;
  align-items: center;
  background-color: #e6f0ff;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.9rem;
  color: #003580;
}

.detail-icon {
  width: 14px;
  height: 14px;
  color: #0071c2;
  margin-right: 0.5rem;
}

.property-amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.property-amenity {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  color: #64748b;
}

.amenity-icon {
  width: 14px;
  height: 14px;
  color: #0071c2;
  margin-right: 0.25rem;
}

.property-amenity.more {
  color: #0071c2;
  font-weight: 500;
}

.property-price {
  margin-bottom: 1rem;
}

.price-value {
  font-size: 1.3rem;
  font-weight: 700;
  color: #003580;
}

.price-period {
  font-size: 0.9rem;
  color: #64748b;
  margin-left: 0.25rem;
}

.view-property-button {
  width: 100%;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 0;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.view-property-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

/* Estilos del Modal */
.property-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 53, 128, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(4px);
  padding: 1rem;
}

.modal-content {
  background-color: white;
  border-radius: 16px;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
}

.modal-content.edit-modal {
  max-width: 800px;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e6f0ff;
  background: linear-gradient(135deg, #003580 0%, #0071c2 100%);
  color: white;
  border-radius: 16px 16px 0 0;
}

.modal-header h2 {
  font-size: 1.5rem;
  margin: 0;
  font-weight: 600;
}

.close-button {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.close-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.close-icon {
  width: 20px;
  height: 20px;
}

.modal-body {
  padding: 2rem;
}

.property-gallery {
  margin-bottom: 1.5rem;
}

.main-image {
  width: 100%;
  height: 250px;
  object-fit: cover;
  border-radius: 12px;
}

.property-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-row {
  display: flex;
  align-items: flex-start;
  padding: 0.75rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e6f0ff;
}

.info-row.description {
  flex-direction: column;
  align-items: flex-start;
}

.info-row.description p {
  margin: 0.5rem 0 0 0;
  line-height: 1.5;
}

.info-row strong {
  color: #003580;
  margin-right: 0.5rem;
  min-width: 100px;
}

.info-row.description strong,
.info-row.amenities-section strong,
.info-row.reviews-section strong {
  min-width: auto;
  margin-bottom: 0.5rem;
}

/* Estilos para amenidades */
.amenities-section {
  flex-direction: column;
  align-items: flex-start;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.5rem;
  width: 100%;
}

.amenity-item {
  display: flex;
  align-items: center;
  padding: 0.25rem 0;
}

.amenity-check-icon {
  width: 16px;
  height: 16px;
  color: #0071c2;
  margin-right: 0.5rem;
}

/* Estilos para reseñas */
.reviews-section {
  flex-direction: column;
  align-items: flex-start;
}

.reviews-summary {
  margin-bottom: 1rem;
}

.average-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.rating-number {
  font-size: 1.5rem;
  font-weight: bold;
  color: #003580;
}

.stars {
  display: flex;
}

.star {
  font-size: 1.2rem;
  color: #e5e7eb;
}

.star.filled {
  color: #fbbf24;
}

.reviews-list {
  width: 100%;
  max-height: 300px;
  overflow-y: auto;
}

.review-item {
  border-bottom: 1px solid #e6f0ff;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}

.review-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.reviewer-name {
  font-weight: 600;
  color: #003580;
}

.review-date {
  font-size: 0.85rem;
  color: #64748b;
}

.review-rating {
  margin-bottom: 0.5rem;
}

.review-rating .star {
  font-size: 1rem;
}

.review-text {
  margin: 0;
  line-height: 1.4;
  color: #374151;
}

.more-reviews {
  text-align: center;
  padding: 0.5rem;
  color: #0071c2;
  font-weight: 500;
}

.modal-footer {
  padding: 1.5rem 2rem;
  border-top: 1px solid #e6f0ff;
  background-color: #f8fafc;
  border-radius: 0 0 16px 16px;
}

.edit-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.edit-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

/* Estilos del formulario de edición */
.edit-form {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 600;
  color: #003580;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.form-input,
.form-textarea {
  padding: 0.75rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.cancel-button,
.save-button {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.cancel-button {
  background-color: #f1f5f9;
  color: #64748b;
}

.cancel-button:hover {
  background-color: #e2e8f0;
}

.save-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
}

.save-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.save-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 768px) {
  .property-modal {
    padding: 0.5rem;
  }

  .modal-content {
    max-height: 95vh;
  }

  .modal-header {
    padding: 1rem 1.5rem;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .modal-footer {
    padding: 1rem 1.5rem;
  }

  .amenities-grid {
    grid-template-columns: 1fr;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }
}
</style>
