
import api from '../api/axios';

// Inscription
export async function registerUser(userData) {
    const response = await api.post('auth/register', userData);
    return response.data;
}

// Connexion
export async function loginUser(credentials) {
    const response = await api.post('auth/login', credentials);

    if (!response.data.token) {
        throw new Error('Impossible de se connecter');
    }

    localStorage.setItem('token', response.data.token);

    return response.data;
}

// Déconnexion
export function logoutUser() {
    localStorage.removeItem('token');
}
