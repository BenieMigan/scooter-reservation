<script setup>
import { reactive, ref, watch, computed } from 'vue'
import Modal from './Modal.vue'
import { api } from '../api'
import { toast } from '../toast'
import { toApiDate, nowForInput } from '../format'

const props = defineProps({ open: Boolean, scooterId: { type: [Number, String], default: '' } })
const emit = defineEmits(['close', 'done'])

const form = reactive({ scooterId: '', startAt: '', endAt: '' })
const minDate = ref(nowForInput())
const scooters = ref([])
const loading = ref(false)
const error = ref('')

watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      form.scooterId = props.scooterId ?? ''
      form.startAt = ''
      form.endAt = ''
      minDate.value = nowForInput()
      error.value = ''
      loadScooters()
    }
  },
)

// La personne choisit un scooter par sa PLAQUE ; l'id reste interne (envoyé au back).
async function loadScooters() {
  try {
    // seuls les scooters sur lesquels je ne suis PAS autorisé : les autres, c'est le propriétaire qui réserve
    scooters.value = (await api.availableScooters()).filter((s) => !s.authorized)
  } catch (e) {
    error.value = e.message
  }
}

const selected = computed(() => scooters.value.find((s) => String(s.id) === String(form.scooterId)))

async function submit() {
  error.value = ''
  if (form.endAt <= form.startAt) {
    error.value = "L'heure de fin doit être après l'heure de début."
    return
  }
  loading.value = true
  try {
    await api.requestReservation({
      scooterId: Number(form.scooterId),
      startAt: toApiDate(form.startAt),
      endAt: toApiDate(form.endAt),
    })
    toast('Demande envoyée. Tu verras ici la réponse du propriétaire.', 'success')
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
  <Modal :open="open" title="Demander un scooter" @close="emit('close')">
    <form class="form" @submit.prevent="submit">
      <label class="field">
        <span>Scooter</span>
        <select v-model="form.scooterId" required>
          <option value="" disabled>Choisis un scooter (plaque)</option>
          <option v-for="s in scooters" :key="s.id" :value="s.id">
            {{ s.plate }} — {{ s.model }} ({{ s.ownerName }})
          </option>
        </select>
        <small v-if="selected">Propriétaire : {{ selected.ownerName }} · {{ selected.ownerEmail }}</small>
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
        <button class="btn btn--primary" :disabled="loading">{{ loading ? 'Envoi…' : 'Envoyer la demande' }}</button>
      </div>
    </form>
  </Modal>
</template>
