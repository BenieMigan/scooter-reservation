<script setup>
import { toasts, dismiss } from '../toast'
</script>

<template>
  <div class="toasts" aria-live="polite">
    <TransitionGroup name="toast">
      <div v-for="t in toasts" :key="t.id" :class="['toast', `toast--${t.type}`]" role="status">
        <span>{{ t.message }}</span>
        <button class="toast__close" type="button" aria-label="Fermer le message" @click="dismiss(t.id)">×</button>
      </div>
    </TransitionGroup>
  </div>
</template>
