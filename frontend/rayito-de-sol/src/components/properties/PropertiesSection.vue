<template>
  <div class="properties-section">
    <div class="section-header">
        <button class="add-button" @click="openAddModal">
          <PlusIcon class="add-icon" />
          Añadir Propiedad
        </button>
    </div>
    
    <PropertyList 
      :properties="filteredProperties" 
      :favorites="favorites"
      :showFilters="true"
      :showPagination="true"
      @toggleFavorite="toggleFavorite"
      @viewProperty="viewProperty"
    />
    
<!--     <PropertyStatistics 
      v-if="showStatistics"
      :properties="properties" 
      :bookings="bookings"
    /> -->
    
    <EditPropertyModal 
      v-if="showEditModal"
      :visible="showEditModal"
      :property="selectedProperty"
      :isEditMode="isEditMode"
      @close="closeEditModal"
      @submit="handlePropertySubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { PlusIcon } from 'lucide-vue-next';
import PropertyList from './PropertyList.vue';
import PropertyStatistics from './PropertyStatistics.vue';
import EditPropertyModal from './EditPropertyModal.vue';

// Estado
const properties = ref([
  {
    id: 1,
    name: 'Apartamento Vista Mar',
    location: 'Calahonda, Málaga',
    bedrooms: 2,
    capacity: 4,
    price: 120,
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80',
    description: 'Hermoso apartamento con vistas al mar Mediterráneo. Perfecto para familias o parejas que buscan unas vacaciones tranquilas.',
    amenities: [1, 2, 3, 4],
    status: 'active',
    statusText: 'Activo'
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
    statusText: 'Activo'
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
    statusText: 'Inactivo'
  }
]);

const bookings = ref([
  {
    id: 1,
    propertyId: 1,
    checkIn: '2023-08-10',
    checkOut: '2023-08-17',
    guestName: 'Juan Pérez',
    guests: 3,
    total: 890,
    status: 'confirmed'
  },
  {
    id: 2,
    propertyId: 2,
    checkIn: '2023-09-05',
    checkOut: '2023-09-12',
    guestName: 'María García',
    guests: 5,
    total: 1260,
    status: 'pending'
  }
]);


const favorites = ref([1, 3]);
const showStatistics = ref(true);
const showAddPropertyModal = ref(false);
const showEditModal = ref(false);
const isEditMode = ref(false);
const selectedProperty = ref({});
const searchParams = ref({});
const filterParams = ref({});

// Propiedades filtradas basadas en búsqueda y filtros
const filteredProperties = computed(() => {
  let result = [...properties.value];
  
  // Aplicar filtros de búsqueda
  if (searchParams.value.location) {
    result = result.filter(property => 
      property.location.toLowerCase().includes(searchParams.value.location.toLowerCase())
    );
  }
  
  // Aplicar filtros avanzados
  if (filterParams.value.minPrice) {
    result = result.filter(property => property.price >= filterParams.value.minPrice);
  }
  
  if (filterParams.value.maxPrice) {
    result = result.filter(property => property.price <= filterParams.value.maxPrice);
  }
  
  if (filterParams.value.amenities && filterParams.value.amenities.length > 0) {
    result = result.filter(property => 
      filterParams.value.amenities.every(amenity => 
        property.amenities.includes(amenity)
      )
    );
  }
  
  return result;
});

// Métodos
const handleSearch = (params) => {
  searchParams.value = params;
};

const handleFilters = (params) => {
  filterParams.value = params;
};

const toggleFavorite = (propertyId) => {
  const index = favorites.value.indexOf(propertyId);
  if (index === -1) {
    favorites.value.push(propertyId);
  } else {
    favorites.value.splice(index, 1);
  }
};

const viewProperty = (propertyId) => {
  // Aquí podrías navegar a la página de detalles de la propiedad
  console.log(`Viewing property ${propertyId}`);
};

const openAddModal = () => {
  selectedProperty.value = {};
  isEditMode.value = false;
  showEditModal.value = true;
};

const openEditModal = (property) => {
  selectedProperty.value = { ...property };
  isEditMode.value = true;
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
};

const handlePropertySubmit = async (propertyData) => {
  if (isEditMode.value) {
    // Actualizar propiedad 
    try {
      await axios.put(`/api/properties/${propertyData.id}`, propertyData);
      const index = properties.value.findIndex(p => p.id === propertyData.id);
      if (index !== -1) {
        properties.value[index] = { ...propertyData };
      }
    } catch (error) {
      alert('Error al actualizar la propiedad');
      return;
    }
  } else {
    // Crear propiedad
    try {
      const response = await axios.post('/api/properties', propertyData);
      properties.value.push(response.data);
    } catch (error) {
      return;
    }
  }
  showEditModal.value = false;
};
</script>

<style scoped>
.properties-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 60vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.section-title {
  font-size: 1.8rem;
  color: #003580;
  margin: 0;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.add-button {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
  margin-left: auto; 
}


.add-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.add-icon {
  width: 18px;
  height: 18px;
  margin-right: 0.5rem;
}

@media (max-width: 768px) {
  .properties-section {
    padding: 1.5rem;
    margin: 1.5rem;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .add-button {
    width: 100%;
    justify-content: center;
  }
}
</style>