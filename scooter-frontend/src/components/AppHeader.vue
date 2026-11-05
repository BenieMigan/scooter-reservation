<script setup>
import { computed, onMounted, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import Icon from './Icon.vue'
import { auth } from '../auth'
import { api } from '../api'
import { pendingCount } from '../state'
import { APP_NAME } from '../config'

const route = useRoute()
const router = useRouter()

// Le menu dépend du rôle : le propriétaire a son espace (3 pages), l'utilisateur simple a les siennes (2 pages).
const ownerLinks = [
  { to: '/scooters', label: 'Mes scooters', icon: 'scooter' },
  { to: '/demandes', label: 'Demandes reçues', icon: 'inbox', badge: true },
  { to: '/historique', label: 'Historique', icon: 'history' },
]
const commonLinks = [
  { to: '/disponibles', label: 'Scooters disponibles', icon: 'scooter' },
  { to: '/mes-reservations', label: 'Mes réservations', icon: 'calendar' },
]
const links = computed(() => (auth.isOwner ? ownerLinks : commonLinks))

const initial = computed(() => (auth.email[0] || '?').toUpperCase())

// Met à jour la pastille « demandes en attente » du menu.
async function refreshPending() {
  if (!auth.isOwner) return
  try {
    pendingCount.value = (await api.receivedRequests()).length
  } catch {
    /* silencieux : la page concernée affichera l'erreur */
  }
}

onMounted(refreshPending)
watch(() => route.fullPath, refreshPending)

function logout() {
  auth.logout()
  router.replace('/connexion')
}
</script>

<template>
  <header class="topbar">
    <div class="topbar__inner">
      <RouterLink :to="auth.home" class="brand" :aria-label="`${APP_NAME}, accueil`">
        <span class="plate plate--brand">
          <span class="plate__strip" aria-hidden="true"></span>
          <span class="plate__text">{{ APP_NAME }}</span>
        </span>
      </RouterLink>

      <nav class="nav" aria-label="Navigation principale">
        <RouterLink v-for="link in links" :key="link.to" :to="link.to" class="nav__link">
          <Icon :name="link.icon" :size="20" />
          <span class="nav__label">{{ link.label }}</span>
          <span v-if="link.badge && pendingCount" class="nav__count">{{ pendingCount }}</span>
        </RouterLink>
      </nav>

      <div class="userbox">
        <span class="avatar" :title="auth.email" aria-hidden="true">{{ initial }}</span>
        <span class="sr-only">Connecté : {{ auth.email }}</span>
        <button class="btn btn--ghost btn--sm" type="button" @click="logout">
          <Icon name="logout" :size="16" />
          <span class="userbox__logout">Déconnexion</span>
        </button>
      </div>
    </div>
  </header>
</template>
