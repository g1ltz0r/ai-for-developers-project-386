import { useMutation } from '@tanstack/vue-query'
import { apiClient } from '@/api/client'
import type { components } from '@/api/openapi'
import { useUiStore } from '@/stores/ui'
import { useRouter } from 'vue-router'

type BookingCreate = components['schemas']['BookingCreate']
type BookingConfirmation = components['schemas']['BookingConfirmation']

export function useCreateBooking() {
  const uiStore = useUiStore()
  const router = useRouter()

  return useMutation({
    mutationFn: async ({ eventTypeId, body }: { eventTypeId: string; body: BookingCreate }) => {
      const { data, error } = await apiClient.POST('/event-types/{id}/book', {
        params: { path: { id: eventTypeId } },
        body,
      })
      if (error) throw error
      return data as BookingConfirmation
    },
    onSuccess: (data) => {
      uiStore.setBookingConfirmation(data)
      router.push({ name: 'booking-confirmation', params: { id: data.id } })
    },
  })
}
