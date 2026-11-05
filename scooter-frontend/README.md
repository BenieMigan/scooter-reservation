# Zémo : le front (Vue 3)

Interface web de ton application de partage de scooters. Elle utilise tes routes Symfony (`/api/...`).

## Lancer le projet

Il faut Node.js 20 ou plus.

```bash
cd scooter-front
npm install
npm run dev
```

Ouvre ensuite http://localhost:5173.

Ton back Symfony doit tourner en même temps sur `http://localhost:8000`
(par exemple avec `symfony serve --port=8000`).

Le front relaie automatiquement toutes les requêtes `/api/...` vers le back (voir `vite.config.js`).
Tu n'as donc **pas besoin de configurer le CORS** côté Symfony.
Si ton back est ailleurs : `API_TARGET=http://127.0.0.1:8001 npm run dev`.

## Les pages

| Page | Adresse | Routes API utilisées |
|---|---|---|
| Connexion / inscription | `/connexion` | `POST /api/auth/register`, `POST /api/auth/login` |
| Mes scooters | `/scooters` | `GET /api/scooters`, `POST /api/scooters`, `POST /api/scooter-access`, `POST /api/reservations` |
| Demandes reçues | `/demandes` | `GET /api/reservation-requests`, `POST .../{id}/accept`, `POST .../{id}/refuse` |
| Historique | `/historique` | `GET /api/reservation-history` |
| Mes réservations | `/mes-reservations` | `GET /api/my-reservations`, `POST /api/reservation-requests` |

## Où est quoi

| Fichier | Rôle |
|---|---|
| `src/api.js` | **Tout** ce que le front dit au back. Une fonction par route Symfony |
| `src/auth.js` | Garde le token JWT (dans le navigateur) et lit l'email dedans |
| `src/router.js` | Les pages, et le blocage des pages privées sans token |
| `src/views/` | Les 5 pages |
| `src/components/` | Les morceaux réutilisables : fenêtres (modales), plaque, statut, illustration du scooter |
| `src/style.css` | Tout le design (couleurs en haut du fichier) |
| `src/config.js` | Le nom de l'application et la fréquence d'actualisation |

## À vérifier côté back

1. **Adresse de création d'un scooter.** Ton Swagger affichait `POST /api/auth/register/scooters`.
   Le front appelle `POST /api/scooters`. Corrige le `uriTemplate` de `ScooterResource` côté Symfony,
   ou change la constante `SCOOTER_CREATE_PATH` en haut de `src/api.js`.
2. **Routes récentes.** `GET /api/reservation-history` et `GET /api/my-reservations`
   doivent exister dans ton back (elles n'étaient pas dans le Swagger que tu m'as envoyé).
3. **Numéro du scooter.** Pour demander un scooter, la personne tape son numéro.
   Le propriétaire le voit sur la carte de son scooter (bouton « Numéro … » qui le copie).

## Remarques

- Le token JWT dure 1 heure. Après, le front te ramène à la connexion avec un message.
- Les dates sont envoyées sans fuseau horaire (`2026-11-12T09:00:00`) : le serveur les lit
  dans son propre fuseau. Garde le même fuseau pour le front et le back (Africa/Porto-Novo).
- « Mes réservations » se met à jour toute seule toutes les 15 secondes et prévient quand
  une demande passe de « En attente » à « Acceptée » ou « Refusée ».
