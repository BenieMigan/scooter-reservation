<script setup>
import { ref, onMounted } from 'vue'
import ScooterArt from '../components/ScooterArt.vue'
import PlateBadge from '../components/PlateBadge.vue'
import EmptyState from '../components/EmptyState.vue'
import RequestModal from '../components/RequestModal.vue'
import { api } from '../api'
import { stageStyle } from '../stage'

const scooters = ref([])
const loading = ref(true)
const error = ref('')
const requesting = ref(null) // le scooter sur lequel on fait une demande

async function load() {
  loading.value = true
  error.value = ''
  try {
    scooters.value = await api.availableScooters()
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
        <h1>Scooters disponibles</h1>
        <p class="lead">Les scooters de tous les propriétaires. Envoie une demande : le propriétaire l'accepte ou la refuse.</p>
      </div>
    </header>

    <p v-if="error" class="notice notice--bad" role="alert">
      {{ error }}
      <button class="link" type="button" @click="load">Réessayer</button>
    </p>

    <ul v-if="loading" class="grid-cards" aria-busy="true">
      <li v-for="n in 3" :key="n" class="skeleton skeleton--card"></li>
    </ul>

    <EmptyState
      v-else-if="!scooters.length && !error"
      title="Aucun scooter pour le moment"
      text="Dès qu'un propriétaire enregistre un scooter, il apparaît ici."
    />

    <ul v-else-if="scooters.length" class="grid-cards">
      <li v-for="s in scooters" :key="s.id" class="scooter-card">
        <div class="scooter-card__stage" :style="stageStyle(s.id)">
          <ScooterArt :variant="s.id" :muted="!s.availableNow" />
        </div>
        <div class="scooter-card__body">
          <div class="scooter-card__title">
            <h2>{{ s.model }}</h2>
            <PlateBadge :plate="s.plate" />
          </div>
          <p class="lead">
            Propriétaire : <strong>{{ s.ownerName }}</strong><br />
            <span>{{ s.ownerEmail }}</span>
          </p>
          <p>
            <span :class="['badge', s.availableNow ? 'badge--ok' : 'badge--bad']">
              <span class="badge__dot" aria-hidden="true"></span>{{ s.availableNow ? 'Disponible' : 'Occupé' }}
            </span>
            <span v-if="s.authorized" class="badge badge--ok"><span class="badge__dot" aria-hidden="true"></span>Tu es autorisé</span>
          </p>
          <div class="scooter-card__actions">
            <button v-if="!s.authorized" class="btn btn--primary btn--sm" type="button" @click="requesting = s">
              Demander ce scooter
            </button>
            <p v-else class="lead">Le propriétaire te réserve ce scooter directement.</p>
          </div>
        </div>
      </li>
    </ul>

    <RequestModal :open="!!requesting" :scooter-id="requesting?.id ?? ''" @close="requesting = null" @done="load" />
  </section>
</template>
