<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Icon from '../components/Icon.vue'
import ScooterArt from '../components/ScooterArt.vue'
import PlateBadge from '../components/PlateBadge.vue'
import StatusBadge from '../components/StatusBadge.vue'
import EmptyState from '../components/EmptyState.vue'
import RequestModal from '../components/RequestModal.vue'
import { api } from '../api'
import { toast } from '../toast'
import { stageStyle } from '../stage'
import { longDayLabel, rangeLabel, timeLabel } from '../format'
import { REFRESH_MS } from '../config'

const items = ref([])
const loading = ref(true)
const error = ref('')
const requesting = ref(false)
const updatedAt = ref(null)

// On se souvient du statut précédent de chaque demande pour prévenir
// quand le propriétaire répond (passage de "En attente" à "Acceptée" ou "Refusée").
const previous = new Map()
let timer = null

function announceChanges(list) {
  for (const r of list) {
    const before = previous.get(r.id)
    if (before === 'PENDING' && r.status === 'ACCEPTED') {
      toast(`Ta demande pour ${r.plate} a été acceptée.`, 'success', 7000)
    } else if (before === 'PENDING' && r.status === 'REFUSED') {
      toast(`Ta demande pour ${r.plate} a été refusée.`, 'error', 7000)
    }
    previous.set(r.id, r.status)
  }
}

async function load(silent = false) {
  if (!silent) {
    loading.value = true
    error.value = ''
  }
  try {
    const list = await api.myReservations()
    announceChanges(list)
    items.value = list
    updatedAt.value = new Date()
    error.value = ''
  } catch (e) {
    if (!silent) error.value = e.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  load()
  timer = setInterval(() => load(true), REFRESH_MS)
})

onBeforeUnmount(() => clearInterval(timer))
</script>

<template>
  <section>
    <header class="page-head">
      <div>
        <h1>Mes réservations</h1>
        <p class="lead">
          Tes demandes et les réservations faites pour toi, avec leur statut. La page se met à jour toute seule.
        </p>
      </div>
      <button class="btn btn--zem" type="button" @click="requesting = true">
        <Icon name="plus" /> Demander un scooter
      </button>
    </header>

    <p v-if="updatedAt" class="sync">
      <Icon name="refresh" :size="14" /> Mis à jour à {{ timeLabel(updatedAt) }}
      <button class="link" type="button" @click="load()">Actualiser</button>
    </p>

    <p v-if="error" class="notice notice--bad" role="alert">
      {{ error }}
      <button class="link" type="button" @click="load()">Réessayer</button>
    </p>

    <ul v-if="loading" class="grid-cards" aria-busy="true">
      <li v-for="n in 3" :key="n" class="skeleton skeleton--card"></li>
    </ul>

    <EmptyState
      v-else-if="!items.length && !error"
      title="Aucune demande pour le moment"
      text="Demande un scooter avec son numéro. Le propriétaire répondra, et tu verras sa réponse ici."
    >
      <button class="btn btn--zem" type="button" @click="requesting = true">
        <Icon name="plus" /> Demander un scooter
      </button>
    </EmptyState>

    <ul v-else-if="items.length" class="grid-cards grid-cards--wide">
      <li v-for="r in items" :key="r.id" class="trip">
        <div class="trip__art" :style="stageStyle(r.scooterId)">
          <ScooterArt :variant="r.scooterId" />
        </div>
        <div class="trip__body">
          <div class="trip__top">
            <PlateBadge :plate="r.plate" small />
            <StatusBadge :status="r.status" />
          </div>
          <h2 class="trip__day">{{ longDayLabel(r.startAt) }}</h2>
          <p class="trip__time">{{ rangeLabel(r.startAt, r.endAt) }}</p>

          <p v-if="r.status === 'PENDING'" class="hint">Le propriétaire n'a pas encore répondu.</p>
          <p v-else-if="r.status === 'ACCEPTED'" class="hint hint--ok">
            Réservation confirmée. Récupère le scooter à l'heure prévue.
          </p>
          <div v-else class="refusal">
            <strong>Motif du refus</strong>
            <p>{{ r.refusalReason || 'Aucun motif indiqué.' }}</p>
          </div>
        </div>
      </li>
    </ul>

    <RequestModal :open="requesting" @close="requesting = false" @done="load()" />
  </section>
</template>
