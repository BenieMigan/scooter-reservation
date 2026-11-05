// Tout ce que le front dit au back passe par CE fichier.
// Chaque fonction de l'objet `api` correspond à une route de ton API Symfony.
import { auth } from './auth'
import { toast } from './toast'

// '' = même adresse que le front (le proxy Vite relaie /api vers Symfony).
// Pour viser directement une autre adresse : VITE_API_URL=http://localhost:8000 dans un fichier .env
const API = (import.meta.env.VITE_API_URL || '') + '/api'

// Si ton Swagger affiche encore /api/auth/register/scooters, deux choix :
//   1. corriger le uriTemplate de ScooterResource côté Symfony (recommandé : '/scooters')
//   2. ou mettre ici '/auth/register/scooters'
const SCOOTER_CREATE_PATH = '/scooters'

export class ApiError extends Error {
  constructor(message, status = 0) {
    super(message)
    this.status = status
  }
}

async function toError(res, isLogin) {
  let data = null
  try {
    data = await res.json()
  } catch {
    /* pas de JSON dans la réponse */
  }
  const violations = data?.violations || data?.['hydra:violations'] || []
  let message = data?.detail || data?.description || data?.['hydra:description'] || data?.message || ''

  if (violations.length) message = violations.map((v) => v.message).join(' ')
  if (res.status === 401) {
    message = isLogin ? 'Email ou mot de passe incorrect.' : 'Ta session a expiré. Reconnecte-toi.'
  }
  if (res.status >= 500) {
    message = `Le serveur a rencontré une erreur (${res.status}). Regarde var/log/dev.log côté Symfony.`
  }
  return new ApiError(message || `Erreur ${res.status}`, res.status)
}

async function request(path, { method = 'GET', body, plainJson = false, isLogin = false } = {}) {
  const headers = { Accept: plainJson ? 'application/json' : 'application/ld+json' }
  if (body !== undefined) {
    // La connexion (json_login de Symfony) veut du JSON simple.
    // Les routes API Platform acceptent le JSON-LD.
    headers['Content-Type'] = plainJson ? 'application/json' : 'application/ld+json'
  }
  if (auth.token && !isLogin) headers.Authorization = `Bearer ${auth.token}`

  let res
  try {
    res = await fetch(API + path, {
      method,
      headers,
      body: body !== undefined ? JSON.stringify(body) : undefined,
    })
  } catch {
    throw new ApiError("Impossible de joindre le serveur. Vérifie que l'API tourne sur le port 8000.")
  }

  if (!res.ok) {
    const error = await toError(res, isLogin)
    if (res.status === 401 && !isLogin) {
      toast(error.message, 'error')
      auth.logout() // App.vue renvoie alors vers la page de connexion
    }
    throw error
  }
  if (res.status === 204) return null
  // Réponse vide possible (ex : inscription, 201 sans contenu)
  const text = await res.text()
  return text ? JSON.parse(text) : null
}

// API Platform renvoie les listes dans "member" (ou "hydra:member" selon la version)
function members(data) {
  return data?.member ?? data?.['hydra:member'] ?? (Array.isArray(data) ? data : [])
}

// Une réservation vue par l'écran
function reservation(r) {
  return {
    id: r.id,
    scooterId: r.scooterId,
    plate: r.plate,
    requesterName: r.requesterName,
    requesterEmail: r.requesterEmail,
    startAt: new Date(r.startAt),
    endAt: new Date(r.endAt),
    status: r.status,
    refusalReason: r.refusalReason ?? null,
  }
}

export const api = {
  // 1. Inscription : POST /api/auth/register
  register: ({ name, email, password, role }) =>
    request('/auth/register', { method: 'POST', body: { name, email, password, role } }),

  // 2. Connexion : POST /api/auth/login  → { token }
  login: (email, password) =>
    request('/auth/login', { method: 'POST', body: { email, password }, plainJson: true, isLogin: true }),

  // 3. Enregistrer mon scooter : POST /api/scooters
  createScooter: ({ model, plate }) =>
    request(SCOOTER_CREATE_PATH, { method: 'POST', body: { model, plate } }),

  // 4. Voir mes scooters : GET /api/scooters
  listScooters: async () => members(await request('/scooters')),

  // 5. Autoriser une personne : POST /api/scooter-access
  grantAccess: ({ scooterId, email }) =>
    request('/scooter-access', { method: 'POST', body: { scooterId, email } }),

  // Scooters des autres propriétaires : GET /api/available-scooters
  availableScooters: async () => members(await request('/available-scooters')),

  // 6. Réserver pour une personne autorisée : POST /api/reservations
  createReservation: ({ scooterId, email, startAt, endAt }) =>
    request('/reservations', { method: 'POST', body: { scooterId, email, startAt, endAt } }),

  // 7. Faire une demande de réservation : POST /api/reservation-requests
  requestReservation: ({ scooterId, startAt, endAt }) =>
    request('/reservation-requests', { method: 'POST', body: { scooterId, startAt, endAt } }),

  // 8. Demandes reçues (en attente) : GET /api/reservation-requests
  receivedRequests: async () => members(await request('/reservation-requests')).map(reservation),

  // 9. Accepter : POST /api/reservation-requests/{id}/accept
  acceptRequest: (id) => request(`/reservation-requests/${id}/accept`, { method: 'POST' }),

  // 10. Refuser avec motif : POST /api/reservation-requests/{id}/refuse
  refuseRequest: (id, reason) =>
    request(`/reservation-requests/${id}/refuse`, { method: 'POST', body: { reason } }),

  // 11. Historique du propriétaire : GET /api/reservation-history
  history: async () => members(await request('/reservation-history')).map(reservation),

  // 12. Mes réservations et le statut de mes demandes : GET /api/my-reservations
  myReservations: async () => members(await request('/my-reservations')).map(reservation),
}
