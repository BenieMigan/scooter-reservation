
<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink, useRouter, useRoute } from 'vue-router';
import { loginUser } from '../services/authService';

const router = useRouter();
const route = useRoute();

const email = ref('');
const password = ref('');
const loading = ref(false);
const showPassword = ref(false);

const errorMessage = ref('');
const successMessage = ref('');

onMounted(() => {
    if (route.query.registered === 'success') {
        successMessage.value =
            'Votre compte a été créé ! Vous pouvez maintenant vous connecter.';
    }
});

async function handleLogin() {
    errorMessage.value = '';
    successMessage.value = '';

    loading.value = true;

    try {
        await loginUser({
            email: email.value.trim(),
            password: password.value,
        });

        await router.push({ name: 'dashboard' });
    } catch (error) {
        errorMessage.value =
            error.response?.data?.detail ||
            error.response?.data?.message ||
            'Email ou mot de passe incorrect.';
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
                <span class="eyebrow">YOUR CITY. YOUR RIDE.</span>

                <h1>
                    La route vous
                    <span>appartient.</span>
                </h1>

                <p>
                    Connectez-vous et retrouvez votre espace.
                    Votre prochaine destination n'attend que vous.
                </p>

                <div class="scooter-illustration">
                    <div class="scooter-circle"></div>
                    <span>🛵</span>
                </div>

                <div class="visual-footer">
                    <span>Liberté</span>
                    <span>Mobilité</span>
                    <span>Confort</span>
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
                    <span class="eyebrow">BON RETOUR</span>
                    <h2>Ravi de vous revoir !</h2>
                    <p>Connectez-vous pour accéder à votre espace.</p>
                </div>

                <form @submit.prevent="handleLogin">
                    <div v-if="errorMessage" class="alert-error">
                        {{ errorMessage }}
                    </div>

                    <div v-if="successMessage" class="alert-success">
                        {{ successMessage }}
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
                                placeholder="Votre mot de passe"
                                autocomplete="current-password"
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

                    <button class="submit-button" :disabled="loading">
                        {{ loading ? 'Connexion...' : 'Se connecter' }}
                        <span v-if="!loading">→</span>
                    </button>
                </form>

                <p class="form-switch">
                    Vous n'avez pas encore de compte ?
                    <RouterLink :to="{ name: 'register' }">
                        Créer un compte
                    </RouterLink>
                </p>

                <p class="form-note">
                    Connectez-vous pour retrouver vos prochaines réservations.
                </p>
            </div>
        </section>
    </main>
</template>
