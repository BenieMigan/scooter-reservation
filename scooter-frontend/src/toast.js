import { reactive } from 'vue'

// Petits messages qui apparaissent en bas de l'écran (succès, erreur, info).
export const toasts = reactive([])
let counter = 0

export function toast(message, type = 'info', duration = 4500) {
  const id = ++counter
  toasts.push({ id, message, type })
  setTimeout(() => dismiss(id), duration)
}

export function dismiss(id) {
  const index = toasts.findIndex((t) => t.id === id)
  if (index > -1) toasts.splice(index, 1)
}
