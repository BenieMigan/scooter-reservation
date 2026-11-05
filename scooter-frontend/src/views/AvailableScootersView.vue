<template>
    <div class="page">
        <header class="header">
            <div>
                <span class="eyebrow">ESPACE UTILISATEUR</span>
                <h1>Tous les scooters</h1>
                <p>Découvrez les scooters enregistrés sur notre plateforme.</p>
            </div>

            <button class="back-button" @click="router.push('/dashboard')">
                ← Mon espace
            </button>
        </header>

        <div class="toolbar">
            <div class="search-box">
                <span>⌕</span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher par modèle ou plaque..."
                />
            </div>

            <span class="count">
                {{ filteredScooters.length }} scooter(s)
            </span>
        </div>

        <div v-if="loading" class="message">
            Chargement des scooters...
        </div>

        <div v-else-if="errorMessage" class="message error">
            {{ errorMessage }}
            <button @click="loadScooters">Réessayer</button>
        </div>

        <div v-else-if="filteredScooters.length === 0" class="empty">
            <div class="empty-icon">🛵</div>
            <h2>Aucun scooter trouvé</h2>
            <p>
                {{ search
                    ? 'Aucun scooter ne correspond à votre recherche.'
                    : 'Aucun scooter n’est actuellement enregistré.' }}
            </p>
        </div>

        <div v-else class="scooters-grid">
            <article
                v-for="scooter in filteredScooters"
                :key="scooter.id"
                class="scooter-card"
            >
                <div class="scooter-image">
                    <span>🛵</span>
                    <span
                        class="availability"
                        :class="scooter.availableNow ? 'available' : 'unavailable'"
                    >
                        {{ scooter.availableNow ? 'Disponible' : 'Indisponible' }}
                    </span>
                </div>

                <div class="card-content">
                    <span class="scooter-id">
                        SCOOTER #{{ scooter.id }}
                    </span>

                    <h2>{{ scooter.model || 'Modèle non renseigné' }}</h2>

                    <div class="info">
                        <span>Plaque d'immatriculation</span>
                        <strong>{{ scooter.plate || 'Non renseignée' }}</strong>
                    </div>

                    <div class="info">
                        <span>Propriétaire</span>
                        <strong>{{ scooter.ownerName || 'Non renseigné' }}</strong>
                    </div>

                    <div class="authorization">
                        <span>Votre autorisation</span>

                        <span
                            class="authorization-status"
                            :class="scooter.authorized ? 'authorized' : 'not-authorized'"
                        >
                            {{ scooter.authorized ? 'Autorisé' : 'Non autorisé' }}
                        </span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getAvailableScooters } from '../services/scooterService';

const router = useRouter();

const scooters = ref([]);
const loading = ref(false);
const errorMessage = ref('');
const search = ref('');

const filteredScooters = computed(() => {
    const value = search.value.toLowerCase().trim();

    if (!value) {
        return scooters.value;
    }

    return scooters.value.filter(scooter =>
        (scooter.model || '').toLowerCase().includes(value) ||
        (scooter.plate || '').toLowerCase().includes(value) ||
        (scooter.ownerName || '').toLowerCase().includes(value)
    );
});

async function loadScooters() {
    loading.value = true;
    errorMessage.value = '';

    try {
        const response = await getAvailableScooters();

        const data = response.data;

        scooters.value =
            data['hydra:member'] ??
            data.member ??
            (Array.isArray(data) ? data : []);

    } catch (error) {
        console.error('Erreur de récupération des scooters :', error);

        errorMessage.value =
            error.response?.data?.['hydra:description'] ||
            error.response?.data?.detail ||
            'Impossible de récupérer les scooters.';
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadScooters();
});
</script>

<style scoped>
.page {
    min-height: 100vh;
    padding: 40px 6%;
    background: #f7f8fc;
    color: #202333;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 35px;
}

.eyebrow {
    color: #6965d8;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2px;
}

h1 {
    font-size: 32px;
    margin: 10px 0;
}

.header p {
    color: #777;
}

.back-button {
    padding: 12px 18px;
    border: 1px solid #dedeea;
    background: white;
    border-radius: 10px;
    cursor: pointer;
}

.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    padding: 0 15px;
    border: 1px solid #e8e8ef;
    border-radius: 12px;
    width: min(450px, 100%);
}

.search-box input {
    width: 100%;
    padding: 14px 0;
    border: none;
    outline: none;
    background: transparent;
}

.count {
    color: #777;
    font-size: 13px;
}

.scooters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 22px;
}

.scooter-card {
    background: white;
    border-radius: 17px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(30, 30, 60, 0.05);
    transition: transform 0.2s;
}

.scooter-card:hover {
    transform: translateY(-4px);
}

.scooter-image {
    height: 155px;
    background: linear-gradient(135deg, #e8e7ff, #f3f1ff);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.scooter-image > span:first-child {
    font-size: 65px;
}

.availability {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 7px 11px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.available {
    background: #e1f7e9;
    color: #187a3b;
}

.unavailable {
    background: #ffe5e5;
    color: #b52d2d;
}

.card-content {
    padding: 22px;
}

.scooter-id {
    color: #7774d9;
    font-size: 11px;
    font-weight: 800;
}

.card-content h2 {
    margin: 9px 0 20px;
    font-size: 20px;
}

.info {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 16px;
}

.info span,
.authorization > span:first-child {
    color: #888;
    font-size: 12px;
}

.info strong {
    font-size: 14px;
}

.authorization {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #eeeef3;
    padding-top: 16px;
    gap: 10px;
}

.authorization-status {
    font-size: 12px;
    font-weight: 700;
}

.authorized {
    color: #187a3b;
}

.not-authorized {
    color: #b52d2d;
}

.message,
.empty {
    background: white;
    padding: 35px;
    text-align: center;
    border-radius: 15px;
}

.error {
    color: #b52d2d;
}

.message button {
    display: block;
    margin: 15px auto 0;
    padding: 10px 18px;
    border: none;
    background: #6965d8;
    color: white;
    border-radius: 8px;
    cursor: pointer;
}

.empty-icon {
    font-size: 50px;
}

.empty p {
    color: #777;
}

@media (max-width: 600px) {
    .page {
        padding: 25px 16px;
    }

    .header,
    .toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    h1 {
        font-size: 26px;
    }
}
</style>