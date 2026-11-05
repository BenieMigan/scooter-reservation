
import { createRouter, createWebHistory } from 'vue-router';

import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';
import MyReservationsView from '../views/MyReservationsView.vue';
import AvailableScootersView from '../views/AvailableScootersView.vue';

const routes = [
    {
        path: '/',
        redirect: '/login',
    },
    {
        path: '/login',
        name: 'login',
        component: LoginView,
    },
    
    {
        path: '/register',
        name: 'register',
        component: RegisterView,
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        redirect: { name: 'scooters' },
        meta: { requiresAuth: true },
    },
    {
        path: '/scooters',
        name: 'scooters',
        component: () => import('../views/ScootersView.vue'),
        meta: { requiresAuth: true },
    },
   {
    path: '/reservations',
    name: 'reservations',
    component: () => import('../views/ReservationsView.vue'),
    meta: { requiresAuth: true }
},
{
    path: '/my-reservations',
    name: 'my-reservations',
    component: MyReservationsView,
    meta: { requiresAuth: true }
},
{
    path: '/available-scooters',
    name: 'available-scooters',
    component: AvailableScootersView,
    meta: { requiresAuth: true }
}
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const token = localStorage.getItem('token');

    if (to.meta.requiresAuth && !token) {
        return { name: 'login' };
    }

    if (
        (to.name === 'login' || to.name === 'register') &&
        token
    ) {
        return { name: 'scooters' };
    }
});

export default router;
