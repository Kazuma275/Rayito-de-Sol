<template>
  <div class="stat-card">
    <component :is="iconComponent" class="stat-icon" />
    <div class="stat-content">
      <h3 class="stat-title">{{ title }}</h3>
      <p class="stat-value">{{ value }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { 
  HomeIcon, 
  CalendarIcon, 
  TrendingUpIcon, 
  EuroIcon 
} from 'lucide-vue-next';

const props = defineProps({
  icon: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  }
});

const iconComponent = computed(() => {
  const icons = {
    'Home': HomeIcon,
    'Calendar': CalendarIcon,
    'TrendingUp': TrendingUpIcon,
    'Euro': EuroIcon
  };
  return icons[props.icon] || HomeIcon;
});
</script>

<style scoped>
.stat-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 40px;
  height: 40px;
  color: #0071c2;
  margin-right: 1rem;
}

.stat-title {
  font-size: 0.9rem;
  color: #666;
  margin: 0 0 0.5rem;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #003580;
  margin: 0;
}
</style>