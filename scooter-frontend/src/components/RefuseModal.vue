<script setup>
import { ref, watch } from 'vue'
import Modal from './Modal.vue'
import { api } from '../api'

const props = defineProps({
  open: Boolean,
  request: { type: Object, default: null },
})
const emit = defineEmits(['close', 'done'])

const reason = ref('')
const loading = ref(false)
const error = ref('')

watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      reason.value = ''
      error.value = ''
    }
  },
)

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await api.refuseRequest(props.request.id, reason.value.trim())
    emit('done', props.request)
    emit('close')
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Modal :open="open" :title="`Refuser la demande de ${request?.requesterName ?? ''}`" @close="emit('close')">
    <form class="form" @submit.prevent="submit">
      <label class="field">
        <span>Motif du refus</span>
        <textarea
          v-model="reason"
          required
          minlength="3"
          rows="4"
          placeholder="Ex. : le scooter est en maintenance ce jour-là."
        ></textarea>
        <small>La personne verra ce motif dans « Mes réservations ».</small>
      </label>

      <p v-if="error" class="form-error" role="alert">{{ error }}</p>

      <div class="form__actions">
        <button type="button" class="btn btn--ghost" @click="emit('close')">Annuler</button>
        <button class="btn btn--danger-solid" :disabled="loading">{{ loading ? 'Envoi…' : 'Refuser la demande' }}</button>
      </div>
    </form>
  </Modal>
</template>
