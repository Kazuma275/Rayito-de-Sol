<template>
  <div class="bookings-list">
    <BookingCard
      v-for="booking in bookings"
      :key="booking.id"
      :booking="booking"
      :get-property-by-id="getPropertyById"
      @view-details="() => $emit('view-details', booking)"
      @accept="() => $emit('accept', booking)"
      @reject="() => $emit('reject', booking)"
      @message="() => $emit('message', booking)"
    />
  </div>
</template>

<script setup>
import BookingCard from './BookingCard.vue'
defineProps({
  bookings: Array,
  getPropertyById: Function
})
defineEmits(['view-details', 'accept', 'reject', 'message'])
</script>