// Couleurs des scooters. Un scooter garde toujours la même couleur,
// car elle est choisie à partir de son numéro (id).
const PALETTE = [
  { name: 'turquoise', body: '#14a3a0', dark: '#0b6764' },
  { name: 'orange', body: '#f0702a', dark: '#a8420f' },
  { name: 'rouge', body: '#d23b36', dark: '#8d2320' },
  { name: 'jaune', body: '#f6c90e', dark: '#a98604' },
  { name: 'bleu', body: '#2f6fd0', dark: '#1b4390' },
  { name: 'vert', body: '#3a9d5d', dark: '#22693c' },
]

export function paletteFor(id) {
  const n = Number(id) || 0
  return PALETTE[Math.abs(n) % PALETTE.length]
}

export const MUTED_PALETTE = { name: 'gris', body: '#c9d4da', dark: '#9fb0ba' }
