<script setup>
import { reactive, ref, watch } from 'vue'
import Modal from './Modal.vue'
import { api } from '../api'
import { toast } from '../toast'
import { toApiDate, nowForInput } from '../format'

const props = defineProps({
  open: Boolean,
  scooter: { type: Object, default: null },
})
const emit = defineEmits(['close', 'done'])

const form = reactive({ email: '', startAt: '', endAt: '' })
const minDate = ref(nowForInput())
const loading = ref(false)
const error = ref('')

watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      form.email = ''
      form.startAt = ''
      form.endAt = ''
      minDate.value = nowForInput()
      error.value = ''
    }
  },
)

async function submit() {
  error.value = ''
  if (form.endAt <= form.startAt) {
    error.value = "L'heure de fin doit être après l'heure de début."
    return
  }
  loading.value = true
  try {
    await api.createReservation({
      scooterId: props.scooter.id,
      email: form.email.trim(),
      startAt: toApiDate(form.startAt),
      endAt: toApiDate(form.endAt),
    })
    toast(`Réservation enregistrée pour ${form.email.trim()}.`, 'success')
    emit('done')
    emit('close')
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Modal :open="open" :title="`Réserver ${scooter?.plate ?? ''} pour quelqu'un`" @close="emit('close')">
    <form class="form" @submit.prevent="submit">
      <p class="form__intro">
        Tu peux réserver seulement pour une personne que tu as déjà autorisée. La réservation est acceptée
        directement.
      </p>
      <label class="field">
        <span>Adresse email de la personne</span>
        <input v-model="form.email" type="email" required placeholder="marie@exemple.com" autocomplete="off" />
      </label>
      <div class="field-row">
        <label class="field">
          <span>Début</span>
          <input v-model="form.startAt" type="datetime-local" required :min="minDate" />
        </label>
        <label class="field">
          <span>Fin</span>
          <input v-model="form.endAt" type="datetime-local" required :min="form.startAt || minDate" />
        </label>
      </div>

      <p v-if="error" class="form-error" role="alert">{{ error }}</p>

      <div class="form__actions">
        <button type="button" class="btn btn--ghost" @click="emit('close')">Annuler</button>
        <button class="btn btn--primary" :disabled="loading">{{ loading ? 'Réservation…' : 'Réserver' }}</button>
      </div>
    </form>
  </Modal>
</template>
