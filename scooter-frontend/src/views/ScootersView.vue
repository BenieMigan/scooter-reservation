<script setup>
import { ref, onMounted } from 'vue'
import Icon from '../components/Icon.vue'
import ScooterArt from '../components/ScooterArt.vue'
import PlateBadge from '../components/PlateBadge.vue'
import EmptyState from '../components/EmptyState.vue'
import AddScooterModal from '../components/AddScooterModal.vue'
import GrantAccessModal from '../components/GrantAccessModal.vue'
import ReserveForModal from '../components/ReserveForModal.vue'
import { api } from '../api'
import { toast } from '../toast'
import { stageStyle } from '../stage'

const scooters = ref([])
const loading = ref(true)
const error = ref('')

const adding = ref(false)
const granting = ref(null) // le scooter sur lequel on autorise quelqu'un
const reserving = ref(null) // le scooter qu'on réserve pour quelqu'un

async function load() {
  loading.value = true
  error.value = ''
  try {
    scooters.value = await api.listScooters()
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

async function copyId(scooter) {
  try {
    await navigator.clipboard.writeText(String(scooter.id))
    toast(`Numéro ${scooter.id} copié. Donne-le à la personne qui veut réserver.`, 'success')
  } catch {
    toast(`Le numéro de ce scooter est ${scooter.id}.`, 'info')
  }
}

onMounted(load)
</script>

<template>
  <section>
    <header class="page-head">
      <div>
        <h1>Mes scooters</h1>
        <p class="lead">
          Les scooters que tu prêtes. Donne le numéro d'un scooter à quelqu'un pour qu'il puisse t'envoyer une demande.
        </p>
      </div>
      <button class="btn btn--zem" type="button" @click="adding = true">
        <Icon name="plus" /> Ajouter un scooter
      </button>
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
      title="Tu n'as pas encore de scooter"
      text="Ajoute le premier pour commencer à autoriser des proches et à recevoir des demandes."
    >
      <button class="btn btn--zem" type="button" @click="adding = true">
        <Icon name="plus" /> Ajouter un scooter
      </button>
    </EmptyState>

    <ul v-else-if="scooters.length" class="grid-cards">
      <li v-for="s in scooters" :key="s.id" class="scooter-card">
        <div class="scooter-card__stage" :style="stageStyle(s.id)">
          <ScooterArt :variant="s.id" />
        </div>
        <div class="scooter-card__body">
          <div class="scooter-card__title">
            <h2>{{ s.model }}</h2>
            <PlateBadge :plate="s.plate" />
          </div>
          <button class="idchip" type="button" :title="'Copier le numéro du scooter'" @click="copyId(s)">
            <span>Numéro {{ s.id }}</span>
            <Icon name="copy" :size="15" />
          </button>
          <div class="scooter-card__actions">
            <button class="btn btn--ghost btn--sm" type="button" @click="granting = s">
              <Icon name="key" :size="16" /> Autoriser quelqu'un
            </button>
            <button class="btn btn--primary btn--sm" type="button" @click="reserving = s">
              <Icon name="calendar" :size="16" /> Réserver pour quelqu'un
            </button>
          </div>
        </div>
      </li>
    </ul>

    <AddScooterModal :open="adding" @close="adding = false" @done="load" />
    <GrantAccessModal :open="!!granting" :scooter="granting" @close="granting = null" />
    <ReserveForModal :open="!!reserving" :scooter="reserving" @close="reserving = null" />
  </section>
</template>
