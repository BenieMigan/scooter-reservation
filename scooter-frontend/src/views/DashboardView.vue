
<script setup>
import { useRouter } from 'vue-router';
import { logoutUser } from '../services/authService';

const router = useRouter();

function handleLogout() {
    logoutUser();
    router.push({ name: 'login' });
}
</script>

<template>
    <main class="dashboard">
        <div class="dashboard-card">
            <div class="brand-logo">S</div>
            <span class="eyebrow">VOTRE ESPACE SCOOTGO</span>
            <h1>Bienvenue !</h1>
            <p>
                Vous êtes connecté. Préparez-vous à découvrir
                une nouvelle expérience de mobilité.
            </p>

            <button @click="$router.push('/available-scooters')">
                Découvrir nos scooters
            </button>

            <div class="scooter">🛵</div>

            <button @click="handleLogout">
                Se déconnecter
            </button>
        </div>
    </main>
</template>

<style scoped>
.dashboard {
    display: grid;
    min-height: 100vh;
    place-items: center;
    padding: 25px;
    background: #f5f7fa;
}

.dashboard-card {
    width: 100%;
    max-width: 470px;
    padding: 45px;
    border-radius: 20px;
    background: white;
    text-align: center;
    box-shadow: 0 15px 50px #17253a12;
}

.brand-logo {
    display: grid;
    width: 48px;
    height: 48px;
    margin: 0 auto 25px;
    place-items: center;
    border-radius: 13px;
    background: #f28a35;
    color: white;
    font-size: 24px;
    font-weight: 800;
}

.eyebrow {
    color: #e47d2b;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
}

h1 {
    color: #17253a;
}

p {
    color: #8993a1;
    line-height: 1.8;
}

.scooter {
    margin: 25px;
    font-size: 75px;
}

button {
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 10px;
    background: #f28a35;
    color: white;
    font-weight: 800;
    cursor: pointer;
}
</style>
