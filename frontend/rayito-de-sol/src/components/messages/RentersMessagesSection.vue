<template>
  <div class="messages-section">
    <div class="section-header">
      <h2 class="section-title">Centro de Mensajes</h2>
      <div class="header-actions">
        <button class="compose-button" @click="openComposeModal">
          <PenSquareIcon class="button-icon" />
          Nuevo Mensaje
        </button>
      </div>
    </div>
    <div class="messages-container">
      <div class="messages-sidebar">
        <div class="search-container">
          <div class="search-input">
            <SearchIcon class="search-icon" />
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Buscar mensajes..."
              class="search-field"
            />
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              class="clear-search"
            >
              <XIcon class="clear-icon" />
            </button>
          </div>
        </div>
        <div class="conversation-filters">
          <button
            v-for="filter in conversationFilters"
            :key="filter.id"
            class="filter-button"
            :class="{ active: activeFilter === filter.id }"
            @click="activeFilter = filter.id"
          >
            <component :is="filter.icon" class="filter-icon" />
            <span>{{ filter.name }}</span>
            <span class="filter-count">{{ getFilterCount(filter.id) }}</span>
          </button>
        </div>
        <div class="conversations-list">
          <div
            v-for="conversation in filteredConversations"
            :key="conversation.id"
            class="conversation-item"
            :class="{
              active: activeConversation === conversation.id,
              unread: conversation.unread,
            }"
            @click="selectConversation(conversation.id)"
          >
            <div class="conversation-avatar">
              <img
                v-if="conversation.avatar"
                :src="conversation.avatar"
                :alt="conversation.name"
                class="avatar-image"
              />
              <div v-else class="avatar-placeholder">
                {{ getInitials(conversation.name) }}
              </div>
              <div
                v-if="isOnline(getOtherUserId(conversation))"
                class="online-indicator"
              ></div>
            </div>
            <div class="conversation-content">
              <div class="conversation-header">
                <h3 class="conversation-name">{{ conversation.name }}</h3>
                <span class="conversation-time">{{
                  formatMessageTime(conversation.lastMessage.timestamp)
                }}</span>
              </div>
              <div class="conversation-property">
                <HomeIcon class="property-icon" />
                <span>{{ conversation.property?.name }}</span>
              </div>
              <p class="conversation-preview">
                <span v-if="conversation.lastMessage.isOwner">Tú: </span>
                {{ conversation.lastMessage.text }}
              </p>
            </div>
          </div>
          <div
            v-if="filteredConversations.length === 0"
            class="empty-conversations"
          >
            <MessageSquareOffIcon class="empty-icon" />
            <p>No hay conversaciones que coincidan con tu búsqueda</p>
          </div>
        </div>
      </div>
      <div class="messages-content">
        <div v-if="activeConversation" class="conversation-view">
          <div class="conversation-header">
            <div class="conversation-user">
              <div class="user-avatar">
                <img
                  v-if="currentConversation.avatar"
                  :src="currentConversation.avatar"
                  :alt="currentConversation.name"
                  class="avatar-image"
                />
                <div v-else class="avatar-placeholder">
                  {{ getInitials(currentConversation.name) }}
                </div>
                <div
                  v-if="isOnline(getOtherUserId(currentConversation))"
                  class="online-indicator"
                ></div>
              </div>
              <div class="user-info">
                <h3>{{ currentConversation.name }}</h3>
                <div class="user-status">
                  <span
                    v-if="isOnline(getOtherUserId(currentConversation))"
                    class="status online"
                    >En línea</span
                  >
                  <span v-else class="status offline"
                    >Última vez
                    {{ formatLastSeen(currentConversation.lastSeen) }}</span
                  >
                </div>
              </div>
            </div>
            <div class="conversation-actions">
<!--               <button class="action-button" @click="showBookingDetails">
                <CalendarIcon class="action-icon" />
                <span class="action-text">Ver Reserva</span>
              </button> -->
              <button class="action-button" @click="toggleInfoPanel">
                <InfoIcon class="action-icon" />
                <span class="action-text">Info</span>
              </button>
            </div>
          </div>
          <div class="messages-list" ref="messagesList">
            <div
              v-for="(message, index) in currentMessages"
              :key="message.id || index"
              class="message-group"
            >
              <div
                v-if="showDateDivider(message, currentMessages[index - 1])"
                class="date-divider"
              >
                <span>{{ formatMessageDate(message.timestamp) }}</span>
              </div>
              <div
                class="message-bubble"
                :class="{
                  owner: message.isOwner,
                  guest: !message.isOwner,
                  first: isFirstInGroup(message, currentMessages[index - 1]),
                  last: isLastInGroup(message, currentMessages[index + 1]),
                }"
              >
                <div class="message-content">{{ message.text }}</div>
                <div class="message-time">
                  {{ formatMessageTime(message.timestamp) }}
                  <span
                    v-if="message.isOwner"
                    class="message-status"
                    :class="message.status"
                  >
                    <CheckIcon
                      v-if="message.status === 'read'"
                      class="status-icon"
                    />
                    <CheckCheckIcon
                      v-else-if="message.status === 'delivered'"
                      class="status-icon"
                    />
                    <ClockIcon v-else class="status-icon" />
                  </span>
                </div>
              </div>
            </div>
            <div v-if="isTyping" class="typing-indicator">
              <div class="typing-bubble">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
              </div>
              <span>{{ currentConversation.name }} está escribiendo...</span>
            </div>
          </div>
          <div class="message-composer">
            <div class="composer-attachments" v-if="attachments.length > 0">
              <div
                v-for="(attachment, index) in attachments"
                :key="index"
                class="attachment-preview"
              >
                <img
                  v-if="attachment.type.startsWith('image/')"
                  :src="attachment.preview"
                  class="attachment-image"
                />
                <div v-else class="attachment-file">
                  <FileIcon class="file-icon" />
                  <span class="file-name">{{ attachment.name }}</span>
                </div>
                <button
                  class="remove-attachment"
                  @click="removeAttachment(index)"
                >
                  <XIcon class="remove-icon" />
                </button>
              </div>
            </div>
            <div class="composer-input-container">
              <button class="attachment-button" @click="triggerFileInput">
                <PaperclipIcon class="attachment-icon" />
                <input
                  type="file"
                  ref="fileInput"
                  @change="handleFileUpload"
                  multiple
                  style="display: none"
                />
              </button>
              <div class="composer-input">
                <textarea
                  v-model="newMessage"
                  placeholder="Escribe un mensaje..."
                  class="message-input"
                  @keydown.enter.prevent="sendMessageHandler"
                  @input="sendTyping"
                  ref="messageInput"
                  rows="1"
                ></textarea>
              </div>
              <button
                class="send-button"
                :disabled="
                  !(newMessage && newMessage.trim()) && attachments.length === 0
                "
                @click="sendMessageHandler"
              >
                <SendIcon class="send-icon" />
              </button>
            </div>
          </div>
        </div>
        <div v-else class="no-conversation-selected">
          <MessageSquareIcon class="empty-icon" />
          <h3>Selecciona una conversación</h3>
          <p>Elige una conversación de la lista para ver los mensajes</p>
        </div>
      </div>
      <div v-if="showInfoPanel && currentConversation" class="info-panel">
        <div class="info-panel-header">
          <h3>Información</h3>
          <button class="close-info-button" @click="toggleInfoPanel">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="info-panel-content">
          <div class="info-section">
            <h4>Detalles del Huésped</h4>
            <div class="info-item">
              <UserIcon class="info-icon" />
              <div>
                <p class="info-label">Nombre</p>
                <p class="info-value">{{ currentConversation.name }}</p>
              </div>
            </div>
            <div class="info-item">
              <MailIcon class="info-icon" />
              <div>
                <p class="info-label">Email</p>
                <p class="info-value">
                  {{ currentConversation.email || "No disponible" }}
                </p>
              </div>
            </div>
            <div class="info-item">
              <PhoneIcon class="info-icon" />
              <div>
                <p class="info-label">Teléfono</p>
                <p class="info-value">
                  {{ currentConversation.phone || "No disponible" }}
                </p>
              </div>
            </div>
          </div>
          <div class="info-section">
            <h4>Detalles de la Reserva</h4>
            <div class="info-item">
              <HomeIcon class="info-icon" />
              <div>
                <p class="info-label">Propiedad</p>
                <p class="info-value">
                  {{ currentConversation.property?.name || "No disponible" }}
                </p>
              </div>
            </div>
          </div>
          <div class="info-section">
            <h4>Acciones Rápidas</h4>
            <div class="quick-actions">
              <button class="quick-action-button">
                <CalendarIcon class="action-icon" />
                <span>Ver Calendario</span>
              </button>
              <button class="quick-action-button">
                <FileTextIcon class="action-icon" />
                <span>Enviar Instrucciones</span>
              </button>
              <button class="quick-action-button">
                <AlertTriangleIcon class="action-icon" />
                <span>Reportar Problema</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="showComposeModal"
      class="compose-modal"
      @click="closeComposeModal"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Nuevo Mensaje</h3>
          <button class="close-button" @click="closeComposeModal">
            <XIcon class="close-icon" />
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="sendNewMessage" class="compose-form">
            <div class="form-group">
              <label for="reservation">Reserva</label>
              <select
                id="reservation"
                v-model="newConversation.reservationId"
                class="form-input"
                required
              >
                <option value="" disabled>Selecciona una reserva</option>
                <option
                  v-for="res in reservations"
                  :key="res.id"
                  :value="res.id"
                >
                  {{ getPropertyName(res.property_id) }} -
                  {{ res.reservation_date }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="message">Mensaje</label>
              <textarea
                id="message"
                v-model="newConversation.message"
                class="form-textarea"
                rows="4"
                required
                placeholder="Escribe tu mensaje aquí..."
              ></textarea>
            </div>
            <div class="form-actions">
              <button
                type="button"
                class="cancel-button"
                @click="closeComposeModal"
              >
                Cancelar
              </button>
              <button type="submit" class="submit-button">
                Enviar Mensaje
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted, nextTick, watch } from "vue";
import {
  PenSquareIcon,
  SearchIcon,
  XIcon,
  MessageSquareIcon,
  MessageSquareOffIcon,
  InboxIcon,
  SendIcon,
  ArchiveIcon,
  StarIcon,
  AlertTriangleIcon,
  HomeIcon,
  UserIcon,
  CalendarIcon,
  InfoIcon,
  CheckIcon,
  CheckCheckIcon,
  ClockIcon,
  PaperclipIcon,
  FileIcon,
  MailIcon,
  PhoneIcon,
  UsersIcon,
  CreditCardIcon,
  FileTextIcon,
} from "lucide-vue-next";
import {
  getConversations,
  getMessages,
  sendMessage,
  createConversation,
  markAsRead,
} from "@/api/messages";
import { apiHeaders } from "@/../utils/api";
import { useMessaging } from "@/../componsables/useMessaging";

const props = defineProps({
  properties: { type: Array, default: () => [] },
  reservations: { type: Array, default: () => [] },
  userId: { type: Number, required: true },
});

const searchQuery = ref("");
const activeFilter = ref("all");
const activeConversation = ref(null);
const showInfoPanel = ref(false);
const isTyping = ref(false);
const attachments = ref([]);
const showComposeModal = ref(false);
const newConversation = ref({
  reservationId: "",
  message: "",
});

function toggleInfoPanel() {
  showInfoPanel.value = !showInfoPanel.value;
}

const messagesList = ref(null);
const fileInput = ref(null);

const conversationFilters = [
  { id: "all", name: "Todos", icon: MessageSquareIcon },
  { id: "unread", name: "No leídos", icon: InboxIcon },
  { id: "sent", name: "Enviados", icon: SendIcon },
  { id: "archived", name: "Archivados", icon: ArchiveIcon },
  { id: "starred", name: "Destacados", icon: StarIcon },
];

const conversations = ref([]);
const conversationMessages = ref([]);
const currentMessages = computed(() => {
  // Asegúrate de que siempre es un array y no null/undefined
  return Array.isArray(conversationMessages.value)
    ? conversationMessages.value
    : [];
});

function getOtherUserId(conversation) {
  if (!props.userId || !conversation) return null;
  if ("user_one_id" in conversation && "user_two_id" in conversation) {
    return conversation.user_one_id === props.userId
      ? conversation.user_two_id
      : conversation.user_one_id;
  }
  return null;
}

const {
  newMessage,
  typingUserId,
  onlineUsers,
  listenToConversation,
  leaveConversation,
  listenOnline,
  sendTyping,
} = useMessaging(props.userId);

async function selectConversation(conversationId) {
  console.log('🎯 Seleccionando conversación:', conversationId);
  
  // Si ya hay una conversación activa, desconectarse
  if (activeConversation.value && activeConversation.value !== conversationId) {
    console.log('🔌 Desconectando de conversación anterior:', activeConversation.value);
    leaveConversation(activeConversation.value);
  }
  
  activeConversation.value = conversationId;
  
  try {
    // Obtener mensajes
    const { data } = await getMessages(conversationId);
    if (Array.isArray(data)) {
      conversationMessages.value = data;
    } else if (Array.isArray(data.messages)) {
      conversationMessages.value = data.messages;
    } else {
      conversationMessages.value = [];
    }
    
    // Marcar como leído
    await markAsRead(conversationId);
    const c = conversations.value.find((c) => c.id === conversationId);
    if (c) c.unread = false;
    
  } catch (error) {
    console.error('Error cargando mensajes:', error);
  }
  
  // SOLO conectarse a esta conversación específica
  listenToConversation(
    conversationId, 
    // Callback para mensajes
    (msg) => {
      console.log("[FRONT] Mensaje recibido:", msg);
      conversationMessages.value.push(msg);
      nextTick(scrollToBottom);
    },
    // Callback para typing
    (fromUserId) => {
      const currentOtherId = getOtherUserId(currentConversation.value);
      if (currentConversation.value && fromUserId === currentOtherId) {
        isTyping.value = true;
        setTimeout(() => (isTyping.value = false), 2000);
      }
    }
  );
  
  nextTick(scrollToBottom);
}

const isOnline = (userId) => onlineUsers.value.some((u) => u.id === userId);

function getConversationName(conversation) {
  if (!props.userId || !conversation) return "";
  if (conversation.owner_id === props.userId)
    return conversation.guest?.name || "";
  return conversation.owner?.name || "";
}
function getPropertyName(propertyId) {
  const property = props.properties.find((p) => p.id === propertyId);
  return property ? property.name : "No disponible";
}

function getFilterCount(filterId) {
  if (filterId === "all") return conversations.value.length;
  if (filterId === "unread")
    return conversations.value.filter((c) => c.unread).length;
  if (filterId === "sent")
    return conversations.value.filter(
      (c) => c.last_message?.sender_id === props.userId
    ).length;
  if (filterId === "archived")
    return conversations.value.filter(
      (c) => c.archived_by_guest || c.archived_by_owner
    ).length;
  if (filterId === "starred")
    return conversations.value.filter(
      (c) => c.starred_by_guest || c.starred_by_owner
    ).length;
  return 0;
}

const filteredConversations = computed(() => {
  let filtered = [...conversations.value];
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (conversation) =>
        getConversationName(conversation).toLowerCase().includes(query) ||
        (conversation.last_message?.text?.toLowerCase() || "").includes(query)
    );
  }
  if (activeFilter.value === "unread") {
    filtered = filtered.filter((conversation) => conversation.unread);
  } else if (activeFilter.value === "sent") {
    filtered = filtered.filter(
      (conversation) => conversation.last_message?.sender_id === props.userId
    );
  } else if (activeFilter.value === "archived") {
    filtered = filtered.filter(
      (conversation) =>
        conversation.archived_by_guest || conversation.archived_by_owner
    );
  } else if (activeFilter.value === "starred") {
    filtered = filtered.filter(
      (conversation) =>
        conversation.starred_by_guest || conversation.starred_by_owner
    );
  }
  filtered.sort(
    (a, b) =>
      new Date(b.last_message?.created_at || 0) -
      new Date(a.last_message?.created_at || 0)
  );
  return filtered;
});

const currentConversation = computed(() => {
  if (!activeConversation.value) return null;
  return (
    conversations.value.find((c) => c.id === activeConversation.value) || null
  );
});

const getInitials = (name) =>
  name
    ? name
        .split(" ")
        .map((p) => p[0])
        .join("")
        .toUpperCase()
        .substring(0, 2)
    : "?";

const formatMessageTime = (timestamp) => {
  if (!timestamp) return "";
  const date = new Date(timestamp);
  const now = new Date();
  const diffMs = now - date;
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  if (diffDays === 0) {
    return date.toLocaleTimeString("es-ES", {
      hour: "2-digit",
      minute: "2-digit",
    });
  } else if (diffDays === 1) {
    return "Ayer";
  } else if (diffDays < 7) {
    const days = [
      "Domingo",
      "Lunes",
      "Martes",
      "Miércoles",
      "Jueves",
      "Viernes",
      "Sábado",
    ];
    return days[date.getDay()];
  } else {
    return date.toLocaleDateString("es-ES", { day: "numeric", month: "short" });
  }
};
const formatMessageDate = (timestamp) => {
  if (!timestamp) return "";
  const date = new Date(timestamp);
  const now = new Date();
  const diffMs = now - date;
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  if (diffDays === 0) return "Hoy";
  else if (diffDays === 1) return "Ayer";
  else
    return date.toLocaleDateString("es-ES", {
      day: "numeric",
      month: "long",
      year: "numeric",
    });
};
const formatBookingDates = (reservation) => {
  if (!reservation || !reservation.reservation_date) return "No disponible";
  return new Date(reservation.reservation_date).toLocaleDateString("es-ES");
};
function formatLastSeen(lastSeen) {
  if (!lastSeen) return "hace mucho";
  const date = new Date(lastSeen);
  const now = new Date();
  const diffMs = now - date;
  const diffMinutes = Math.floor(diffMs / (1000 * 60));
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (diffMinutes < 1) return "hace un momento";
  if (diffMinutes < 60) return `hace ${diffMinutes} min`;
  if (diffHours < 24) return `hace ${diffHours} h`;
  if (diffDays === 1) return "ayer";
  if (diffDays < 7) return `hace ${diffDays} días`;
  return date.toLocaleDateString("es-ES");
}

function showDateDivider(msg, prevMsg) {
  if (!prevMsg) return true; // Primer mensaje, muestra divider
  const date1 = new Date(msg.created_at || msg.timestamp);
  const date2 = new Date(prevMsg.created_at || prevMsg.timestamp);
  return (
    date1.getDate() !== date2.getDate() ||
    date1.getMonth() !== date2.getMonth() ||
    date1.getFullYear() !== date2.getFullYear()
  );
}
function isFirstInGroup(msg, prevMsg) {
  if (!prevMsg) return true;
  return msg.isOwner !== prevMsg.isOwner || showDateDivider(msg, prevMsg);
}
function isLastInGroup(msg, nextMsg) {
  if (!nextMsg) return true;
  return msg.isOwner !== nextMsg.isOwner || showDateDivider(nextMsg, msg);
}

function getPaymentStatusText(status) {
  if (!status) return "Pendiente";
  if (status === "paid") return "Pagado";
  if (status === "pending") return "Pendiente";
  if (status === "failed") return "Fallido";
  return status;
}

const scrollToBottom = () => {
  if (messagesList.value)
    messagesList.value.scrollTop = messagesList.value.scrollHeight;
};
const openComposeModal = () => {
  showComposeModal.value = true;
  newConversation.value = { reservationId: "", message: "" };
};
const closeComposeModal = () => (showComposeModal.value = false);
async function sendNewMessage() {
  if (!newConversation.value.reservationId) return;
  const { data: conversation } = await createConversation(
    newConversation.value.reservationId
  );
  if (!conversations.value.some((c) => c.id === conversation.id))
    conversations.value.push(conversation);
  await selectConversation(conversation.id);
  if (newConversation.value.message) {
    await sendMessage(conversation.id, newConversation.value.message);
    await selectConversation(conversation.id);
  }
  closeComposeModal();
}
async function sendMessageHandler() {
  if (!newMessage.value.trim() && attachments.value.length === 0) return;
  await sendMessage(
    activeConversation.value,
    newMessage.value,
    attachments.value[0]?.file || null
  );
  await selectConversation(activeConversation.value);
  newMessage.value = "";
  attachments.value = [];
}
const triggerFileInput = () => fileInput.value.click();
const handleFileUpload = (event) => {
  const files = event.target.files;
  if (!files.length) return;
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const reader = new FileReader();
    reader.onload = (e) => {
      attachments.value.push({
        name: file.name,
        type: file.type,
        size: file.size,
        preview: file.type.startsWith("image/") ? e.target.result : null,
        file: file,
      });
    };
    reader.readAsDataURL(file);
  }
  event.target.value = "";
};
const removeAttachment = (index) => attachments.value.splice(index, 1);

let typingTimeout = null;
function sendTypingNotification() {
  if (!activeConversation.value) return;

  if (typingTimeout) clearTimeout(typingTimeout);

  typingTimeout = setTimeout(() => {
    sendTyping(activeConversation.value);
  }, 300); // Debounce de 300ms
}

onMounted(async () => {
  if (window.Echo) {
    window.Echo.connector.pusher.connection.bind(
      "state_change",
      function (states) {
        console.log("Pusher state changed:", states);
      }
    );
    window.Echo.connector.pusher.connection.bind("error", function (err) {
      console.error("Pusher error:", err);
    });
  }
  
  try {
    // Carga inicial
    const { data } = await getConversations();
    console.log("CONVERSATIONS:", data);
    conversations.value = data;
    
    // SOLO seleccionar la primera conversación, NO conectar a todas
    if (conversations.value.length > 0) {
      await selectConversation(conversations.value[0].id);
    }
    
    // Escucha usuarios online
    listenOnline();
    
  } catch (error) {
    console.error('Error en onMounted:', error);
  }
});

watch(
  () => conversationMessages.value.length,
  () => nextTick(scrollToBottom)
);

watch(activeConversation, (conversationId, oldId) => {
  if (window.Echo) {
    if (oldId) window.Echo.leave(`chat.${oldId}`);
    window.Echo.private(`chat.${conversationId}`)
      .listen(".message.sent", (e) => {
        console.log("[FRONT] Mensaje recibido:", e.message);
      })
      .listen(".user.typing", (e) => {
        console.log("[FRONT] Usuario está escribiendo:", e.from_user_id);
      });
  }
});
</script>

<style scoped>
.messages-section {
  background: linear-gradient(to bottom, #f0f7ff, #ffffff);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 53, 128, 0.1);
  padding: 2rem;
  min-height: 70vh;
  width: 100%;
  max-width: 1200px;
  margin: 2rem auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
  position: relative;
  overflow: hidden;
}

.messages-section::before {
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

.header-actions {
  display: flex;
  gap: 1rem;
}

.compose-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.compose-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

.button-icon {
  width: 18px;
  height: 18px;
}

/* Contenedor principal de mensajes */
.messages-container {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 1.5rem;
  height: calc(70vh - 6rem);
  position: relative;
  z-index: 1;
}

/* Sidebar de conversaciones */
.messages-sidebar {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
}

.search-container {
  padding: 1rem;
  border-bottom: 1px solid #e6f0ff;
}

.search-input {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  width: 18px;
  height: 18px;
}

.search-field {
  width: 100%;
  padding: 0.75rem 2.5rem 0.75rem 2.5rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 0.95rem;
  background-color: #f8fafc;
  transition: all 0.3s;
}

.search-field:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
  background-color: white;
}

.clear-search {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.clear-icon {
  width: 16px;
  height: 16px;
}

.conversation-filters {
  display: flex;
  overflow-x: auto;
  padding: 0.5rem 1rem;
  gap: 0.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.filter-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 6px;
  background: none;
  color: #64748b;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
}

.filter-button:hover {
  background-color: #f1f5f9;
  color: #0071c2;
}

.filter-button.active {
  background-color: #e6f0ff;
  color: #0071c2;
}

.filter-icon {
  width: 16px;
  height: 16px;
}

.filter-count {
  background-color: #f1f5f9;
  color: #64748b;
  border-radius: 20px;
  padding: 0.1rem 0.5rem;
  font-size: 0.75rem;
  min-width: 20px;
  text-align: center;
}

.filter-button.active .filter-count {
  background-color: #0071c2;
  color: white;
}

.conversations-list {
  flex: 1;
  overflow-y: auto;
}

.conversation-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid #e6f0ff;
  cursor: pointer;
  transition: all 0.3s;
}

.conversation-item:hover {
  background-color: #f8fafc;
}

.conversation-item.active {
  background-color: #e6f0ff;
}

.conversation-item.unread {
  background-color: #f0f7ff;
}

.conversation-avatar {
  position: relative;
  width: 48px;
  height: 48px;
  flex-shrink: 0;
}

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1.1rem;
}

.online-indicator {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: #10b981;
  border: 2px solid white;
}

.conversation-content {
  flex: 1;
  min-width: 0;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.25rem;
}

.conversation-name {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conversation-time {
  font-size: 0.8rem;
  color: #64748b;
  white-space: nowrap;
}

.conversation-property {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
  font-size: 0.85rem;
  color: #0071c2;
}

.property-icon {
  width: 14px;
  height: 14px;
}

.conversation-preview {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conversation-item.unread .conversation-preview {
  color: #1e293b;
  font-weight: 500;
}

.empty-conversations {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
  color: #64748b;
}

.empty-icon {
  width: 48px;
  height: 48px;
  color: #cbd5e1;
  margin-bottom: 1rem;
}

/* Contenido de mensajes */
.messages-content {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 8px rgba(0, 53, 128, 0.05);
  border: 1px solid rgba(0, 53, 128, 0.05);
  position: relative;
}

.conversation-view {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e6f0ff;
  background-color: #f8fafc;
}

.conversation-user {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-avatar {
  width: 48px;
  height: 48px;
}

.user-info h3 {
  font-size: 1.1rem;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.user-status {
  font-size: 0.85rem;
}

.status {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.status.online {
  color: #10b981;
}

.status.online::before {
  content: "";
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #10b981;
}

.status.offline {
  color: #64748b;
}

.conversation-actions {
  display: flex;
  gap: 0.75rem;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #e6f0ff;
  border-radius: 6px;
  background-color: white;
  color: #0071c2;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s;
}

.action-button:hover {
  background-color: #f0f7ff;
  border-color: #0071c2;
}

.action-icon {
  width: 16px;
  height: 16px;
}

.messages-list {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background-color: #f8fafc;
}

.date-divider {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 1rem 0;
  position: relative;
}

.date-divider::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background-color: #e6f0ff;
  z-index: 0;
}

.date-divider span {
  background-color: #f8fafc;
  padding: 0 1rem;
  font-size: 0.85rem;
  color: #64748b;
  position: relative;
  z-index: 1;
}

.message-group {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.message-bubble {
  max-width: 70%;
  padding: 0.75rem 1rem;
  border-radius: 12px;
  position: relative;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.message-bubble.owner {
  align-self: flex-end;
  background-color: #e6f0ff;
  color: #1e293b;
  border-bottom-right-radius: 4px;
}

.message-bubble.guest {
  align-self: flex-start;
  background-color: white;
  color: #1e293b;
  border: 1px solid #e6f0ff;
  border-bottom-left-radius: 4px;
}

.message-bubble.first {
  margin-top: 0.5rem;
}

.message-bubble.last {
  margin-bottom: 0.5rem;
}

.message-content {
  font-size: 0.95rem;
  line-height: 1.5;
  word-break: break-word;
}

.message-time {
  font-size: 0.75rem;
  color: #64748b;
  text-align: right;
  margin-top: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.25rem;
}

.message-status {
  display: inline-flex;
  align-items: center;
}

.status-icon {
  width: 14px;
  height: 14px;
}

.message-status.sent {
  color: #94a3b8;
}

.message-status.delivered {
  color: #64748b;
}

.message-status.read {
  color: #0071c2;
}

.typing-indicator {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background-color: white;
  border: 1px solid #e6f0ff;
  border-radius: 12px;
  max-width: 70%;
  align-self: flex-start;
  margin-top: 0.5rem;
  font-size: 0.9rem;
  color: #64748b;
}

.typing-bubble {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.typing-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #94a3b8;
  animation: typingAnimation 1.5s infinite ease-in-out;
}

.typing-dot:nth-child(1) {
  animation-delay: 0s;
}

.typing-dot:nth-child(2) {
  animation-delay: 0.3s;
}

.typing-dot:nth-child(3) {
  animation-delay: 0.6s;
}

@keyframes typingAnimation {
  0%,
  100% {
    transform: translateY(0);
    opacity: 0.5;
  }
  50% {
    transform: translateY(-5px);
    opacity: 1;
  }
}

.message-composer {
  padding: 1rem;
  border-top: 1px solid #e6f0ff;
  background-color: white;
}

.composer-attachments {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background-color: #f8fafc;
  border-radius: 8px;
}

.attachment-preview {
  position: relative;
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  background-color: #e6f0ff;
}

.attachment-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.attachment-file {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
}

.file-icon {
  width: 24px;
  height: 24px;
  color: #0071c2;
  margin-bottom: 0.25rem;
}

.file-name {
  font-size: 0.7rem;
  color: #1e293b;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
}

.remove-attachment {
  position: absolute;
  top: 0.25rem;
  right: 0.25rem;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.remove-attachment:hover {
  background-color: rgba(0, 0, 0, 0.7);
}

.remove-icon {
  width: 12px;
  height: 12px;
}

.composer-input-container {
  display: flex;
  align-items: flex-end;
  gap: 0.75rem;
}

.attachment-button {
  background: none;
  border: none;
  color: #64748b;
  padding: 0.75rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.attachment-button:hover {
  background-color: #f1f5f9;
  color: #0071c2;
}

.attachment-icon {
  width: 20px;
  height: 20px;
}

.composer-input {
  flex: 1;
  background-color: #f8fafc;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  padding: 0.5rem 1rem;
  transition: all 0.3s;
}

.composer-input:focus-within {
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
  background-color: white;
}

.message-input {
  width: 100%;
  border: none;
  background: none;
  resize: none;
  font-size: 0.95rem;
  color: #1e293b;
  font-family: inherit;
  padding: 0;
  max-height: 120px;
}

.message-input:focus {
  outline: none;
}

.message-input::placeholder {
  color: #94a3b8;
}

.send-button {
  background-color: #0071c2;
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
}

.send-button:hover:not(:disabled) {
  background-color: #005999;
  transform: translateY(-2px);
}

.send-button:disabled {
  background-color: #cbd5e1;
  cursor: not-allowed;
}

.send-icon {
  width: 18px;
  height: 18px;
}

.no-conversation-selected {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 2rem;
  text-align: center;
  color: #64748b;
}

.no-conversation-selected .empty-icon {
  width: 64px;
  height: 64px;
  color: #cbd5e1;
  margin-bottom: 1.5rem;
}

.no-conversation-selected h3 {
  font-size: 1.3rem;
  color: #003580;
  margin-bottom: 0.5rem;
}

/* Panel de información */
.info-panel {
  width: 300px;
  background-color: white;
  border-left: 1px solid #e6f0ff;
  display: flex;
  flex-direction: column;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.info-panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e6f0ff;
  background-color: #f8fafc;
}

.info-panel-header h3 {
  font-size: 1.1rem;
  color: #003580;
  margin: 0;
}

.close-info-button {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.3s;
}

.close-info-button:hover {
  background-color: #e6f0ff;
  color: #0071c2;
}

.info-panel-content {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.info-section {
  margin-bottom: 2rem;
}

.info-section:last-child {
  margin-bottom: 0;
}

.info-section h4 {
  font-size: 1rem;
  color: #003580;
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e6f0ff;
}

.info-item {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-icon {
  width: 18px;
  height: 18px;
  color: #0071c2;
}

.info-label {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0 0 0.25rem;
}

.info-value {
  font-size: 0.95rem;
  color: #1e293b;
  margin: 0;
}

.payment-status {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 20px;
  font-size: 0.8rem;
}

.payment-status.paid {
  background-color: #e6f7ee;
  color: #00703c;
}

.payment-status.pending {
  background-color: #fff8e6;
  color: #b27b00;
}

.payment-status.refunded {
  background-color: #e6f0ff;
  color: #0071c2;
}

.payment-status.failed {
  background-color: #fff2f0;
  color: #e41c00;
}

.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.quick-action-button {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  background-color: #f8fafc;
  color: #1e293b;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
}

.quick-action-button:hover {
  background-color: #e6f0ff;
  border-color: #0071c2;
  color: #0071c2;
}

/* Modal de nuevo mensaje */
.compose-modal {
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

.modal-content {
  background-color: white;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 53, 128, 0.2);
  animation: modalFadeIn 0.3s ease;
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

.modal-body {
  padding: 2rem;
}

.compose-form {
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
  font-weight: 600;
  color: #003580;
  font-size: 0.95rem;
}

.form-input,
.form-textarea {
  padding: 0.875rem 1rem;
  border: 1px solid #e6f0ff;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  color: #1e293b;
  background-color: #f8fafc;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #0071c2;
  box-shadow: 0 0 0 3px rgba(0, 113, 194, 0.1);
  background-color: white;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-button {
  background-color: white;
  color: #64748b;
  border: 1px solid #e6f0ff;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover {
  background-color: #f8fafc;
  color: #1e293b;
}

.submit-button {
  background: linear-gradient(135deg, #0071c2 0%, #003580 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 53, 128, 0.1);
}

.submit-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 53, 128, 0.15);
}

/* Responsive */
@media (max-width: 1024px) {
  .messages-container {
    grid-template-columns: 280px 1fr;
  }

  .info-panel {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 10;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
  }
}

@media (max-width: 768px) {
  .messages-section {
    padding: 1.5rem;
  }

  .messages-container {
    grid-template-columns: 1fr;
    height: auto;
  }

  .messages-sidebar {
    display: none;
  }

  .messages-sidebar.active {
    display: flex;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 100%;
    z-index: 10;
  }

  .conversation-view {
    height: 60vh;
  }

  .action-text {
    display: none;
  }

  .info-panel {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .messages-section {
    padding: 1rem;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .header-actions {
    width: 100%;
  }

  .compose-button {
    width: 100%;
    justify-content: center;
  }

  .conversation-header {
    padding: 1rem;
  }

  .user-avatar {
    width: 40px;
    height: 40px;
  }

  .messages-list {
    padding: 1rem;
  }

  .message-bubble {
    max-width: 85%;
  }

  .modal-content {
    width: 95%;
  }

  .modal-body {
    padding: 1.5rem;
  }
}
</style>