<template>
  <div :class="['custom-toast', `custom-toast--${type}`]">
    <div class="custom-toast__icon">
      <component :is="icon" />
    </div>
    <div class="custom-toast__content">
      <div class="custom-toast__title">{{ title }}</div>
      <div class="custom-toast__message">{{ message }}</div>
    </div>
    <button class="custom-toast__close" @click="closeToast">
      <XIcon size="18" />
    </button>
  </div>
</template>

<script setup>
import { CheckCircleIcon, AlertCircleIcon, InfoIcon, AlertTriangleIcon, XIcon } from 'lucide-vue-next';
import { defineProps, computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  closeToast: {
    type: Function,
    required: true
  }
});

const icon = computed(() => {
  switch (props.type) {
    case 'success':
      return CheckCircleIcon;
    case 'error':
      return AlertCircleIcon;
    case 'warning':
      return AlertTriangleIcon;
    case 'info':
    default:
      return InfoIcon;
  }
});
</script>

<style scoped>
.custom-toast {
  display: flex;
  align-items: flex-start;
  padding: 16px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  min-width: 300px;
  max-width: 450px;
  animation: slideIn 0.3s ease-out forwards;
  position: relative;
  overflow: hidden;
}

.custom-toast::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  width: 100%;
  background: rgba(255, 255, 255, 0.3);
}

.custom-toast--success {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: white;
}

.custom-toast--error {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: white;
}

.custom-toast--warning {
  background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
  color: white;
}

.custom-toast--info {
  background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
  color: white;
}

.custom-toast__icon {
  flex-shrink: 0;
  margin-right: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
}

.custom-toast__content {
  flex-grow: 1;
  padding-right: 12px;
}

.custom-toast__title {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 4px;
}

.custom-toast__message {
  font-size: 0.9rem;
  opacity: 0.9;
  line-height: 1.4;
}

.custom-toast__close {
  background: transparent;
  border: none;
  color: white;
  opacity: 0.7;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.custom-toast__close:hover {
  opacity: 1;
  background: rgba(255, 255, 255, 0.1);
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
</style>