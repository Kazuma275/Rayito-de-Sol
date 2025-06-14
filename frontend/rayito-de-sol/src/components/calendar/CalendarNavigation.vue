<template>
  <div class="calendar-navigation">
    <button class="nav-button" @click="$emit('prev')">
      <ChevronLeftIcon />
    </button>
    <h3 class="current-month">{{ currentMonthName }} {{ currentYear }}</h3>
    <button class="nav-button" @click="$emit('next')">
      <ChevronRightIcon />
    </button>
  </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next'
defineProps({ currentMonthName: String, currentYear: Number })
defineEmits(['prev', 'next'])
</script>