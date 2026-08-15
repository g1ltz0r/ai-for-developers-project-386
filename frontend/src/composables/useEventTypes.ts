import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { apiClient } from '@/api/client'
import type { components } from '@/api/openapi'

type EventType = components['schemas']['EventType']
type EventTypeCreate = components['schemas']['EventTypeCreate']
type EventTypeUpdate = components['schemas']['EventTypeUpdate']

export function useEventTypes() {
  return useQuery({
    queryKey: ['event-types'],
    queryFn: async () => {
      const { data, error } = await apiClient.GET('/event-types')
      if (error) throw error
      return data as EventType[]
    },
  })
}

export function useCreateEventType() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (body: EventTypeCreate) => {
      const { data, error } = await apiClient.POST('/event-types', { body })
      if (error) throw error
      return data as EventType
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['event-types'] })
    },
  })
}

export function useUpdateEventType() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, body }: { id: string; body: EventTypeUpdate }) => {
      const { data, error } = await apiClient.PATCH('/event-types/{id}', {
        params: { path: { id } },
        body,
      })
      if (error) throw error
      return data as EventType
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['event-types'] })
    },
  })
}

export function useDeleteEventType() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (id: string) => {
      const { error } = await apiClient.DELETE('/event-types/{id}', {
        params: { path: { id } },
      })
      if (error) throw error
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['event-types'] })
    },
  })
}
