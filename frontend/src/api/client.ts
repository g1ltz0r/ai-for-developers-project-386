import createClient from 'openapi-fetch'
import type { paths } from './openapi'

const baseUrl = import.meta.env.VITE_API_BASE_URL || '/api'

export const apiClient = createClient<paths>({
  baseUrl,
  headers: {
    'Content-Type': 'application/json',
  },
})

export function isApiError(error: unknown): error is { message: string } {
  return typeof error === 'object' && error !== null && 'message' in error && typeof (error as { message: string }).message === 'string'
}

export function getErrorMessage(error: unknown): string {
  if (isApiError(error)) {
    return error.message
  }
  return 'Произошла неизвестная ошибка'
}
