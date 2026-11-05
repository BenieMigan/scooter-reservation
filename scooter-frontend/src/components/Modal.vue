<script setup>
import { ref, watch, nextTick, onBeforeUnmount } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: '' },
})
const emit = defineEmits(['close'])
const panel = ref(null)

function onKey(event) {
  if (event.key === 'Escape') emit('close')
}

watch(
  () => props.open,
  async (isOpen) => {
    if (isOpen) {
      document.addEventListener('keydown', onKey)
      document.body.style.overflow = 'hidden'
      await nextTick()
      panel.value?.querySelector('input, textarea, select')?.focus()
    } else {
      document.removeEventListener('keydown', onKey)
      document.body.style.overflow = ''
    }
  },
  { immediate: true },
)

onBeforeUnmount(() => {
  document.removeEventListener('keydown', onKey)
  document.body.style.overflow = ''
})
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="modal" @mousedown.self="emit('close')">
      <div ref="panel" class="modal__panel" role="dialog" aria-modal="true" :aria-label="title">
        <header class="modal__head">
          <h2>{{ title }}</h2>
          <button class="iconbtn" type="button" aria-label="Fermer" @click="emit('close')">
            <Icon name="x" />
          </button>
        </header>
        <slot />
      </div>
    </div>
  </Teleport>
</template>
