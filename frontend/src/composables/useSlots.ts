import { useQuery } from '@tanstack/vue-query'
import { apiClient } from '@/api/client'
import type { components } from '@/api/openapi'
import { computed, type Ref } from 'vue'

type SlotsResponse = components['schemas']['SlotsResponse']

export function useSlots(eventTypeId: Ref<string | undefined>, timezone: Ref<string>) {
  const enabled = computed(() => Boolean(eventTypeId.value && timezone.value))

  return useQuery({
    queryKey: ['slots', eventTypeId, timezone],
    queryFn: async () => {
      if (!eventTypeId.value || !timezone.value) {
        return null
      }
      const { data, error } = await apiClient.GET('/event-types/{id}/slots', {
        params: {
          path: { id: eventTypeId.value },
          query: { tz: timezone.value },
        },
      })
      if (error) throw error
      return data as SlotsResponse
    },
    enabled,
  })
}
