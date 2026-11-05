import { reactive } from 'vue'

// Le token JWT reçu à la connexion est gardé dans le navigateur (localStorage),
// pour rester connecté après un rafraîchissement de la page.
const KEY = 'zemo_token'

function read() {
  try {
    return localStorage.getItem(KEY)
  } catch {
    return null
  }
}

function write(value) {
  try {
    if (value) localStorage.setItem(KEY, value)
    else localStorage.removeItem(KEY)
  } catch {
    /* navigation privée : on ignore */
  }
}

// Un JWT = 3 parties séparées par des points. La 2e contient les infos
// (email dans "username", expiration dans "exp"). Elle est lisible mais signée.
function decode(token) {
  try {
    const part = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')
    const bytes = Uint8Array.from(atob(part), (c) => c.charCodeAt(0))
    return JSON.parse(new TextDecoder().decode(bytes))
  } catch {
    return null
  }
}

export const auth = reactive({
  token: read(),

  get payload() {
    return this.token ? decode(this.token) : null
  },
  get email() {
    return this.payload?.username ?? ''
  },
  get roles() {
    return this.payload?.roles ?? []
  },
  get isOwner() {
    return this.roles.includes('ROLE_OWNER')
  },
  // Page d'accueil selon le rôle : propriétaire → /scooters, utilisateur → /disponibles
  get home() {
    return this.isOwner ? '/scooters' : '/disponibles'
  },
  get isLoggedIn() {
    const p = this.payload
    return !!this.token && (!p?.exp || p.exp * 1000 > Date.now())
  },

  setToken(token) {
    this.token = token
    write(token)
  },
  logout() {
    this.setToken(null)
  },
})
