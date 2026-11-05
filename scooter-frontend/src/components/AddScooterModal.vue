<script setup>
import { reactive, ref, watch } from 'vue'
import Modal from './Modal.vue'
import { api } from '../api'
import { toast } from '../toast'

const props = defineProps({ open: Boolean })
const emit = defineEmits(['close', 'done'])

const form = reactive({ model: '', plate: '' })
const loading = ref(false)
const error = ref('')

// Formulaire vide à chaque ouverture
watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      form.model = ''
      form.plate = ''
      error.value = ''
    }
  },
)

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await api.createScooter({ model: form.model.trim(), plate: form.plate.trim() })
    toast('Scooter ajouté.', 'success')
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
  <Modal :open="open" title="Ajouter un scooter" @close="emit('close')">
    <form class="form" @submit.prevent="submit">
      <label class="field">
        <span>Modèle</span>
        <input v-model="form.model" required maxlength="100" placeholder="Yamaha Neo" autocomplete="off" />
      </label>
      <label class="field">
        <span>Plaque d'immatriculation</span>
        <input v-model="form.plate" required maxlength="20" placeholder="AB 1234 BJ" autocomplete="off" />
        <small>Une plaque ne peut être enregistrée qu'une seule fois.</small>
      </label>

      <p v-if="error" class="form-error" role="alert">{{ error }}</p>

      <div class="form__actions">
        <button type="button" class="btn btn--ghost" @click="emit('close')">Annuler</button>
        <button class="btn btn--primary" :disabled="loading">{{ loading ? 'Ajout…' : 'Ajouter le scooter' }}</button>
      </div>
    </form>
  </Modal>
</template>
