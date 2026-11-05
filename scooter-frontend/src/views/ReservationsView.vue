<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
    getMyScooters,
    createReservation
} from '../services/scooterService';

const router = useRouter();
const route = useRoute();

const scooters = ref([]);
const loadingScooters = ref(false);
const submitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const form = ref({
    scooterId: '',
    email: '',
    startAt: '',
    endAt: ''
});

// Gestion des erreurs
function getErrorMessage(error) {
    const data = error.response?.data;

    if (data?.['hydra:description']) return data['hydra:description'];
    if (data?.detail) return data.detail;

    if (data?.violations?.length) {
        return data.violations
            .map(violation => violation.message)
            .join(' ');
    }

    return error.message || 'Une erreur est survenue.';
}

// Charger les scooters du propriétaire
async function loadScooters() {
    loadingScooters.value = true;
    errorMessage.value = '';

    try {
        const response = await getMyScooters();
        const data = response.data;

        scooters.value = data['hydra:member']
            ?? data.member
            ?? data;

        if (!Array.isArray(scooters.value)) {
            scooters.value = [];
        }
    } catch (error) {
        errorMessage.value = getErrorMessage(error);
    } finally {
        loadingScooters.value = false;
    }
}

// Convertir la date en format ISO
function toIsoDate(dateTimeLocal) {
    return new Date(dateTimeLocal).toISOString();
}

// Créer une réservation
async function handleCreateReservation() {
    errorMessage.value = '';
    successMessage.value = '';

    const email = form.value.email.trim();

    if (
        !form.value.scooterId ||
        !email ||
        !form.value.startAt ||
        !form.value.endAt
    ) {
        errorMessage.value = 'Veuillez remplir tous les champs.';
        return;
    }

    const startDate = new Date(form.value.startAt);
    const endDate = new Date(form.value.endAt);

    if (
        Number.isNaN(startDate.getTime()) ||
        Number.isNaN(endDate.getTime())
    ) {
        errorMessage.value = 'Veuillez saisir des dates valides.';
        return;
    }

    if (startDate <= new Date()) {
        errorMessage.value = 'La date de début doit être dans le futur.';
        return;
    }

    if (endDate <= startDate) {
        errorMessage.value = 'La date de fin doit être après la date de début.';
        return;
    }

    submitting.value = true;

    try {
        await createReservation({
            scooterId: Number(form.value.scooterId),
            email: email,
            startAt: toIsoDate(form.value.startAt),
            endAt: toIsoDate(form.value.endAt)
        });

        successMessage.value =
            'La réservation a été créée avec succès.';

        form.value = {
            scooterId: '',
            email: '',
            startAt: '',
            endAt: ''
        };

    } catch (error) {
        errorMessage.value = getErrorMessage(error);
    } finally {
        submitting.value = false;
    }
}

// Déconnexion
function logout() {
    localStorage.removeItem('token');
    router.push('/login');
}

// Chargement initial et récupération du scooter dans l'URL
onMounted(async () => {
    await loadScooters();

    const scooterIdFromUrl = route.query.scooterId;

    if (typeof scooterIdFromUrl === 'string') {
        const scooterExists = scooters.value.some(
            scooter => String(scooter.id) === scooterIdFromUrl
        );

        if (scooterExists) {
            form.value.scooterId = scooterIdFromUrl;
        }
    }
});
</script>

<template>
    <main class="reservation-page">

        <header class="topbar">
            <div class="brand">
                <span class="brand-icon">🛵</span>
                <span>
                    Scooter<span class="brand-highlight">App</span>
                </span>
            </div>

            <nav class="topbar-actions">
                <button
                    class="nav-button"
                    type="button"
                    @click="router.push('/dashboard')"
                >
                    Mon espace
                </button>

                <button
                    class="logout-button"
                    type="button"
                    @click="logout"
                >
                    Déconnexion
                </button>
            </nav>
        </header>

        <section class="page-content">

            <div class="welcome">
                <p class="eyebrow">GESTION DES RÉSERVATIONS</p>

                <h1>Réserver un scooter</h1>

                <p>
                    Choisissez un scooter, indiquez la personne autorisée
                    et définissez la période d'utilisation.
                </p>
            </div>

            <div class="reservation-layout">

                <section class="form-card">

                    <div class="card-heading">
                        <div>
                            <p class="eyebrow">NOUVELLE DEMANDE</p>
                            <h2>Informations de réservation</h2>
                        </div>

                        <span class="heading-icon">📅</span>
                    </div>

                    <form @submit.prevent="handleCreateReservation">

                        <!-- Choisir un scooter -->
                        <div class="form-group">

                            <label for="scooter">
                                Scooter à réserver
                            </label>

                            <div
                                v-if="loadingScooters"
                                class="field-info"
                            >
                                Chargement de vos scooters...
                            </div>

                            <select
                                v-else
                                id="scooter"
                                v-model="form.scooterId"
                                required
                            >
                                <option value="" disabled>
                                    -- Choisir un scooter --
                                </option>

                                <option
                                    v-for="scooter in scooters"
                                    :key="scooter.id"
                                    :value="String(scooter.id)"
                                >
                                    {{ scooter.model }} — {{ scooter.plate }}
                                </option>
                            </select>

                            <p
                                v-if="!loadingScooters && scooters.length === 0"
                                class="field-info"
                            >
                                Vous n'avez pas encore de scooter enregistré.
                            </p>

                        </div>

                        <!-- Email de la personne -->
                        <div class="form-group">

                            <label for="email">
                                Email de la personne autorisée
                            </label>

                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="exemple@gmail.com"
                                autocomplete="email"
                                required
                            />

                            <small>
                                Cette personne doit déjà avoir un compte
                                et avoir reçu l'autorisation d'utiliser
                                le scooter sélectionné.
                            </small>

                        </div>

                        <!-- Dates de réservation -->
                        <div class="date-grid">

                            <div class="form-group">

                                <label for="startAt">
                                    Date et heure de début
                                </label>

                                <input
                                    id="startAt"
                                    v-model="form.startAt"
                                    type="datetime-local"
                                    required
                                />

                            </div>

                            <div class="form-group">

                                <label for="endAt">
                                    Date et heure de fin
                                </label>

                                <input
                                    id="endAt"
                                    v-model="form.endAt"
                                    type="datetime-local"
                                    required
                                />

                            </div>

                        </div>

                        <!-- Messages -->
                        <p
                            v-if="errorMessage"
                            class="message error"
                        >
                            {{ errorMessage }}
                        </p>

                        <p
                            v-if="successMessage"
                            class="message success"
                        >
                            {{ successMessage }}
                        </p>

                        <!-- Bouton de confirmation -->
                        <button
                            class="submit-button"
                            type="submit"
                            :disabled="
                                submitting ||
                                loadingScooters ||
                                scooters.length === 0
                            "
                        >
                            {{
                                submitting
                                    ? 'Création en cours...'
                                    : 'Confirmer la réservation'
                            }}
                        </button>

                    </form>

                </section>

                <!-- Informations complémentaires -->
                <aside class="info-card">

                    <div class="info-icon">🔐</div>

                    <h2>Avant de réserver</h2>

                    <ul>
                        <li>
                            Vous devez être connecté avec le compte
                            propriétaire du scooter.
                        </li>

                        <li>
                            La personne doit posséder un compte
                            sur ScooterApp.
                        </li>

                        <li>
                            Vous devez lui avoir accordé l'accès
                            au scooter auparavant.
                        </li>

                        <li>
                            La période choisie doit être dans le futur
                            et le scooter doit être disponible.
                        </li>
                    </ul>

                    <div class="info-note">

                        <strong>À savoir</strong>

                        <p>
                            La réservation est acceptée directement
                            par le propriétaire lorsque toutes les
                            vérifications du serveur sont validées.
                        </p>

                    </div>

                </aside>

            </div>

        </section>

    </main>
</template>

<style scoped>
.reservation-page {
    min-height: 100vh;
    background: #f7f7fb;
    color: #202033;
    font-family: Inter, Arial, sans-serif;
}

.topbar {
    min-height: 76px;
    padding: 0 7%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #eeeeF3;
}

.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 21px;
    font-weight: 800;
}

.brand-icon {
    font-size: 27px;
}

.brand-highlight {
    color: #6d5ce7;
}

.topbar-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.nav-button,
.logout-button {
    border: 1px solid #e7e5ef;
    background: white;
    padding: 10px 15px;
    border-radius: 9px;
    cursor: pointer;
    color: #55536a;
    font-weight: 600;
}

.page-content {
    max-width: 1120px;
    margin: 0 auto;
    padding: 55px 24px;
}

.welcome {
    margin-bottom: 35px;
}

.eyebrow {
    color: #7665e8;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.welcome h1 {
    font-size: 34px;
    margin: 9px 0;
}

.welcome > p:last-child {
    color: #77768a;
    line-height: 1.6;
}

.reservation-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(260px, 0.8fr);
    gap: 24px;
    align-items: start;
}

.form-card,
.info-card {
    background: white;
    border: 1px solid #eeedf4;
    border-radius: 17px;
    padding: 27px;
    box-shadow: 0 8px 30px rgba(30, 25, 70, 0.035);
}

.card-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}

.card-heading .eyebrow {
    margin: 0 0 7px;
}

.card-heading h2,
.info-card h2 {
    margin: 0;
    font-size: 20px;
}

.heading-icon {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    background: #f0edff;
    font-size: 21px;
}

.form-group {
    margin-bottom: 21px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 9px;
}

.form-group input,
.form-group select {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #e6e4ed;
    border-radius: 10px;
    padding: 14px;
    font-size: 14px;
    outline: none;
    background: white;
    color: #29283a;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #7665e8;
    box-shadow: 0 0 0 3px #7665e81c;
}

.form-group small {
    display: block;
    margin-top: 8px;
    color: #77768a;
    font-size: 12px;
    line-height: 1.5;
}

.date-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.submit-button {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 10px;
    background: #6d5ce7;
    color: white;
    font-weight: 700;
    cursor: pointer;
}

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.message {
    padding: 11px;
    border-radius: 8px;
    font-size: 13px;
}

.error {
    background: #fff0f0;
    color: #c03939;
}

.success {
    background: #eaf8ef;
    color: #237b43;
}

.field-info {
    color: #77768a;
    font-size: 13px;
    margin-top: 8px;
}

.info-card {
    background: #fbfaff;
}

.info-icon {
    font-size: 30px;
    margin-bottom: 15px;
}

.info-card ul {
    padding-left: 20px;
    color: #68677a;
    font-size: 13px;
    line-height: 1.8;
}

.info-note {
    margin-top: 22px;
    padding: 15px;
    border-radius: 11px;
    background: #f0edff;
    color: #51469e;
    font-size: 13px;
    line-height: 1.6;
}

.info-note p {
    margin: 6px 0 0;
}

@media (max-width: 800px) {
    .reservation-layout {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 600px) {
    .page-content {
        padding: 35px 17px;
    }

    .topbar {
        padding: 0 17px;
    }

    .date-grid {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .brand {
        font-size: 18px;
    }
}
</style>