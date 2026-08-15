import { ref } from 'vue'
import { defineStore } from 'pinia'
import type { components } from '@/api/openapi'

type BookingConfirmation = components['schemas']['BookingConfirmation']

export const useUiStore = defineStore('ui', () => {
  const timezone = ref(Intl.DateTimeFormat().resolvedOptions().timeZone)
  const bookingConfirmation = ref<BookingConfirmation | null>(null)

  function setTimezone(value: string) {
    timezone.value = value
  }

  function setBookingConfirmation(value: BookingConfirmation | null) {
    bookingConfirmation.value = value
  }

  return {
    timezone,
    bookingConfirmation,
    setTimezone,
    setBookingConfirmation,
  }
})
