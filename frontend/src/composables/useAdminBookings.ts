import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { apiClient } from '@/api/client'
import type { components } from '@/api/openapi'
import type { Ref } from 'vue'

type BookingPage = components['schemas']['BookingPage']

export function useAdminBookings(cursor: Ref<string | undefined>, limit: number = 20) {
  return useQuery({
    queryKey: ['admin-bookings', cursor, limit],
    queryFn: async () => {
      const params: { query: { limit: number; cursor?: string } } = {
        query: { limit },
      }
      if (cursor.value) {
        params.query.cursor = cursor.value
      }
      const { data, error } = await apiClient.GET('/admin/bookings', { params })
      if (error) throw error
      return data as BookingPage
    },
  })
}

export function useCancelBooking() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (id: string) => {
      const { error } = await apiClient.DELETE('/admin/bookings/{id}', {
        params: { path: { id } },
      })
      if (error) throw error
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['admin-bookings'] })
    },
  })
}
