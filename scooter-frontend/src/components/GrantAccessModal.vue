<script setup>
import { ref, watch } from 'vue'
import Modal from './Modal.vue'
import { api } from '../api'
import { toast } from '../toast'

const props = defineProps({
  open: Boolean,
  scooter: { type: Object, default: null },
})
const emit = defineEmits(['close', 'done'])

const email = ref('')
const loading = ref(false)
const error = ref('')

watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      email.value = ''
      error.value = ''
    }
  },
)

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await api.grantAccess({ scooterId: props.scooter.id, email: email.value.trim() })
    toast(`${email.value.trim()} peut maintenant utiliser ${props.scooter.plate}.`, 'success')
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
  <Modal :open="open" :title="`Autoriser quelqu'un sur ${scooter?.plate ?? ''}`" @close="emit('close')">
    <form class="form" @submit.prevent="submit">
      <p class="form__intro">
        La personne doit déjà avoir un compte. Une fois autorisée, tu pourras réserver le scooter pour elle.
      </p>
      <label class="field">
        <span>Adresse email de la personne</span>
        <input v-model="email" type="email" required placeholder="marie@exemple.com" autocomplete="off" />
      </label>

      <p v-if="error" class="form-error" role="alert">{{ error }}</p>

      <div class="form__actions">
        <button type="button" class="btn btn--ghost" @click="emit('close')">Annuler</button>
        <button class="btn btn--primary" :disabled="loading">{{ loading ? 'Autorisation…' : 'Autoriser' }}</button>
      </div>
    </form>
  </Modal>
</template>
