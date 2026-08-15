import { reactive } from 'vue'

export interface Toast {
  id: string
  title?: string
  description?: string
  variant?: 'default' | 'destructive'
  duration?: number
}

const toasts = reactive<Toast[]>([])

function remove(id: string) {
  const index = toasts.findIndex((t) => t.id === id)
  if (index !== -1) {
    toasts.splice(index, 1)
  }
}

export function addToast(toast: Omit<Toast, 'id'>) {
  const id = Math.random().toString(36).substring(2, 9)
  const item: Toast = { id, duration: 5000, ...toast }
  toasts.push(item)
  setTimeout(() => {
    remove(id)
  }, item.duration)
}

export function useToast() {
  return {
    toasts,
    add: addToast,
    remove,
  }
}
