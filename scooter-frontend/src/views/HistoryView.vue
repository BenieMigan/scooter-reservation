<script setup>
import { ref, computed, onMounted } from 'vue'
import Icon from '../components/Icon.vue'
import PlateBadge from '../components/PlateBadge.vue'
import StatusBadge from '../components/StatusBadge.vue'
import EmptyState from '../components/EmptyState.vue'
import { api } from '../api'
import { rangeLabel, dayNumber, monthLabel } from '../format'

const items = ref([])
const loading = ref(true)
const error = ref('')
const filter = ref('ALL')

const filters = [
  { value: 'ALL', label: 'Toutes' },
  { value: 'PENDING', label: 'En attente' },
  { value: 'ACCEPTED', label: 'Acceptées' },
  { value: 'REFUSED', label: 'Refusées' },
]

function count(value) {
  return value === 'ALL' ? items.value.length : items.value.filter((r) => r.status === value).length
}

const visible = computed(() =>
  filter.value === 'ALL' ? items.value : items.value.filter((r) => r.status === filter.value),
)

async function load() {
  loading.value = true
  error.value = ''
  try {
    items.value = await api.history()
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <section>
    <header class="page-head">
      <div>
        <h1>Historique</h1>
        <p class="lead">Toutes les réservations de tes scooters, de la plus récente à la plus ancienne.</p>
      </div>
      <button class="btn btn--ghost" type="button" @click="load">
        <Icon name="refresh" :size="16" /> Actualiser
      </button>
    </header>

    <p v-if="error" class="notice notice--bad" role="alert">
      {{ error }}
      <button class="link" type="button" @click="load">Réessayer</button>
    </p>

    <div v-if="!loading && items.length" class="chips" role="group" aria-label="Filtrer par statut">
      <button
        v-for="f in filters"
        :key="f.value"
        type="button"
        :class="['chip', { 'is-active': filter === f.value }]"
        :aria-pressed="filter === f.value"
        @click="filter = f.value"
      >
        {{ f.label }}
        <span class="chip__count">{{ count(f.value) }}</span>
      </button>
    </div>

    <div v-if="loading" class="logs" aria-busy="true">
      <div v-for="n in 4" :key="n" class="skeleton skeleton--log"></div>
    </div>

    <EmptyState
      v-else-if="!items.length && !error"
      title="Rien dans l'historique pour l'instant"
      text="Les réservations que tu crées et les demandes que tu reçois s'afficheront ici."
    />

    <p v-else-if="items.length && !visible.length" class="notice">Aucune réservation avec ce statut.</p>

    <ul v-else-if="visible.length" class="logs">
      <li v-for="r in visible" :key="r.id" class="log">
        <div class="log__date" aria-hidden="true">
          <strong>{{ dayNumber(r.startAt) }}</strong>
          <span>{{ monthLabel(r.startAt) }}</span>
        </div>
        <div class="log__main">
          <p class="log__who">
            <strong>{{ r.requesterName }}</strong>
            <PlateBadge :plate="r.plate" small />
          </p>
          <p class="log__when">{{ rangeLabel(r.startAt, r.endAt) }}</p>
          <p v-if="r.status === 'REFUSED' && r.refusalReason" class="log__reason">
            Motif du refus : {{ r.refusalReason }}
          </p>
        </div>
        <StatusBadge :status="r.status" />
      </li>
    </ul>
  </section>
</template>
