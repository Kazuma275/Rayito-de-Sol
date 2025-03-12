<template>
  <div class="help-support-page">
    <h1 class="section-title">Centro de Ayuda y Soporte</h1>
    
    <div class="help-container">
      <div class="help-sidebar card">
        <h3 class="help-sidebar-title">Categorías</h3>
        <ul class="help-categories">
          <li v-for="(category, index) in categories" :key="index" 
              :class="{ active: activeCategory === index }"
              @click="activeCategory = index">
            {{ category.name }}
          </li>
        </ul>
        
        <div class="contact-support">
          <h3>¿Necesitas más ayuda?</h3>
          <p>Nuestro equipo de soporte está disponible 24/7</p>
          <button class="btn btn-primary">Contactar Soporte</button>
        </div>
      </div>
      
      <div class="help-content card">
        <div class="help-section">
          <h2>{{ categories[activeCategory].name }}</h2>
          
          <div class="faq-list">
            <div v-for="(faq, index) in categories[activeCategory].faqs" :key="index" class="faq-item">
              <div class="faq-question" @click="toggleFaq(index)">
                <h3>{{ faq.question }}</h3>
                <ChevronDownIcon :class="{ 'rotate': openFaqs.includes(index) }" />
              </div>
              <div class="faq-answer" v-if="openFaqs.includes(index)">
                <p>{{ faq.answer }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { ChevronDownIcon } from 'lucide-vue-next';

const activeCategory = ref(0);
const openFaqs = ref([]);

const toggleFaq = (index) => {
  if (openFaqs.value.includes(index)) {
    openFaqs.value = openFaqs.value.filter(i => i !== index);
  } else {
    openFaqs.value.push(index);
  }
};

const categories = [
  {
    name: 'Primeros pasos',
    faqs: [
      {
        question: '¿Cómo añadir una nueva propiedad?',
        answer: 'Para añadir una nueva propiedad, ve a la sección "Propiedades" y haz clic en el botón "Añadir propiedad". Completa el formulario con los detalles de tu propiedad y haz clic en "Guardar".'
      },
      {
        question: '¿Cómo gestionar las reservas?',
        answer: 'Puedes gestionar todas tus reservas desde la sección "Reservas". Allí podrás ver, confirmar, modificar o cancelar reservas existentes.'
      },
      {
        question: '¿Cómo configurar mi cuenta?',
        answer: 'Para configurar tu cuenta, haz clic en tu perfil en la esquina superior derecha y selecciona "Configuración". Allí podrás actualizar tu información personal, preferencias de notificación y métodos de pago.'
      }
    ]
  },
  {
    name: 'Gestión de propiedades',
    faqs: [
      {
        question: '¿Cómo editar los detalles de una propiedad?',
        answer: 'Para editar una propiedad, ve a la sección "Propiedades", encuentra la propiedad que deseas modificar y haz clic en el botón "Editar". Realiza los cambios necesarios y guarda.'
      },
      {
        question: '¿Puedo desactivar temporalmente una propiedad?',
        answer: 'Sí, puedes cambiar el estado de una propiedad a "Inactiva" desde la página de edición de la propiedad. Esto ocultará la propiedad a nuevas reservas, pero mantendrá las reservas existentes.'
      },
      {
        question: '¿Cómo añadir fotos a mi propiedad?',
        answer: 'En la página de edición de la propiedad, encontrarás una sección para gestionar las fotos. Puedes arrastrar y soltar imágenes o hacer clic para seleccionarlas desde tu dispositivo.'
      }
    ]
  },
  {
    name: 'Reservas y calendario',
    faqs: [
      {
        question: '¿Cómo bloquear fechas en el calendario?',
        answer: 'Ve a la sección "Calendario", selecciona la propiedad y las fechas que deseas bloquear, luego haz clic en "Bloquear fechas". Estas fechas no estarán disponibles para nuevas reservas.'
      },
      {
        question: '¿Puedo establecer precios diferentes según la temporada?',
        answer: 'Sí, puedes configurar precios estacionales desde la sección "Propiedades". Edita la propiedad y ve a la pestaña "Precios", donde podrás definir diferentes tarifas según la temporada.'
      },
      {
        question: '¿Cómo gestionar múltiples reservas a la vez?',
        answer: 'En la sección "Calendario", puedes seleccionar múltiples reservas manteniendo presionada la tecla Ctrl (o Cmd en Mac) mientras haces clic. Luego puedes realizar acciones masivas como confirmar, cancelar o enviar mensajes.'
      }
    ]
  },
  {
    name: 'Pagos y facturación',
    faqs: [
      {
        question: '¿Cómo añadir un método de pago?',
        answer: 'Ve a "Configuración" > "Pagos" y haz clic en "Añadir método de pago". Sigue las instrucciones para añadir tu tarjeta de crédito o cuenta bancaria.'
      },
      {
        question: '¿Cuándo recibiré los pagos de las reservas?',
        answer: 'Los pagos se procesan automáticamente 24 horas después del check-in del huésped. El dinero se transferirá a tu cuenta bancaria en un plazo de 3-5 días hábiles.'
      },
      {
        question: '¿Cómo puedo ver mis facturas?',
        answer: 'Puedes acceder a todas tus facturas desde "Configuración" > "Pagos" > "Historial de facturas". Allí podrás ver y descargar todas tus facturas anteriores.'
      }
    ]
  }
];
</script>

<style scoped>
.help-support-page {
  padding: 1rem 0;
}

.help-container {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
}

.help-sidebar {
  padding: 1.5rem;
}

.help-sidebar-title {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
  color: var(--primary-color);
}

.help-categories {
  list-style: none;
  margin-bottom: 2rem;
}

.help-categories li {
  padding: 0.75rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  margin-bottom: 0.5rem;
  transition: background-color 0.3s;
}

.help-categories li:hover {
  background-color: var(--light-gray);
}

.help-categories li.active {
  background-color: var(--primary-light);
  color: white;
}

.contact-support {
  background-color: var(--light-gray);
  padding: 1.5rem;
  border-radius: 8px;
  text-align: center;
}

.contact-support h3 {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.contact-support p {
  margin-bottom: 1rem;
  color: #666;
}

.help-content {
  padding: 2rem;
}

.help-section h2 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: var(--primary-color);
}

.faq-item {
  margin-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.faq-question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  cursor: pointer;
}

.faq-question h3 {
  font-size: 1.1rem;
  font-weight: 500;
}

.faq-question svg {
  transition: transform 0.3s;
}

.faq-question svg.rotate {
  transform: rotate(180deg);
}

.faq-answer {
  padding: 0 0 1rem;
  color: #666;
}

@media (max-width: 992px) {
  .help-container {
    grid-template-columns: 1fr;
  }
  
  .help-sidebar {
    margin-bottom: 1rem;
  }
}
</style>