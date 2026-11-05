<script setup>
import { ref, onMounted } from 'vue'
import Icon from '../components/Icon.vue'
import ScooterArt from '../components/ScooterArt.vue'
import PlateBadge from '../components/PlateBadge.vue'
import EmptyState from '../components/EmptyState.vue'
import RefuseModal from '../components/RefuseModal.vue'
import { api } from '../api'
import { toast } from '../toast'
import { stageStyle } from '../stage'
import { rangeLabel } from '../format'
import { pendingCount } from '../state'

const items = ref([])
const loading = ref(true)
const error = ref('')
const busyId = ref(null) // demande en cours d'acceptation
const refusing = ref(null) // demande qu'on est en train de refuser

async function load() {
  loading.value = true
  error.value = ''
  try {
    items.value = await api.receivedRequests()
    pendingCount.value = items.value.length
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

function removeFromList(id) {
  items.value = items.value.filter((r) => r.id !== id)
  pendingCount.value = items.value.length
}

async function accept(request) {
  busyId.value = request.id
  try {
    await api.acceptRequest(request.id)
    toast(`Demande de ${request.requesterName} acceptée.`, 'success')
    removeFromList(request.id)
  } catch (e) {
    // Ex. : un autre créneau déjà accepté se chevauche (409)
    toast(e.message, 'error')
  } finally {
    busyId.value = null
  }
}

function onRefused(request) {
  toast(`Demande de ${request.requesterName} refusée.`, 'success')
  removeFromList(request.id)
}

onMounted(load)
</script>

<template>
  <section>
    <header class="page-head">
      <div>
        <h1>Demandes reçues</h1>
        <p class="lead">Les personnes qui veulent utiliser l'un de tes scooters. Accepte, ou refuse en expliquant pourquoi.</p>
      </div>
      <button class="btn btn--ghost" type="button" @click="load">
        <Icon name="refresh" :size="16" /> Actualiser
      </button>
    </header>

    <p v-if="error" class="notice notice--bad" role="alert">
      {{ error }}
      <button class="link" type="button" @click="load">Réessayer</button>
    </p>

    <ul v-if="loading" class="rows" aria-busy="true">
      <li v-for="n in 3" :key="n" class="skeleton skeleton--row"></li>
    </ul>

    <EmptyState
      v-else-if="!items.length && !error"
      title="Aucune demande en attente"
      text="Quand quelqu'un demandera l'un de tes scooters, la demande apparaîtra ici."
    />

    <ul v-else-if="items.length" class="rows">
      <li v-for="r in items" :key="r.id" class="row">
        <div class="row__art" :style="stageStyle(r.scooterId)">
          <ScooterArt :variant="r.scooterId" />
        </div>
        <div class="row__main">
          <p class="row__who">
            <strong>{{ r.requesterName }}</strong>
            <span>{{ r.requesterEmail }}</span>
          </p>
          <p class="row__when">{{ rangeLabel(r.startAt, r.endAt) }}</p>
          <PlateBadge :plate="r.plate" small />
        </div>
        <div class="row__actions">
          <button class="btn btn--ghost btn--sm" type="button" :disabled="busyId === r.id" @click="refusing = r">
            <Icon name="x" :size="16" /> Refuser
          </button>
          <button class="btn btn--primary btn--sm" type="button" :disabled="busyId === r.id" @click="accept(r)">
            <Icon name="check" :size="16" /> {{ busyId === r.id ? 'Acceptation…' : 'Accepter' }}
          </button>
        </div>
      </li>
    </ul>

    <RefuseModal :open="!!refusing" :request="refusing" @close="refusing = null" @done="onRefused" />
  </section>
</template>
