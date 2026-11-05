import { paletteFor } from './scooterPalette'

// Fond teinté derrière l'illustration d'un scooter : même couleur que le scooter.
export function stageStyle(id) {
  return { '--body': paletteFor(id).body }
}
