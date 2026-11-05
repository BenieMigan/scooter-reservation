import { createRouter, createWebHistory } from 'vue-router'
import { auth } from './auth'
import { APP_NAME } from './config'
import AuthView from './views/AuthView.vue'
import ScootersView from './views/ScootersView.vue'
import RequestsView from './views/RequestsView.vue'
import HistoryView from './views/HistoryView.vue'
import AvailableView from './views/AvailableView.vue'
import MyReservationsView from './views/MyReservationsView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/connexion', component: AuthView, meta: { public: true, title: 'Connexion' } },
    { path: '/scooters', component: ScootersView, meta: { title: 'Mes scooters', owner: true } },
    { path: '/disponibles', component: AvailableView, meta: { title: 'Scooters disponibles' } },
    { path: '/demandes', component: RequestsView, meta: { title: 'Demandes reçues', owner: true } },
    { path: '/historique', component: HistoryView, meta: { title: 'Historique', owner: true } },
    { path: '/mes-reservations', component: MyReservationsView, meta: { title: 'Mes réservations' } },
    { path: '/', redirect: () => auth.home },
    { path: '/:pathMatch(.*)*', redirect: () => auth.home },
  ],
})

// Garde : sans token valide, on renvoie vers la page de connexion.
router.beforeEach((to) => {
  if (!to.meta.public && !auth.isLoggedIn) {
    return { path: '/connexion', query: { redirect: to.fullPath } }
  }
  if (to.meta.public && auth.isLoggedIn) {
    return auth.home
  }
  // Pages réservées aux propriétaires : un utilisateur simple est renvoyé vers ses scooters disponibles
  if (to.meta.owner && !auth.isOwner) {
    return '/disponibles'
  }
})

router.afterEach((to) => {
  document.title = `${to.meta.title} - ${APP_NAME}`
})

export default router
