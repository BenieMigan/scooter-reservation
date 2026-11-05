
<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { registerUser } from '../services/authService';

const router = useRouter();

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

const loading = ref(false);
const errorMessage = ref('');
const showPassword = ref(false);

async function handleRegister() {
    errorMessage.value = '';

    if (password.value.length < 6) {
        errorMessage.value =
            'Le mot de passe doit contenir au moins 6 caractères.';
        return;
    }

    if (password.value !== confirmPassword.value) {
        errorMessage.value =
            'Les deux mots de passe ne correspondent pas.';
        return;
    }

    loading.value = true;

    try {
        await registerUser({
            name: name.value.trim(),
            email: email.value.trim(),
            password: password.value,
        });

        await router.push({
            name: 'login',
            query: { registered: 'success' },
        });
    } catch (error) {
        const data = error.response?.data;

        errorMessage.value =
            data?.violations?.map(item => item.message).join(' ') ||
            data?.detail ||
            data?.message ||
            "Une erreur est survenue pendant l'inscription.";
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <main class="auth-page">
        <section class="auth-visual">
            <div class="brand">
                <span class="brand-logo">S</span>
                <span>ScootGo</span>
            </div>

            <div class="visual-content">
                <span class="eyebrow">MOVE WITHOUT LIMITS</span>

                <h1>
                    Votre liberté
                    <span>commence ici.</span>
                </h1>

                <p>
                    Découvrez une nouvelle manière de vous déplacer.
                    Choisissez votre scooter et partez à l'aventure.
                </p>

                <div class="scooter-illustration">
                    <div class="scooter-circle"></div>
                    <span>🛵</span>
                </div>

                <div class="visual-footer">
                    <span>Simple</span>
                    <span>Rapide</span>
                    <span>Pratique</span>
                </div>
            </div>
        </section>

        <section class="auth-content">
            <div class="form-container">
                <div class="mobile-brand">
                    <span class="brand-logo">S</span>
                    ScootGo
                </div>

                <div class="form-heading">
                    <span class="eyebrow">REJOIGNEZ-NOUS</span>
                    <h2>Créer un compte</h2>
                    <p>Inscrivez-vous pour réserver votre prochain scooter.</p>
                </div>

                <form @submit.prevent="handleRegister">
                    <div v-if="errorMessage" class="alert-error">
                        {{ errorMessage }}
                    </div>

                    <div class="form-group">
                        <label>Nom complet</label>
                        <input
                            v-model.trim="name"
                            type="text"
                            placeholder="Votre nom complet"
                            autocomplete="name"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label>Adresse email</label>
                        <input
                            v-model.trim="email"
                            type="email"
                            placeholder="exemple@email.com"
                            autocomplete="email"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label>Mot de passe</label>
                        <div class="password-field">
                            <input
                                v-model="password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="6 caractères minimum"
                                autocomplete="new-password"
                                required
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                            >
                                {{ showPassword ? 'Masquer' : 'Afficher' }}
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirmer le mot de passe</label>
                        <input
                            v-model="confirmPassword"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Confirmez votre mot de passe"
                            autocomplete="new-password"
                            required
                        />
                    </div>

                    <button class="submit-button" :disabled="loading">
                        {{ loading ? 'Création du compte...' : 'Créer mon compte' }}
                        <span v-if="!loading">→</span>
                    </button>
                </form>

                <p class="form-switch">
                    Vous avez déjà un compte ?
                    <RouterLink :to="{ name: 'login' }">
                        Se connecter
                    </RouterLink>
                </p>

                <p class="form-note">
                    Vos informations sont protégées et utilisées pour gérer
                    votre compte.
                </p>
            </div>
        </section>
    </main>
</template>
