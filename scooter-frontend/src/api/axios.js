
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api/',
  headers: {
    'Content-Type': 'application/ld+json',
    'Accept': 'application/ld+json'
  }
});

// Ajouter automatiquement le JWT aux requêtes
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export default api;
