<template>
  <div class="help-support-section">
    <div class="section-header">
      <h2 class="section-title">Centro de Ayuda y Soporte</h2>
      <p class="section-description">Encuentra respuestas a tus preguntas y soluciones a problemas comunes</p>
    </div>
    
    <div class="help-container">
      <div class="help-sidebar">
        <div class="search-box">
          <SearchIcon class="search-icon" />
          <input 
            type="text" 
            placeholder="Buscar ayuda..." 
            v-model="searchQuery"
            class="search-input"
            @input="filterCategories"
          />
        </div>
        
        <div class="category-list">
          <h3 class="sidebar-title">Categorías</h3>
          <div 
            v-for="(category, index) in filteredCategories" 
            :key="index" 
            class="category-item"
            :class="{ active: activeCategory === index }"
            @click="activeCategory = index"
          >
            <component :is="category.icon" class="category-icon" />
            <span>{{ category.name }}</span>
            <ChevronRightIcon class="arrow-icon" />
          </div>
        </div>
        
        <div class="contact-support">
          <h3>¿Necesitas más ayuda?</h3>
          <p>Nuestro equipo de soporte está disponible 24/7 para ayudarte</p>
          <button class="contact-button" @click="showContactModal = true">
            <MessageSquareIcon class="button-icon" />
            Contactar Soporte
          </button>
        </div>
      </div>
      
      <div class="help-content">
        <div class="category-header">
          <component :is="categories[activeCategory].icon" class="header-icon" />
          <h2>{{ categories[activeCategory].name }}</h2>
        </div>
        
        <div class="faq-list">
          <div 
            v-for="(faq, index) in categories[activeCategory].faqs" 
            :key="index" 
            class="faq-item"
            :class="{ expanded: expandedFaqs.includes(index) }"
          >
            <div class="faq-question" @click="toggleFaq(index)">
              <h3>{{ faq.question }}</h3>
              <ChevronDownIcon class="question-icon" :class="{ rotated: expandedFaqs.includes(index) }" />
            </div>
            <div class="faq-answer" v-if="expandedFaqs.includes(index)">
              <div v-html="faq.answer"></div>
              
              <div v-if="faq.links && faq.links.length > 0" class="faq-links">
                <h4>Enlaces útiles:</h4>
                <ul>
                  <li v-for="(link, linkIndex) in faq.links" :key="linkIndex">
                    <a :href="link.url" target="_blank" class="help-link">
                      <ExternalLinkIcon class="link-icon" />
                      {{ link.text }}
                    </a>
                  </li>
                </ul>
              </div>
              
              <div v-if="faq.relatedQuestions && faq.relatedQuestions.length > 0" class="related-questions">
                <h4>Preguntas relacionadas:</h4>
                <ul>
                  <li v-for="(question, qIndex) in faq.relatedQuestions" :key="qIndex">
                    <a href="#" @click.prevent="goToRelatedQuestion(question.categoryIndex, question.questionIndex)" class="related-link">
                      {{ question.text }}
                    </a>
                  </li>
                </ul>
              </div>
              
              <div class="feedback-section">
                <p>¿Te ha resultado útil esta respuesta?</p>
                <div class="feedback-buttons">
                  <button class="feedback-button" @click="provideFeedback(index, true)">
                    <ThumbsUpIcon class="feedback-icon" />
                    Sí
                  </button>
                  <button class="feedback-button" @click="provideFeedback(index, false)">
                    <ThumbsDownIcon class="feedback-icon" />
                    No
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal de contacto -->
    <div v-if="showContactModal" class="contact-modal-overlay" @click="showContactModal = false">
      <div class="contact-modal" @click.stop>
        <div class="modal-header">
          <h3>Contactar con Soporte</h3>
          <button class="close-button" @click="showContactModal = false">
            <XIcon class="close-icon" />
          </button>
        </div>
        
        <div class="modal-body">
          <form @submit.prevent="submitContactForm" class="contact-form">
            <div class="form-group">
              <label for="contact-subject">Asunto</label>
              <select id="contact-subject" v-model="contactForm.subject" class="form-input" required>
                <option value="">Selecciona un asunto</option>
                <option value="technical">Problema técnico</option>
                <option value="account">Cuenta y acceso</option>
                <option value="billing">Facturación y pagos</option>
                <option value="reservation">Reservas</option>
                <option value="other">Otro</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="contact-message">Mensaje</label>
              <textarea 
                id="contact-message" 
                v-model="contactForm.message" 
                class="form-textarea" 
                rows="5" 
                placeholder="Describe tu problema o pregunta con detalle..."
                required
              ></textarea>
            </div>
            
            <div class="form-group">
              <label for="contact-email">Email de contacto</label>
              <input 
                type="email" 
                id="contact-email" 
                v-model="contactForm.email" 
                class="form-input" 
                placeholder="tu@email.com"
                required
              />
            </div>
            
            <div class="form-group checkbox-group">
              <input type="checkbox" id="contact-urgent" v-model="contactForm.urgent" />
              <label for="contact-urgent">Este es un problema urgente</label>
            </div>
            
            <div class="form-actions">
              <button type="button" class="cancel-button" @click="showContactModal = false">Cancelar</button>
              <button type="submit" class="submit-button" :disabled="isSubmitting">
                <LoaderIcon v-if="isSubmitting" class="spinner" />
                <span v-else>Enviar Mensaje</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { 
  SearchIcon, 
  ChevronRightIcon, 
  ChevronDownIcon, 
  MessageSquareIcon,
  ExternalLinkIcon,
  ThumbsUpIcon,
  ThumbsDownIcon,
  XIcon,
  LoaderIcon,
  HomeIcon,
  CalendarIcon,
  CreditCardIcon,
  UserIcon,
  SettingsIcon,
  HelpCircleIcon,
  ShieldIcon,
  BookIcon
} from 'lucide-vue-next';

// Estado
const searchQuery = ref('');
const activeCategory = ref(0);
const expandedFaqs = ref([]);
const showContactModal = ref(false);
const isSubmitting = ref(false);
const contactForm = ref({
  subject: '',
  message: '',
  email: '',
  urgent: false
});

// Categorías y preguntas frecuentes
const categories = ref([
  {
    name: 'Primeros pasos',
    icon: HomeIcon,
    faqs: [
      {
        question: '¿Cómo añadir una nueva propiedad?',
        answer: `
          <p>Para añadir una nueva propiedad a tu cuenta, sigue estos pasos:</p>
          <ol>
            <li>Ve a la sección "Propiedades" en el menú principal</li>
            <li>Haz clic en el botón "Añadir Propiedad" en la parte superior derecha</li>
            <li>Completa el formulario con los detalles de tu propiedad:
              <ul>
                <li>Nombre y ubicación</li>
                <li>Número de habitaciones y capacidad</li>
                <li>Precio por noche</li>
                <li>Descripción detallada</li>
                <li>Servicios disponibles</li>
              </ul>
            </li>
            <li>Sube fotos de alta calidad de tu propiedad</li>
            <li>Haz clic en "Guardar Propiedad"</li>
          </ol>
          <p>Una vez añadida, tu propiedad estará disponible para ser reservada por los huéspedes.</p>
        `,
        links: [
          { text: 'Guía completa para añadir propiedades', url: '#' },
          { text: 'Consejos para fotos atractivas', url: '#' }
        ],
        relatedQuestions: [
          { text: '¿Cómo editar los detalles de una propiedad?', categoryIndex: 1, questionIndex: 0 },
          { text: '¿Cómo establecer precios estacionales?', categoryIndex: 2, questionIndex: 1 }
        ]
      },
      {
        question: '¿Cómo gestionar las reservas?',
        answer: `
          <p>La gestión de reservas se realiza desde la sección "Reservas" del panel de control. Desde allí podrás:</p>
          <ul>
            <li><strong>Ver todas tus reservas:</strong> Organizadas por estado (pendientes, confirmadas, completadas, canceladas)</li>
            <li><strong>Confirmar reservas:</strong> Revisa y acepta las solicitudes de reserva pendientes</li>
            <li><strong>Comunicarte con los huéspedes:</strong> Envía mensajes directamente a los huéspedes</li>
            <li><strong>Gestionar el calendario:</strong> Bloquea fechas no disponibles o ajusta la disponibilidad</li>
            <li><strong>Ver detalles completos:</strong> Accede a toda la información de cada reserva</li>
          </ul>
          <p>Recuerda responder a las solicitudes de reserva lo antes posible para mantener una buena valoración como anfitrión.</p>
        `,
        links: [
          { text: 'Tutorial de gestión de reservas', url: '#' }
        ],
        relatedQuestions: [
          { text: '¿Cómo bloquear fechas en el calendario?', categoryIndex: 2, questionIndex: 0 },
          { text: '¿Cómo cancelar una reserva?', categoryIndex: 3, questionIndex: 1 }
        ]
      },
      {
        question: '¿Cómo configurar mi cuenta?',
        answer: `
          <p>Para configurar tu cuenta y personalizar tus preferencias:</p>
          <ol>
            <li>Haz clic en tu perfil en la esquina superior derecha</li>
            <li>Selecciona "Configuración" en el menú desplegable</li>
            <li>Desde allí podrás:
              <ul>
                <li><strong>Perfil:</strong> Actualizar tu información personal y foto de perfil</li>
                <li><strong>Notificaciones:</strong> Configurar cómo y cuándo recibes alertas</li>
                <li><strong>Pagos:</strong> Gestionar tus métodos de pago y datos bancarios</li>
                <li><strong>Privacidad:</strong> Ajustar tus preferencias de privacidad</li>
                <li><strong>Seguridad:</strong> Cambiar tu contraseña y configurar la verificación en dos pasos</li>
              </ul>
            </li>
          </ol>
          <p>Es recomendable mantener tu información actualizada para facilitar la comunicación y los pagos.</p>
        `,
        links: [
          { text: 'Guía de configuración de cuenta', url: '#' }
        ],
        relatedQuestions: [
          { text: '¿Cómo cambiar mi contraseña?', categoryIndex: 4, questionIndex: 0 },
          { text: '¿Cómo actualizar mis datos bancarios?', categoryIndex: 3, questionIndex: 0 }
        ]
      }
    ]
  },
  {
    name: 'Gestión de propiedades',
    icon: BookIcon,
    faqs: [
      {
        question: '¿Cómo editar los detalles de una propiedad?',
        answer: `
          <p>Para editar una propiedad existente:</p>
          <ol>
            <li>Ve a la sección "Propiedades" en el menú principal</li>
            <li>Localiza la propiedad que deseas modificar</li>
            <li>Haz clic en el botón "Editar" (icono de lápiz)</li>
            <li>Actualiza la información necesaria en el formulario</li>
            <li>Haz clic en "Guardar Cambios" para aplicar las modificaciones</li>
          </ol>
          <p>Puedes editar todos los aspectos de tu propiedad, incluyendo:</p>
          <ul>
            <li>Información básica (nombre, ubicación, etc.)</li>
            <li>Detalles (habitaciones, capacidad, etc.)</li>
            <li>Precios y tarifas</li>
            <li>Descripción y servicios</li>
            <li>Fotos (añadir, eliminar o reordenar)</li>
            <li>Normas de la casa</li>
          </ul>
          <p>Los cambios se reflejarán inmediatamente en tu listado.</p>
        `,
        links: [
          { text: 'Consejos para optimizar tu listado', url: '#' }
        ]
      },
      {
        question: '¿Puedo desactivar temporalmente una propiedad?',
        answer: `
          <p>Sí, puedes desactivar temporalmente una propiedad sin eliminarla completamente de tu cuenta. Para hacerlo:</p>
          <ol>
            <li>Ve a la sección "Propiedades" en el menú principal</li>
            <li>Encuentra la propiedad que deseas desactivar</li>
            <li>Haz clic en el botón "Editar"</li>
            <li>En la parte inferior del formulario, encontrarás la opción "Estado de la propiedad"</li>
            <li>Cambia el estado de "Activo" a "Inactivo"</li>
            <li>Guarda los cambios</li>
          </ol>
          <p>Cuando una propiedad está inactiva:</p>
          <ul>
            <li>No aparecerá en los resultados de búsqueda</li>
            <li>No podrá recibir nuevas reservas</li>
            <li>Las reservas existentes se mantendrán activas</li>
            <li>Podrás reactivarla en cualquier momento siguiendo los mismos pasos</li>
          </ul>
          <p>Esta función es útil si necesitas hacer renovaciones, si la propiedad no está disponible temporalmente, o si deseas tomarte un descanso como anfitrión.</p>
        `,
        links: [
          { text: 'Gestión del estado de propiedades', url: '#' }
        ]
      },
      {
        question: '¿Cómo añadir fotos a mi propiedad?',
        answer: `
          <p>Las fotos de calidad son esenciales para atraer huéspedes. Para añadir o gestionar las fotos de tu propiedad:</p>
          <ol>
            <li>Ve a la sección "Propiedades" y selecciona la propiedad deseada</li>
            <li>Haz clic en "Editar" o en la pestaña específica de "Fotos"</li>
            <li>Verás la sección de gestión de fotos donde puedes:
              <ul>
                <li><strong>Añadir nuevas fotos:</strong> Haz clic en "Subir fotos" o arrastra y suelta los archivos</li>
                <li><strong>Reordenar fotos:</strong> Arrastra las imágenes para cambiar su orden (la primera será la imagen principal)</li>
                <li><strong>Eliminar fotos:</strong> Haz clic en el icono de papelera en cada imagen</li>
                <li><strong>Editar descripciones:</strong> Añade textos descriptivos a cada imagen</li>
              </ul>
            </li>
            <li>Guarda los cambios cuando hayas terminado</li>
          </ol>
          <p><strong>Recomendaciones para fotos efectivas:</strong></p>
          <ul>
            <li>Utiliza imágenes de alta resolución (mínimo 1024x683 píxeles)</li>
            <li>Incluye fotos de todas las habitaciones y espacios</li>
            <li>Toma las fotos durante el día con buena iluminación natural</li>
            <li>Mantén los espacios ordenados y limpios</li>
            <li>Incluye fotos del exterior y las vistas</li>
            <li>Destaca características únicas de tu propiedad</li>
          </ul>
        `,
        links: [
          { text: 'Guía para fotografiar propiedades', url: '#' },
          { text: 'Formatos y tamaños de imagen recomendados', url: '#' }
        ]
      },
      {
        question: '¿Cómo gestionar los servicios y amenidades?',
        answer: `
          <p>Los servicios y amenidades son características importantes que los huéspedes consideran al elegir una propiedad. Para gestionarlos:</p>
          <ol>
            <li>Ve a la sección "Propiedades" y selecciona la propiedad deseada</li>
            <li>Haz clic en "Editar"</li>
            <li>Desplázate hasta la sección "Servicios disponibles"</li>
            <li>Marca las casillas de los servicios que ofrece tu propiedad</li>
            <li>Si un servicio no aparece en la lista, puedes añadirlo en la sección "Servicios adicionales"</li>
            <li>Guarda los cambios</li>
          </ol>
          <p><strong>Servicios populares que atraen a los huéspedes:</strong></p>
          <ul>
            <li>Wi-Fi de alta velocidad</li>
            <li>Aire acondicionado y calefacción</li>
            <li>Piscina o acceso a playa</li>
            <li>Cocina equipada</li>
            <li>Lavadora y secadora</li>
            <li>Estacionamiento gratuito</li>
            <li>Smart TV con servicios de streaming</li>
            <li>Terraza o balcón</li>
          </ul>
          <p>Recuerda que es importante ser honesto sobre los servicios que ofreces para evitar malentendidos con los huéspedes.</p>
        `,
        links: [
          { text: 'Servicios más valorados por los huéspedes', url: '#' }
        ]
      }
    ]
  },
  {
    name: 'Reservas y calendario',
    icon: CalendarIcon,
    faqs: [
      {
        question: '¿Cómo bloquear fechas en el calendario?',
        answer: `
          <p>Bloquear fechas en tu calendario es útil cuando la propiedad no está disponible para reservas. Para hacerlo:</p>
          <ol>
            <li>Ve a la sección "Calendario" en el menú principal</li>
            <li>Selecciona la propiedad para la que deseas bloquear fechas</li>
            <li>Tienes dos opciones para bloquear fechas:
              <ul>
                <li><strong>Bloqueo individual:</strong> Haz clic en una fecha específica en el calendario y selecciona "Bloquear"</li>
                <li><strong>Bloqueo en masa:</strong> Utiliza la sección "Acciones en bloque" para seleccionar un rango de fechas, luego haz clic en "Marcar como no disponible"</li>
              </ul>
            </li>
            <li>Opcionalmente, puedes añadir una nota para recordar el motivo del bloqueo</li>
            <li>Guarda los cambios</li>
          </ol>
          <p>Las fechas bloqueadas aparecerán en rojo en tu calendario y no estarán disponibles para reservas.</p>
          <p><strong>Casos comunes para bloquear fechas:</strong></p>
          <ul>
            <li>Uso personal de la propiedad</li>
            <li>Mantenimiento o renovaciones</li>
            <li>Temporadas en las que no deseas alquilar</li>
            <li>Reservas realizadas a través de otros canales</li>
          </ul>
        `,
        links: [
          { text: 'Gestión avanzada del calendario', url: '#' }
        ]
      },
      {
        question: '¿Puedo establecer precios diferentes según la temporada?',
        answer: `
          <p>Sí, puedes configurar precios estacionales para maximizar tus ingresos. Para establecer precios variables:</p>
          <ol>
            <li>Ve a la sección "Propiedades" y selecciona la propiedad deseada</li>
            <li>Haz clic en "Editar" y navega a la pestaña "Precios"</li>
            <li>En la sección "Precios estacionales", haz clic en "Añadir temporada"</li>
            <li>Define cada temporada con:
              <ul>
                <li>Nombre (ej. "Temporada alta", "Navidad", "Verano")</li>
                <li>Fechas de inicio y fin</li>
                <li>Precio por noche para ese período</li>
                <li>Estancia mínima (opcional)</li>
              </ul>
            </li>
            <li>Puedes añadir múltiples temporadas según necesites</li>
            <li>Guarda los cambios</li>
          </ol>
          <p><strong>Estrategias de precios recomendadas:</strong></p>
          <ul>
            <li>Aumenta los precios durante temporadas de alta demanda (verano, festividades)</li>
            <li>Ofrece descuentos en temporada baja para mantener la ocupación</li>
            <li>Considera precios especiales para fines de semana</li>
            <li>Ajusta los precios según eventos locales importantes</li>
            <li>Ofrece descuentos para estancias largas</li>
          </ul>
          <p>Un buen sistema de precios dinámicos puede aumentar significativamente tus ingresos anuales.</p>
        `,
        links: [
          { text: 'Guía de precios estacionales', url: '#' },
          { text: 'Análisis de mercado para optimizar precios', url: '#' }
        ]
      },
      {
        question: '¿Cómo gestionar múltiples reservas a la vez?',
        answer: `
          <p>Gestionar múltiples reservas puede ser sencillo con las herramientas adecuadas:</p>
          <ol>
            <li>Utiliza la vista de calendario para tener una visión general de todas tus reservas</li>
            <li>En la sección "Reservas", puedes:
              <ul>
                <li>Filtrar por propiedad, estado o fechas</li>
                <li>Seleccionar múltiples reservas manteniendo presionada la tecla Ctrl (o Cmd en Mac) mientras haces clic</li>
                <li>Realizar acciones masivas como confirmar, enviar mensajes o exportar datos</li>
              </ul>
            </li>
            <li>Configura notificaciones para mantenerte al día con todas las reservas</li>
          </ol>
          <p><strong>Consejos para gestionar múltiples propiedades:</strong></p>
          <ul>
            <li>Establece un sistema para revisar las reservas diariamente</li>
            <li>Utiliza etiquetas personalizadas para organizar las reservas</li>
            <li>Configura respuestas automáticas para las consultas más frecuentes</li>
            <li>Considera contratar ayuda para la limpieza y el check-in si gestionas muchas propiedades</li>
            <li>Utiliza la función de exportación para mantener registros externos</li>
          </ul>
          <p>Una buena organización es clave para gestionar múltiples reservas sin estrés.</p>
        `,
        links: [
          { text: 'Herramientas de productividad para anfitriones', url: '#' }
        ]
      },
      {
        question: '¿Qué hacer si un huésped solicita cambios en su reserva?',
        answer: `
          <p>Las solicitudes de modificación de reservas son comunes. Así puedes gestionarlas:</p>
          <ol>
            <li>Cuando un huésped solicita un cambio, recibirás una notificación</li>
            <li>Ve a la sección "Reservas" y localiza la reserva en cuestión</li>
            <li>Haz clic en "Ver detalles" y luego en la pestaña "Modificaciones"</li>
            <li>Revisa los cambios solicitados (fechas, número de huéspedes, etc.)</li>
            <li>Tienes tres opciones:
              <ul>
                <li><strong>Aceptar:</strong> Aprueba los cambios tal como se solicitan</li>
                <li><strong>Rechazar:</strong> Mantén la reserva original sin cambios</li>
                <li><strong>Proponer alternativa:</strong> Sugiere una solución diferente</li>
              </ul>
            </li>
          </ol>
          <p><strong>Consideraciones importantes:</strong></p>
          <ul>
            <li>Verifica la disponibilidad antes de aceptar cambios de fechas</li>
            <li>Revisa si hay cambios en el precio total</li>
            <li>Considera si los cambios afectan a otras reservas</li>
            <li>Comunícate claramente con el huésped sobre tu decisión</li>
            <li>Si propones una alternativa, explica tus razones</li>
          </ul>
          <p>Ser flexible con los cambios razonables puede generar buenas reseñas, pero no dudes en rechazar solicitudes que te causen inconvenientes significativos.</p>
        `,
        links: [
          { text: 'Políticas de modificación recomendadas', url: '#' }
        ]
      }
    ]
  },
  {
    name: 'Pagos y facturación',
    icon: CreditCardIcon,
    faqs: [
      {
        question: '¿Cómo actualizar mis datos bancarios?',
        answer: `
          <p>Para actualizar tus datos bancarios y asegurar que recibes tus pagos correctamente:</p>
          <ol>
            <li>Ve a "Configuración" en el menú desplegable de tu perfil</li>
            <li>Selecciona la pestaña "Pagos"</li>
            <li>Haz clic en "Métodos de pago" y luego en "Editar" junto a tu cuenta bancaria</li>
            <li>Introduce la nueva información:
              <ul>
                <li>Nombre del titular de la cuenta</li>
                <li>Número de cuenta o IBAN</li>
                <li>Código SWIFT/BIC (para transferencias internacionales)</li>
                <li>Dirección del banco</li>
              </ul>
            </li>
            <li>Verifica que todos los datos sean correctos</li>
            <li>Haz clic en "Guardar cambios"</li>
          </ol>
          <p><strong>Información importante:</strong></p>
          <ul>
            <li>Por razones de seguridad, es posible que debas verificar tu identidad nuevamente</li>
            <li>Los cambios pueden tardar hasta 3 días hábiles en procesarse</li>
            <li>Los pagos ya programados podrían seguir enviándose a tu cuenta anterior</li>
            <li>Asegúrate de que la cuenta bancaria esté a tu nombre o al nombre registrado en la plataforma</li>
          </ul>
          <p>Mantén tus datos bancarios actualizados para evitar retrasos en los pagos.</p>
        `,
        links: [
          { text: 'Requisitos para cuentas bancarias internacionales', url: '#' }
        ]
      },
      {
        question: '¿Cuándo recibiré los pagos de las reservas?',
        answer: `
          <p>El calendario de pagos depende de tu configuración y de las políticas de la plataforma:</p>
          <ol>
            <li>Por defecto, los pagos se procesan 24 horas después del check-in del huésped</li>
            <li>El dinero se transferirá a tu cuenta bancaria en un plazo de 3-5 días hábiles después del procesamiento</li>
          </ol>
          <p><strong>Opciones de calendario de pagos:</strong></p>
          <ul>
            <li><strong>Estándar (predeterminado):</strong> Pago 24 horas después del check-in</li>
            <li><strong>Mensual:</strong> Todos los pagos acumulados se envían una vez al mes</li>
            <li><strong>Pago dividido:</strong> 50% al confirmar la reserva y 50% después del check-in</li>
          </ul>
          <p>Para cambiar tu calendario de pagos:</p>
          <ol>
            <li>Ve a "Configuración" > "Pagos"</li>
            <li>Selecciona "Preferencias de pago"</li>
            <li>Elige tu opción preferida</li>
            <li>Guarda los cambios</li>
          </ol>
          <p><strong>Nota:</strong> Los cambios en el calendario de pagos se aplicarán solo a las nuevas reservas, no a las existentes.</p>
        `,
        links: [
          { text: 'Detalles sobre el procesamiento de pagos', url: '#' }
        ]
      },
      {
        question: '¿Cómo puedo ver mis facturas?',
        answer: `
          <p>Puedes acceder a todas tus facturas y al historial de transacciones siguiendo estos pasos:</p>
          <ol>
            <li>Ve a "Configuración" en el menú desplegable de tu perfil</li>
            <li>Selecciona la pestaña "Pagos"</li>
            <li>Haz clic en "Historial de facturas"</li>
            <li>Aquí encontrarás:
              <ul>
                <li>Todas tus facturas ordenadas por fecha</li>
                <li>Detalles de cada transacción</li>
                <li>Estado de los pagos</li>
                <li>Comisiones aplicadas</li>
              </ul>
            </li>
            <li>Puedes filtrar por fecha, propiedad o estado</li>
            <li>Para ver o descargar una factura específica, haz clic en "Ver" o "Descargar PDF"</li>
          </ol>
          <p><strong>Información fiscal importante:</strong></p>
          <ul>
            <li>Todas las facturas incluyen el desglose de impuestos aplicables</li>
            <li>Puedes descargar un resumen anual para tu declaración de impuestos</li>
            <li>Las facturas se guardan durante 5 años en el sistema</li>
            <li>Si necesitas facturas con datos fiscales específicos, puedes configurarlo en "Información fiscal"</li>
          </ul>
          <p>Mantén un buen registro de tus facturas para facilitar la gestión contable y fiscal de tu actividad como anfitrión.</p>
        `,
        links: [
          { text: 'Guía fiscal para anfitriones', url: '#' },
          { text: 'Cómo exportar datos para contabilidad', url: '#' }
        ]
      },
      {
        question: '¿Qué comisiones se aplican a mis reservas?',
        answer: `
          <p>Nuestra plataforma aplica las siguientes comisiones a las reservas:</p>
          <ul>
            <li><strong>Comisión básica para anfitriones:</strong> 3% del subtotal de la reserva (antes de impuestos)</li>
            <li><strong>Comisión para huéspedes:</strong> 8-12% del subtotal, dependiendo del valor de la reserva</li>
          </ul>
          <p><strong>Desglose detallado:</strong></p>
          <table class="fee-table">
            <tr>
              <th>Concepto</th>
              <th>Porcentaje</th>
              <th>Notas</th>
            </tr>
            <tr>
              <td>Comisión básica</td>
              <td>3%</td>
              <td>Se aplica a todas las reservas</td>
            </tr>
            <tr>
              <td>Procesamiento de pagos</td>
              <td>1.5%</td>
              <td>Para cubrir costes de transacciones</td>
            </tr>
            <tr>
              <td>Seguro de propiedad</td>
              <td>0.5%</td>
              <td>Protección básica incluida</td>
            </tr>
          </table>
          <p><strong>Programas opcionales:</strong></p>
          <ul>
            <li><strong>Programa Propiedades Premium:</strong> Comisión del 5% pero con mayor visibilidad y servicios adicionales</li>
            <li><strong>Seguro ampliado:</strong> 1% adicional para cobertura extendida</li>
            <li><strong>Gestión de limpieza:</strong> Tarifa fija según el tamaño de la propiedad</li>
          </ul>
          <p>Todas las comisiones se detallan claramente en tus facturas y se deducen automáticamente antes de realizar los pagos a tu cuenta bancaria.</p>
        `,
        links: [
          { text: 'Comparativa de comisiones con otras plataformas', url: '#' },
          { text: 'Programa Propiedades Premium', url: '#' }
        ]
      }
    ]
  },
  {
    name: 'Cuenta y seguridad',
    icon: ShieldIcon,
    faqs: [
      {
        question: '¿Cómo cambiar mi contraseña?',
        answer: `
          <p>Para cambiar tu contraseña y mantener tu cuenta segura:</p>
          <ol>
            <li>Haz clic en tu perfil en la esquina superior derecha</li>
            <li>Selecciona "Configuración" en el menú desplegable</li>
            <li>Ve a la pestaña "Seguridad"</li>
            <li>Haz clic en "Cambiar contraseña"</li>
            <li>Introduce tu contraseña actual</li>
            <li>Introduce tu nueva contraseña (debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos)</li>
            <li>Confirma tu nueva contraseña</li>
            <li>Haz clic en "Guardar cambios"</li>
          </ol>
          <p><strong>Recomendaciones de seguridad:</strong></p>
          <ul>
            <li>Cambia tu contraseña regularmente (cada 3-6 meses)</li>
            <li>No uses la misma contraseña que en otros sitios</li>
            <li>Evita contraseñas obvias como fechas de nacimiento o "123456"</li>
            <li>Considera usar un gestor de contraseñas</li>
            <li>Activa la autenticación de dos factores para mayor seguridad</li>
          </ul>
          <p>Después de cambiar tu contraseña, recibirás un correo electrónico de confirmación y se cerrarán todas las sesiones activas excepto la actual.</p>
        `,
        links: [
          { text: 'Consejos para crear contraseñas seguras', url: '#' }
        ]
      },
      {
        question: '¿Cómo activar la verificación en dos pasos?',
        answer: `
          <p>La verificación en dos pasos (2FA) añade una capa extra de seguridad a tu cuenta. Para activarla:</p>
          <ol>
            <li>Ve a "Configuración" > "Seguridad"</li>
            <li>Busca la sección "Verificación en dos pasos"</li>
            <li>Haz clic en "Activar"</li>
            <li>Elige tu método preferido:
              <ul>
                <li><strong>Aplicación de autenticación:</strong> Google Authenticator, Microsoft Authenticator, etc.</li>
                <li><strong>SMS:</strong> Códigos enviados a tu teléfono móvil</li>
                <li><strong>Email:</strong> Códigos enviados a tu dirección de correo</li>
              </ul>
            </li>
            <li>Sigue las instrucciones para completar la configuración</li>
            <li>Guarda tus códigos de recuperación en un lugar seguro</li>
          </ol>
          <p><strong>¿Cómo funciona?</strong></p>
          <p>Una vez activada, cada vez que inicies sesión desde un nuevo dispositivo o navegador, después de introducir tu contraseña, se te pedirá un código adicional que recibirás a través del método que hayas elegido.</p>
          <p><strong>Importante:</strong> Si pierdes el acceso a tu método de verificación (por ejemplo, cambias de teléfono), necesitarás los códigos de recuperación para acceder a tu cuenta. Guárdalos en un lugar seguro.</p>
        `,
        links: [
          { text: 'Aplicaciones de autenticación recomendadas', url: '#' }
        ]
      },
      {
        question: '¿Qué hago si olvidé mi contraseña?',
        answer: `
          <p>Si has olvidado tu contraseña, puedes restablecerla siguiendo estos pasos:</p>
          <ol>
            <li>En la página de inicio de sesión, haz clic en "¿Olvidaste tu contraseña?"</li>
            <li>Introduce la dirección de correo electrónico asociada a tu cuenta</li>
            <li>Haz clic en "Enviar enlace de restablecimiento"</li>
            <li>Recibirás un correo electrónico con un enlace para restablecer tu contraseña</li>
            <li>Haz clic en el enlace (válido por 24 horas)</li>
            <li>Crea una nueva contraseña segura</li>
            <li>Inicia sesión con tu nueva contraseña</li>
          </ol>
          <p><strong>Si no recibes el correo electrónico:</strong></p>
          <ul>
            <li>Revisa tu carpeta de spam o correo no deseado</li>
            <li>Verifica que has introducido la dirección de correo correcta</li>
            <li>Espera unos minutos, a veces hay retrasos en la entrega</li>
            <li>Intenta el proceso nuevamente</li>
          </ul>
          <p><strong>Si sigues teniendo problemas:</strong></p>
          <p>Contacta con nuestro equipo de soporte proporcionando detalles que puedan verificar tu identidad, como la dirección de correo alternativa, el número de teléfono asociado a la cuenta o detalles sobre tus propiedades.</p>
        `,
        links: [
          { text: 'Contactar con soporte para problemas de acceso', url: '#' }
        ]
      },
      {
        question: '¿Cómo proteger mi cuenta de accesos no autorizados?',
        answer: `
          <p>Proteger tu cuenta es fundamental para la seguridad de tus datos y propiedades. Sigue estas recomendaciones:</p>
          <ol>
            <li><strong>Utiliza una contraseña fuerte y única:</strong> Combina mayúsculas, minúsculas, números y símbolos</li>
            <li><strong>Activa la verificación en dos pasos:</strong> Añade una capa extra de seguridad</li>
            <li><strong>Mantén actualizada tu información de contacto:</strong> Especialmente tu email y número de teléfono</li>
            <li><strong>Revisa regularmente la actividad de tu cuenta:</strong> Verifica inicios de sesión y cambios</li>
            <li><strong>No compartas tus credenciales:</strong> Ni siquiera con familiares o colaboradores (usa la función de acceso para colaboradores)</li>
            <li><strong>Cierra sesión en dispositivos compartidos:</strong> No dejes tu cuenta abierta en ordenadores públicos</li>
            <li><strong>Mantén actualizado tu navegador y sistema operativo:</strong> Las actualizaciones suelen incluir parches de seguridad</li>
          </ol>
          <p><strong>Señales de alerta:</strong></p>
          <ul>
            <li>Emails de confirmación de cambios que tú no has realizado</li>
            <li>Notificaciones de inicio de sesión desde ubicaciones desconocidas</li>
            <li>Cambios en tus reservas o propiedades que no reconoces</li>
            <li>Mensajes enviados desde tu cuenta que tú no has escrito</li>
          </ul>
          <p>Si detectas alguna actividad sospechosa, cambia inmediatamente tu contraseña y contacta con nuestro equipo de soporte.</p>
        `,
        links: [
          { text: 'Centro de seguridad', url: '#' },
          { text: 'Cómo reportar actividad sospechosa', url: '#' }
        ]
      }
    ]
  },
  {
    name: 'Configuración',
    icon: SettingsIcon,
    faqs: [
      {
        question: '¿Cómo configurar notificaciones?',
        answer: `
          <p>Personaliza tus notificaciones para mantenerte informado sin sentirte abrumado:</p>
          <ol>
            <li>Ve a "Configuración" en el menú desplegable de tu perfil</li>
            <li>Selecciona la pestaña "Notificaciones"</li>
            <li>Aquí puedes configurar:
              <ul>
                <li><strong>Canales de notificación:</strong> Email, SMS, notificaciones push</li>
                <li><strong>Tipos de notificaciones:</strong> Reservas, mensajes, pagos, etc.</li>
                <li><strong>Frecuencia:</strong> Inmediata, resumen diario, resumen semanal</li>
              </ul>
            </li>
            <li>Personaliza cada tipo de notificación según tus preferencias</li>
            <li>Guarda los cambios</li>
          </ol>
          <p><strong>Notificaciones recomendadas:</strong></p>
          <ul>
            <li><strong>Inmediatas:</strong> Nuevas reservas, cancelaciones, mensajes de huéspedes</li>
            <li><strong>Diarias:</strong> Recordatorios de check-in/check-out, resumen de actividad</li>
            <li><strong>Semanales:</strong> Informes de rendimiento, estadísticas</li>
          </ul>
          <p>Puedes desactivar temporalmente todas las notificaciones con el modo "No molestar" si necesitas un descanso.</p>
        `,
        links: [
          { text: 'Configuración óptima de notificaciones', url: '#' }
        ]
      },
      {
        question: '¿Cómo añadir usuarios adicionales a mi cuenta?',
        answer: `
          <p>Si necesitas que otras personas te ayuden a gestionar tus propiedades, puedes añadir usuarios adicionales con diferentes niveles de acceso:</p>
          <ol>
            <li>Ve a "Configuración" > "Equipo"</li>
            <li>Haz clic en "Añadir miembro"</li>
            <li>Introduce el email de la persona</li>
            <li>Selecciona el nivel de acceso:
              <ul>
                <li><strong>Administrador:</strong> Acceso completo a todas las funciones</li>
                <li><strong>Gestor de propiedades:</strong> Puede gestionar propiedades y reservas, pero no tiene acceso a la información financiera</li>
                <li><strong>Asistente:</strong> Puede responder mensajes y gestionar el calendario, pero no puede modificar propiedades ni aceptar reservas</li>
                <li><strong>Contable:</strong> Solo tiene acceso a la información financiera y facturas</li>
              </ul>
            </li>
            <li>Selecciona las propiedades específicas a las que tendrá acceso (o todas)</li>
            <li>Haz clic en "Enviar invitación"</li>
          </ol>
          <p>La persona recibirá un email con instrucciones para crear su cuenta o vincularla si ya tiene una.</p>
          <p><strong>Consideraciones importantes:</strong></p>
          <ul>
            <li>Puedes modificar o revocar el acceso en cualquier momento</li>
            <li>Cada acción realizada por un usuario adicional queda registrada</li>
            <li>El propietario principal sigue siendo responsable de todas las actividades</li>
            <li>Hay un límite de 5 usuarios adicionales en cuentas estándar (más en cuentas premium)</li>
          </ul>
        `,
        links: [
          { text: 'Mejores prácticas para gestión de equipos', url: '#' }
        ]
      },
      {
        question: '¿Cómo cambiar el idioma de la plataforma?',
        answer: `
          <p>Puedes cambiar el idioma de la interfaz en cualquier momento:</p>
          <ol>
            <li>Haz clic en tu perfil en la esquina superior derecha</li>
            <li>Selecciona "Configuración"</li>
            <li>Ve a la pestaña "Preferencias"</li>
            <li>En la sección "Idioma", selecciona tu idioma preferido del menú desplegable</li>
            <li>Haz clic en "Guardar cambios"</li>
          </ol>
          <p>La plataforma está disponible en los siguientes idiomas:</p>
          <ul>
            <li>Español</li>
            <li>Inglés</li>
            <li>Francés</li>
            <li>Alemán</li>
            <li>Italiano</li>
            <li>Portugués</li>
            <li>Ruso</li>
            <li>Chino (Simplificado)</li>
            <li>Japonés</li>
            <li>Árabe</li>
          </ul>
          <p><strong>Nota:</strong> Cambiar el idioma de la plataforma no afecta al idioma en que se muestran tus propiedades a los huéspedes. Para gestionar los idiomas de tus listados, ve a la sección "Propiedades" > "Editar" > "Traducciones".</p>
        `,
        links: [
          { text: 'Cómo crear listados multilingües', url: '#' }
        ]
      },
      {
        question: '¿Cómo vincular mi calendario con otras plataformas?',
        answer: `
          <p>Puedes sincronizar tu calendario con otras plataformas de alquiler para evitar reservas duplicadas:</p>
          <ol>
            <li>Ve a "Configuración" > "Integraciones"</li>
            <li>En la sección "Sincronización de calendario", haz clic en "Añadir integración"</li>
            <li>Selecciona la plataforma que deseas vincular (Airbnb, Booking.com, etc.) o elige "Otro" para una URL de iCal genérica</li>
            <li>Sigue las instrucciones específicas para cada plataforma:
              <ul>
                <li><strong>Para importar calendarios externos:</strong> Copia la URL de iCal de la otra plataforma y pégala en nuestra plataforma</li>
                <li><strong>Para exportar tu calendario:</strong> Copia la URL de iCal que te proporcionamos y pégala en la otra plataforma</li>
              </ul>
            </li>
            <li>Configura la frecuencia de sincronización (recomendamos cada hora)</li>
            <li>Guarda los cambios</li>
          </ol>
          <p><strong>Limitaciones a tener en cuenta:</strong></p>
          <ul>
            <li>La sincronización solo afecta a la disponibilidad, no a los detalles de las reservas</li>
            <li>Puede haber un retraso de hasta 1 hora en la actualización</li>
            <li>Las modificaciones o cancelaciones pueden no sincronizarse inmediatamente</li>
            <li>Es recomendable mantener un margen de seguridad de 1 día entre reservas</li>
          </ul>
          <p>Para una gestión más avanzada, considera utilizar un channel manager especializado que ofrezca sincronización en tiempo real.</p>
        `,
        links: [
          { text: 'Channel managers compatibles', url: '#' },
          { text: 'Solución de problemas de sincronización', url: '#' }
        ]
      }
    ]
  },
  {
    name: 'Soporte técnico',
    icon: HelpCircleIcon,
    faqs: [
      {
        question: '¿Qué hago si la plataforma no funciona correctamente?',
        answer: `
          <p>Si experimentas problemas técnicos con la plataforma, sigue estos pasos para resolverlos:</p>
          <ol>
            <li><strong>Soluciones básicas:</strong>
              <ul>
                <li>Actualiza la página (F5 o Ctrl+R)</li>
                <li>Borra la caché del navegador</li>
                <li>Intenta con otro navegador (Chrome, Firefox, Safari)</li>
                <li>Verifica tu conexión a internet</li>
              </ul>
            </li>
            <li><strong>Problemas específicos:</strong>
              <ul>
                <li>Si no puedes subir fotos: verifica el tamaño y formato de los archivos</li>
                <li>Si el calendario no se actualiza: espera unos minutos y vuelve a intentarlo</li>
                <li>Si los pagos no se procesan: verifica tus datos bancarios</li>
              </ul>
            </li>
            <li><strong>Comprobar el estado del sistema:</strong> Visita nuestra página de estado en status.rayitodesol.es para ver si hay problemas conocidos</li>
          </ol>
          <p>Si el problema persiste:</p>
          <ol>
            <li>Toma capturas de pantalla del error</li>
            <li>Anota los pasos exactos que realizaste antes del error</li>
            <li>Contacta con soporte técnico proporcionando esta información</li>
          </ol>
          <p>Nuestro equipo de soporte técnico está disponible 24/7 y responderá a tu solicitud lo antes posible.</p>
        `,
        links: [
          { text: 'Estado del sistema', url: '#' },
          { text: 'Guía de solución de problemas comunes', url: '#' }
        ]
      },
      {
        question: '¿Cómo puedo reportar un error en la plataforma?',
        answer: `
          <p>Si encuentras un error o fallo en la plataforma, tu reporte nos ayuda a mejorar. Para reportar un error:</p>
          <ol>
            <li>Haz clic en el botón "Ayuda" en la parte inferior de cualquier página</li>
            <li>Selecciona "Reportar un problema"</li>
            <li>Completa el formulario con la siguiente información:
              <ul>
                <li>Descripción detallada del error</li>
                <li>Pasos para reproducir el problema</li>
                <li>URL de la página donde ocurrió</li>
                <li>Dispositivo y navegador que estabas utilizando</li>
                <li>Capturas de pantalla (si es posible)</li>
              </ul>
            </li>
            <li>Envía el reporte</li>
          </ol>
          <p>También puedes reportar errores directamente a través de:</p>
          <ul>
            <li>Email: soporte@rayitodesol.es</li>
            <li>Chat en vivo: disponible en horario laboral</li>
            <li>Teléfono: +34 900 123 456</li>
          </ul>
          <p><strong>¿Qué ocurre después?</strong></p>
          <p>Recibirás un número de ticket para seguimiento. Nuestro equipo técnico analizará el problema y te informará cuando esté resuelto. Los errores críticos se priorizan y se solucionan lo antes posible.</p>
        `,
        links: [
          { text: 'Estado de tickets reportados', url: '#' }
        ]
      },
      {
        question: '¿La plataforma funciona en todos los dispositivos?',
        answer: `
          <p>Nuestra plataforma está diseñada para funcionar en una amplia variedad de dispositivos y navegadores:</p>
          <ul>
            <li><strong>Ordenadores:</strong> Windows, macOS, Linux</li>
            <li><strong>Dispositivos móviles:</strong> iOS (iPhone/iPad), Android</li>
            <li><strong>Navegadores compatibles:</strong>
              <ul>
                <li>Google Chrome (recomendado) - versión 60 o superior</li>
                <li>Mozilla Firefox - versión 60 o superior</li>
                <li>Safari - versión 12 o superior</li>
                <li>Microsoft Edge - versión 79 o superior</li>
                <li>Opera - versión 60 o superior</li>
              </ul>
            </li>
          </ul>
          <p><strong>Aplicaciones móviles:</strong></p>
          <ul>
            <li>Disponibles para iOS y Android</li>
            <li>Ofrecen funcionalidades similares a la versión web</li>
            <li>Incluyen notificaciones push para alertas importantes</li>
            <li>Permiten gestionar reservas y mensajes en movimiento</li>
          </ul>
          <p><strong>Limitaciones conocidas:</strong></p>
          <ul>
            <li>Internet Explorer no es compatible</li>
            <li>Algunas funciones avanzadas de gestión solo están disponibles en la versión de escritorio</li>
            <li>Para la mejor experiencia en dispositivos móviles, recomendamos usar nuestra app en lugar del navegador</li>
          </ul>
          <p>Trabajamos constantemente para mejorar la compatibilidad y rendimiento en todos los dispositivos.</p>
        `,
        links: [
          { text: 'Descargar app para iOS', url: '#' },
          { text: 'Descargar app para Android', url: '#' }
        ]
      },
      {
        question: '¿Cómo puedo sugerir nuevas funcionalidades?',
        answer: `
          <p>Valoramos tus ideas para mejorar la plataforma. Para sugerir nuevas funcionalidades:</p>
          <ol>
            <li>Ve a "Configuración" > "Comentarios y sugerencias"</li>
            <li>Haz clic en "Sugerir una funcionalidad"</li>
            <li>Completa el formulario con:
              <ul>
                <li>Título descriptivo de la funcionalidad</li>
                <li>Descripción detallada de cómo funcionaría</li>
                <li>Explicación de por qué sería útil</li>
                <li>Ejemplos de uso (opcional)</li>
              </ul>
            </li>
            <li>Envía tu sugerencia</li>
          </ol>
          <p>Alternativamente, puedes enviar tus ideas a feedback@rayitodesol.es</p>
          <p><strong>¿Qué ocurre con las sugerencias?</strong></p>
          <ul>
            <li>Todas las sugerencias son revisadas por nuestro equipo de producto</li>
            <li>Las ideas populares se someten a votación en nuestra comunidad</li>
            <li>Las funcionalidades seleccionadas se añaden a nuestra hoja de ruta</li>
            <li>Si tu sugerencia es implementada, recibirás una notificación</li>
          </ul>
          <p>También puedes unirte a nuestro programa de beta testers para probar nuevas funcionalidades antes de su lanzamiento oficial.</p>
        `,
        links: [
          { text: 'Programa de beta testers', url: '#' },
          { text: 'Hoja de ruta de producto', url: '#' }
        ]
      }
    ]
  },
  {
    name: 'Políticas y términos',
    icon: UserIcon,
    faqs: [
      {
        question: '¿Cuáles son las políticas de cancelación disponibles?',
        answer: `
          <p>Ofrecemos varias políticas de cancelación que puedes elegir para tus propiedades:</p>
          <ol>
            <li><strong>Flexible (recomendada para empezar):</strong>
              <ul>
                <li>Reembolso completo si se cancela 24 horas antes de la llegada</li>
                <li>Reembolso del 50% de la primera noche si se cancela menos de 24 horas antes</li>
                <li>Reembolso del 50% de las noches restantes si el huésped se va antes</li>
              </ul>
            </li>
            <li><strong>Moderada:</strong>
              <ul>
                <li>Reembolso completo si se cancela 5 días antes de la llegada</li>
                <li>Reembolso del 50% si se cancela menos de 5 días antes</li>
                <li>Sin reembolso si se cancela menos de 24 horas antes</li>
              </ul>
            </li>
            <li><strong>Estricta:</strong>
              <ul>
                <li>Reembolso del 50% si se cancela 7 días antes de la llegada</li>
                <li>Sin reembolso si se cancela menos de 7 días antes</li>
              </ul>
            </li>
            <li><strong>Personalizada:</strong>
              <ul>
                <li>Disponible para anfitriones con más de 10 propiedades</li>
                <li>Contacta con soporte para configurar una política a medida</li>
              </ul>
            </li>
          </ol>
          <p>Para establecer o cambiar la política de cancelación:</p>
          <ol>
            <li>Ve a "Propiedades" y selecciona la propiedad</li>
            <li>Haz clic en "Editar"</li>
            <li>Ve a la sección "Políticas"</li>
            <li>Selecciona la política deseada</li>
            <li>Guarda los cambios</li>
          </ol>
          <p><strong>Nota importante:</strong> Los cambios en la política de cancelación solo afectarán a las nuevas reservas, no a las existentes.</p>
        `,
        links: [
          { text: 'Comparativa detallada de políticas de cancelación', url: '#' }
        ]
      },
      {
        question: '¿Qué incluye la protección para anfitriones?',
        answer: `
          <p>Nuestra protección para anfitriones te ofrece tranquilidad al alquilar tu propiedad:</p>
          <ul>
            <li><strong>Garantía de daños:</strong> Hasta 10.000€ de cobertura por daños causados por huéspedes</li>
            <li><strong>Seguro de responsabilidad civil:</strong> Hasta 1.000.000€ de cobertura por reclamaciones de terceros</li>
            <li><strong>Protección contra cancelaciones:</strong> Compensación parcial por cancelaciones de última hora</li>
            <li><strong>Asistencia 24/7:</strong> Línea directa para emergencias</li>
          </ul>
          <p><strong>¿Qué cubre la garantía de daños?</strong></p>
          <ul>
            <li>Daños a la propiedad y sus contenidos</li>
            <li>Robo de objetos documentados en el inventario</li>
            <li>Limpieza extraordinaria en caso de daños graves</li>
            <li>Pérdida de ingresos durante reparaciones necesarias</li>
          </ul>
          <p><strong>¿Qué no está cubierto?</strong></p>
          <ul>
            <li>Desgaste normal</li>
            <li>Objetos de alto valor no declarados previamente</li>
            <li>Daños causados por negligencia del anfitrión</li>
            <li>Problemas preexistentes</li>
          </ul>
          <p><strong>Cómo presentar una reclamación:</strong></p>
          <ol>
            <li>Documenta los daños con fotos</li>
            <li>Contacta con el huésped para informarle</li>
            <li>Inicia la reclamación en las 48 horas siguientes al check-out</li>
            <li>Proporciona toda la documentación solicitada</li>
            <li>Nuestro equipo evaluará la reclamación en un plazo de 7 días</li>
          </ol>
        `,
        links: [
          { text: 'Términos completos de la protección', url: '#' },
          { text: 'Cómo documentar tu propiedad para mayor protección', url: '#' }
        ]
      },
      {
        question: '¿Cuáles son mis responsabilidades como anfitrión?',
        answer: `
          <p>Como anfitrión en nuestra plataforma, tienes ciertas responsabilidades que debes cumplir:</p>
          <ol>
            <li><strong>Precisión en la información:</strong>
              <ul>
                <li>Proporcionar descripciones precisas y actualizadas de tu propiedad</li>
                <li>Mostrar fotos reales y recientes</li>
                <li>Listar correctamente todas las amenidades disponibles</li>
                <li>Mantener el calendario de disponibilidad actualizado</li>
              </ul>
            </li>
            <li><strong>Comunicación:</strong>
              <ul>
                <li>Responder a las consultas en un plazo de 24 horas</li>
                <li>Proporcionar instrucciones claras para el check-in</li>
                <li>Estar disponible durante la estancia para resolver problemas</li>
                <li>Comunicar cualquier regla especial antes de la reserva</li>
              </ul>
            </li>
            <li><strong>Calidad y limpieza:</strong>
              <ul>
                <li>Asegurar que la propiedad esté limpia y en buen estado</li>
                <li>Proporcionar los servicios básicos prometidos (agua caliente, calefacción, etc.)</li>
                <li>Mantener los estándares de seguridad (detectores de humo, extintores, etc.)</li>
              </ul>
            </li>
            <li><strong>Cumplimiento legal:</strong>
              <ul>
                <li>Cumplir con las leyes y regulaciones locales sobre alquileres turísticos</li>
                <li>Obtener los permisos y licencias necesarios</li>
                <li>Declarar los ingresos según la normativa fiscal aplicable</li>
                <li>Respetar las normas de la comunidad de vecinos</li>
              </ul>
            </li>
          </ol>
          <p><strong>Consecuencias del incumplimiento:</strong></p>
          <p>El incumplimiento de estas responsabilidades puede resultar en:</p>
          <ul>
            <li>Cancelación de reservas sin compensación</li>
            <li>Penalizaciones en el ranking de búsqueda</li>
            <li>Suspensión temporal de la cuenta</li>
            <li>En casos graves, eliminación permanente de la plataforma</li>
          </ul>
          <p>Cumplir con estas responsabilidades no solo es obligatorio, sino que también contribuye a mejores valoraciones y más reservas.</p>
        `,
        links: [
          { text: 'Guía completa para anfitriones responsables', url: '#' },
          { text: 'Normativa legal por comunidades autónomas', url: '#' }
        ]
      },
      {
        question: '¿Qué normativa debo cumplir para alquilar mi propiedad?',
        answer: `
          <p>La normativa para alquileres turísticos varía según la ubicación, pero aquí tienes una guía general:</p>
          <ol>
            <li><strong>Licencias y permisos:</strong>
              <ul>
                <li>La mayoría de comunidades autónomas exigen una licencia de vivienda turística</li>
                <li>Algunas ciudades tienen moratorias o restricciones en ciertas zonas</li>
                <li>Puede ser necesario registrarse en el registro de turismo local</li>
              </ul>
            </li>
            <li><strong>Requisitos de la propiedad:</strong>
              <ul>
                <li>Cumplir con normativas de seguridad (extintores, salidas de emergencia, etc.)</li>
                <li>Proporcionar hojas de registro para los huéspedes</li>
                <li>Mostrar visiblemente la capacidad máxima y normas</li>
                <li>En algunos casos, tener un seguro específico</li>
              </ul>
            </li>
            <li><strong>Obligaciones fiscales:</strong>
              <ul>
                <li>Declarar los ingresos en el IRPF (modelo 100)</li>
                <li>Posible alta en actividades económicas (modelo 036/037)</li>
                <li>Emitir facturas a los huéspedes</li>
                <li>En algunos casos, cobrar y liquidar la tasa turística</li>
              </ul>
            </li>
            <li><strong>Comunidad de vecinos:</strong>
              <ul>
                <li>Verificar que los estatutos permiten alquileres de corta estancia</li>
                <li>En algunos casos, se requiere aprobación de la comunidad</li>
              </ul>
            </li>
          </ol>
          <p><strong>Recursos para cumplir con la normativa:</strong></p>
          <ul>
            <li>Consulta la web oficial de turismo de tu comunidad autónoma</li>
            <li>Contacta con el ayuntamiento local para información específica</li>
            <li>Considera contratar un asesor especializado en alquileres turísticos</li>
            <li>Nuestra plataforma ofrece guías actualizadas por región</li>
          </ul>
          <p><strong>Nota importante:</strong> El incumplimiento de la normativa puede resultar en multas significativas (desde 1.000€ hasta 600.000€ en casos graves). Es tu responsabilidad como anfitrión asegurarte de cumplir con todas las regulaciones aplicables.</p>
        `,
        links: [
          { text: 'Guía de normativa por comunidades autónomas', url: '#' },
          { text: 'Consultoría legal para anfitriones', url: '#' }
        ]
      }
    ]
  }
]);

// Categorías filtradas por búsqueda
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value;
  
  return categories.value.map(category => {
    // Crear una copia de la categoría para no modificar la original
    const filteredCategory = { ...category };
    
    // Filtrar las FAQs que coinciden con la búsqueda
    filteredCategory.faqs = category.faqs.filter(faq => 
      faq.question.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      faq.answer.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
    
    return filteredCategory;
  }).filter(category => category.faqs.length > 0);
});

// Métodos
const toggleFaq = (index) => {
  const position = expandedFaqs.value.indexOf(index);
  if (position !== -1) {
    expandedFaqs.value.splice(position, 1);
  } else {
    expandedFaqs.value.push(index);
  }
};

const filterCategories = () => {
  // Si hay una búsqueda activa, expandir automáticamente todas las FAQs que coinciden
  if (searchQuery.value) {
    expandedFaqs.value = [];
    filteredCategories.value.forEach((category, categoryIndex) => {
      if (categoryIndex === 0) {
        activeCategory.value = 0;
      }
      category.faqs.forEach((_, faqIndex) => {
        if (!expandedFaqs.value.includes(faqIndex)) {
          expandedFaqs.value.push(faqIndex);
        }
      });
    });
  }
};

const goToRelatedQuestion = (categoryIndex, questionIndex) => {
  activeCategory.value = categoryIndex;
  // Esperar a que se actualice el DOM con las nuevas FAQs
  setTimeout(() => {
    expandedFaqs.value = [questionIndex];
    // Desplazarse a la pregunta
    const element = document.querySelectorAll('.faq-item')[questionIndex];
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }, 100);
};

const provideFeedback = (faqIndex, isHelpful) => {
  console.log(`Feedback para FAQ #${faqIndex}: ${isHelpful ? 'Útil' : 'No útil'}`);
  // Aquí se enviaría el feedback al servidor
  alert(`Gracias por tu feedback. ${isHelpful ? '¡Nos alegra haber podido ayudarte!' : 'Trabajaremos para mejorar esta respuesta.'}`);
};

const submitContactForm = () => {
  isSubmitting.value = true;
  
  // Simulación de envío del formulario
  setTimeout(() => {
    isSubmitting.value = false;
    showContactModal.value = false;
    alert('Tu mensaje ha sido enviado. Nuestro equipo de soporte se pondrá en contacto contigo lo antes posible.');
    
    // Resetear el formulario
    contactForm.value = {
      subject: '',
      message: '',
      email: '',
      urgent: false
    };
  }, 1500);
};
</script>

<style scoped>
.help-support-section {
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

.help-support-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 150%, rgba(0, 113, 194, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% -50%, rgba(0, 53, 128, 0.03) 0%, transparent 60%);
  z-index: 0;
}

.section-header {
  position: relative;
  z-index: 1;
  text-align: center;
  margin-bottom: 1rem;
}

.section-title {
  font-size: 2rem;
  color: #003580;
  margin: 0 0 0.5rem;
  position: relative;
  display: inline-block;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background: linear-gradient(90deg, #0071c2, #003580);
  border-radius: 3px;
}

.section-description {
  color: #64748b;
  max-width: 600px;
  margin: 1rem auto 0;
}

.help-container {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
  position: relative;
  z-index: 1;
}

/* Sidebar */
.help-sidebar {
  background-color: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.search-box {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #0071c2;
  width: 18px;
  height: 18px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s;
}

.search-input:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.sidebar-title {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.category-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.category-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  color: #1e293b;
  position: relative;
}

.category-item:hover {
  background-color: #f0f7ff;
}

.category-item.active {
  background-color: #e6f0ff;
  color: #0071c2;
  font-weight: 500;
}

.category-icon {
  width: 20px;
  height: 20px;
  margin-right: 0.75rem;
  color: #0071c2;
}

.arrow-icon {
  position: absolute;
  right: 1rem;
  width: 16px;
  height: 16px;
  color: #94a3b8;
  opacity: 0;
  transition: all 0.3s;
}

.category-item:hover .arrow-icon {
  opacity: 1;
}

.category-item.active .arrow-icon {
  opacity: 1;
  color: #0071c2;
}

.contact-support {
  background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
  border-radius: 12px;
  padding: 1.5rem;
  text-align: center;
  border: 1px solid #e6f0ff;
}

.contact-support h3 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0 0 0.75rem;
}

.contact-support p {
  color: #64748b;
  margin: 0 0 1.25rem;
  font-size: 0.95rem;
}

.contact-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.contact-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Content */
.help-content {
  background-color: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
}

.category-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e6f0ff;
}

.header-icon {
  width: 32px;
  height: 32px;
  color: #0071c2;
}

.category-header h2 {
  font-size: 1.5rem;
  color: #003580;
  margin: 0;
}

.faq-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.faq-item {
  border: 1px solid #e6f0ff;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s;
}

.faq-item.expanded {
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.05);
}

.faq-question {
  padding: 1.25rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  background-color: #f8fafc;
  transition: all 0.3s;
}

.faq-item.expanded .faq-question {
  background-color: #e6f0ff;
}

.faq-question:hover {
  background-color: #f0f7ff;
}

.faq-question h3 {
  font-size: 1.1rem;
  color: #1e293b;
  margin: 0;
  font-weight: 500;
  transition: all 0.3s;
}

.faq-item.expanded .faq-question h3 {
  color: #0071c2;
}

.question-icon {
  width: 20px;
  height: 20px;
  color: #94a3b8;
  transition: all 0.3s;
}

.question-icon.rotated {
  transform: rotate(180deg);
  color: #0071c2;
}

.faq-answer {
  padding: 0 1.25rem 1.25rem;
  color: #1e293b;
  line-height: 1.6;
}

.faq-answer p {
  margin: 0 0 1rem;
}

.faq-answer ul, .faq-answer ol {
  margin: 0 0 1rem;
  padding-left: 1.5rem;
}

.faq-answer li {
  margin-bottom: 0.5rem;
}

.faq-answer strong {
  color: #0071c2;
  font-weight: 600;
}

.faq-links {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid #e6f0ff;
}

.faq-links h4, .related-questions h4 {
  font-size: 1rem;
  color: #003580;
  margin: 0 0 0.75rem;
}

.faq-links ul, .related-questions ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.faq-links li, .related-questions li {
  margin-bottom: 0.5rem;
}

.help-link, .related-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #0071c2;
  text-decoration: none;
  transition: all 0.3s;
}

.help-link:hover, .related-link:hover {
  color: #003580;
  text-decoration: underline;
}

.link-icon {
  width: 14px;
  height: 14px;
}

.related-questions {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid #e6f0ff;
}

.feedback-section {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid #e6f0ff;
  text-align: center;
}

.feedback-section p {
  color: #64748b;
  margin-bottom: 0.75rem;
}

.feedback-buttons {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.feedback-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  background-color: white;
  color: #1e293b;
  cursor: pointer;
  transition: all 0.3s;
}

.feedback-button:hover {
  background-color: #f0f7ff;
  border-color: #0071c2;
}

.feedback-icon {
  width: 16px;
  height: 16px;
}

/* Tablas en las respuestas */
.fee-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
}

.fee-table th, .fee-table td {
  padding: 0.75rem;
  text-align: left;
  border: 1px solid #e6f0ff;
}

.fee-table th {
  background-color: #f0f7ff;
  color: #003580;
  font-weight: 600;
}

.fee-table tr:nth-child(even) {
  background-color: #f8fafc;
}

/* Modal de contacto */
.contact-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 53, 128, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.contact-modal {
  background-color: white;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
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

.modal-header h3 {
  font-size: 1.4rem;
  margin: 0;
  font-weight: 600;
}

.close-button {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
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
  width: 18px;
  height: 18px;
}

.modal-body {
  padding: 2rem;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #1e293b;
}

.form-input, .form-textarea {
  padding: 0.875rem 1rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
}

.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

.checkbox-group {
  flex-direction: row;
  align-items: center;
  gap: 0.75rem;
}

.checkbox-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #0071c2;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-button {
  padding: 0.75rem 1.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  background-color: white;
  color: #64748b;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.cancel-button:hover {
  background-color: #f8fafc;
  color: #1e293b;
}

.submit-button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.15);
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 992px) {
  .help-container {
    grid-template-columns: 250px 1fr;
  }
}

@media (max-width: 768px) {
  .help-support-section {
    padding: 1.5rem;
  }
  
  .help-container {
    grid-template-columns: 1fr;
  }
  
  .help-sidebar {
    position: static;
    margin-bottom: 1.5rem;
  }
  
  .category-header {
    flex-direction: column;
    align-items: flex-start;
    text-align: center;
  }
  
  .header-icon {
    margin-bottom: 0.5rem;
  }
  
  .feedback-buttons {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .feedback-button {
    width: 100%;
    justify-content: center;
  }
  
  .form-actions {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .cancel-button, .submit-button {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .help-support-section {
    padding: 1rem;
  }
  
  .section-title {
    font-size: 1.5rem;
  }
  
  .modal-body {
    padding: 1.5rem;
  }
}
</style>