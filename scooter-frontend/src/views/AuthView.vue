<script setup>
import { ref, reactive, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ScooterArt from '../components/ScooterArt.vue'
import { api } from '../api'
import { auth } from '../auth'
import { APP_NAME } from '../config'

const route = useRoute()
const router = useRouter()

const mode = ref('login') // 'login' ou 'register'
const form = reactive({ name: '', email: '', password: '', role: 'ROLE_USER' })
const info = ref('')
const loading = ref(false)
const error = ref('')

const isRegister = computed(() => mode.value === 'register')

function setMode(value) {
  mode.value = value
  error.value = ''
  info.value = ''
}

async function submit() {
  error.value = ''
  loading.value = true
  const email = form.email.trim()
  try {
    // Inscription : on crée le compte, puis on revient sur la page de connexion.
    if (isRegister.value) {
      await api.register({ name: form.name.trim(), email, password: form.password, role: form.role })
      form.password = ''
      setMode('login')
      info.value = 'Compte créé. Connecte-toi maintenant.'
      return
    }
    const { token } = await api.login(email, form.password)
    auth.setToken(token)
    // Propriétaire → /scooters ; sinon → /disponibles (voir auth.home)
    const wanted = typeof route.query.redirect === 'string' ? route.query.redirect : ''
    router.replace(wanted && (auth.isOwner || !['/scooters', '/demandes', '/historique'].includes(wanted)) ? wanted : auth.home)
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth">
    <section class="auth__hero">
      <div class="plate plate--brand">
        <span class="plate__strip" aria-hidden="true"></span>
        <span class="plate__text">{{ APP_NAME }}</span>
      </div>

      <div class="auth__pitch">
        <h1>Prête ton scooter à qui tu veux, quand tu veux.</h1>
        <p>
          Enregistre ton scooter, autorise tes proches et réponds aux demandes de réservation depuis un seul
          endroit.
        </p>
      </div>

      <div class="auth__road">
        <ScooterArt :variant="3" />
      </div>
    </section>

    <section class="auth__panel">
      <div class="auth__card">
        <div class="tabs" role="tablist" aria-label="Connexion ou inscription">
          <button
            type="button"
            role="tab"
            :aria-selected="!isRegister"
            :class="['tabs__tab', { 'is-active': !isRegister }]"
            @click="setMode('login')"
          >
            Connexion
          </button>
          <button
            type="button"
            role="tab"
            :aria-selected="isRegister"
            :class="['tabs__tab', { 'is-active': isRegister }]"
            @click="setMode('register')"
          >
            Créer un compte
          </button>
        </div>

        <h2 class="auth__title">{{ isRegister ? 'Crée ton compte' : 'Content de te revoir' }}</h2>

        <form class="form" @submit.prevent="submit">
          <label v-if="isRegister" class="field">
            <span>Nom</span>
            <input v-model="form.name" required autocomplete="name" placeholder="Ton nom complet" />
          </label>

          <label class="field">
            <span>Adresse email</span>
            <input v-model="form.email" type="email" required autocomplete="email" placeholder="toi@exemple.com" />
          </label>

          <label class="field">
            <span>Mot de passe</span>
            <input
              v-model="form.password"
              type="password"
              required
              :minlength="isRegister ? 6 : undefined"
              :autocomplete="isRegister ? 'new-password' : 'current-password'"
            />
            <small v-if="isRegister">Au moins 6 caractères.</small>
          </label>

          <p v-if="info" class="notice" role="status">{{ info }}</p>
          <p v-if="error" class="form-error" role="alert">{{ error }}</p>

          <button class="btn btn--zem btn--block" :disabled="loading">
            <template v-if="loading">Un instant…</template>
            <template v-else>{{ isRegister ? 'Créer mon compte' : 'Se connecter' }}</template>
          </button>
        </form>
      </div>
    </section>
  </div>
</template>
