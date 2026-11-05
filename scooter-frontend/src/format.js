// Fonctions d'affichage des dates (en français, heure locale du navigateur)

const dayFmt = new Intl.DateTimeFormat('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' })
const longDayFmt = new Intl.DateTimeFormat('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' })
const timeFmt = new Intl.DateTimeFormat('fr-FR', { hour: '2-digit', minute: '2-digit' })
const monthFmt = new Intl.DateTimeFormat('fr-FR', { month: 'short' })

export function toDate(value) {
  return value instanceof Date ? value : new Date(value)
}

export function sameDay(a, b) {
  return a.toDateString() === b.toDateString()
}

export function dayLabel(value) {
  return dayFmt.format(toDate(value))
}

export function longDayLabel(value) {
  return longDayFmt.format(toDate(value))
}

export function dayNumber(value) {
  return toDate(value).getDate()
}

export function monthLabel(value) {
  return monthFmt.format(toDate(value)).replace('.', '')
}

export function timeLabel(value) {
  return timeFmt.format(toDate(value)).replace(':', 'h')
}

// "mer. 12 nov., de 09h00 à 11h00"  ou  "mer. 12 nov. 22h00 à jeu. 13 nov. 06h00"
export function rangeLabel(start, end) {
  const a = toDate(start)
  const b = toDate(end)
  if (sameDay(a, b)) return `${dayLabel(a)}, de ${timeLabel(a)} à ${timeLabel(b)}`
  return `${dayLabel(a)} ${timeLabel(a)} à ${dayLabel(b)} ${timeLabel(b)}`
}

// Le champ <input type="datetime-local"> donne "2026-11-12T09:00".
// Le back attend une date complète : on ajoute les secondes.
export function toApiDate(value) {
  return value && value.length === 16 ? `${value}:00` : value
}

// Valeur minimale pour datetime-local : maintenant, à la minute près.
export function nowForInput() {
  const d = new Date()
  d.setMinutes(d.getMinutes() - d.getTimezoneOffset())
  return d.toISOString().slice(0, 16)
}

export const STATUS = {
  PENDING: { label: 'En attente', tone: 'wait' },
  ACCEPTED: { label: 'Acceptée', tone: 'ok' },
  REFUSED: { label: 'Refusée', tone: 'bad' },
}
