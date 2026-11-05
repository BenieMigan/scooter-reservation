<script setup>
import ScooterArt from './ScooterArt.vue'

defineProps({
  title: { type: String, required: true },
  text: { type: String, default: '' },
})
</script>

<template>
  <div class="empty">
    <div class="empty__art"><ScooterArt muted /></div>
    <h2>{{ title }}</h2>
    <p v-if="text">{{ text }}</p>
    <div class="empty__actions"><slot /></div>
  </div>
</template>
