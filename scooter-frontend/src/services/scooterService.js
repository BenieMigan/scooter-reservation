import api from '../api/axios';

// Enregistrer un scooter
export const registerScooter = (scooterData) => {
    return api.post('auth/register/scooters', scooterData);
};

// Récupérer les scooters de l'utilisateur connecté
export const getMyScooters = () => {
    return api.get('scooters');
};

// Donner accès à un scooter à une autre personne
export const grantScooterAccess = (accessData) => {
    return api.post('scooter-access', accessData);
};

export const createReservation = (reservationData) => {
    return api.post('reservations', reservationData);
};

// Récupérer toutes les réservations de l'utilisateur connecté
export const getMyReservations = () => {
    return api.get('my-reservations');
};

export const getAvailableScooters = () => {
    return api.get('available-scooters');
};