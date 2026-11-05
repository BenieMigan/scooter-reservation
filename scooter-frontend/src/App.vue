<script setup>
import { watch } from 'vue'
import { RouterView, useRoute, useRouter } from 'vue-router'
import AppHeader from './components/AppHeader.vue'
import ToastHost from './components/ToastHost.vue'
import { auth } from './auth'

const route = useRoute()
const router = useRouter()

// Quand le token disparaît (déconnexion ou session expirée) : retour à la connexion.
watch(
  () => auth.isLoggedIn,
  (loggedIn) => {
    if (!loggedIn && !route.meta.public) router.replace('/connexion')
  },
)
</script>

<template>
  <AppHeader v-if="!route.meta.public" />
  <main :class="route.meta.public ? 'main--bare' : 'page'">
    <RouterView />
  </main>
  <ToastHost />
</template>
