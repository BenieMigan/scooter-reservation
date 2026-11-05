<script setup>
defineProps({
  plate: { type: String, default: '' },
  small: { type: Boolean, default: false },
})
</script>

<template>
  <span :class="['plate', { 'plate--sm': small }]" :aria-label="`Plaque ${plate}`">
    <span class="plate__strip" aria-hidden="true"></span>
    <span class="plate__text">{{ plate }}</span>
  </span>
</template>
