import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// Le front tourne sur http://localhost:5173.
// Toutes les requêtes /api/... sont relayées vers ton back Symfony (localhost:8000).
// Avantage : pas besoin de configurer le CORS côté Symfony.
// Pour viser une autre adresse : API_TARGET=http://127.0.0.1:8001 npm run dev
export default defineConfig({
  plugins: [vue()],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: process.env.API_TARGET || 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
})
