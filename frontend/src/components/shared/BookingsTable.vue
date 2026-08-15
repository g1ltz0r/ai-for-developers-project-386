<script setup lang="ts">
import { format, parseISO } from 'date-fns'
import { ru } from 'date-fns/locale'
import type { components } from '@/api/openapi'
import Button from '@/components/ui/Button.vue'
import { Trash2 } from 'lucide-vue-next'

type AdminBooking = components['schemas']['AdminBooking']

interface Props {
  bookings: AdminBooking[]
  isDeleting?: string | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  cancel: [id: string]
}>()

function formatDateTime(iso: string) {
  return format(parseISO(iso), 'd MMMM yyyy HH:mm', { locale: ru })
}

function formatDuration(minutes: number) {
  return `${minutes} мин`
}
</script>

<template>
  <div class="rounded-md border">
    <table class="w-full text-sm">
      <thead class="border-b bg-muted/50">
        <tr>
          <th class="px-4 py-3 text-left font-medium">Тип события</th>
          <th class="px-4 py-3 text-left font-medium">Гость</th>
          <th class="px-4 py-3 text-left font-medium">Начало</th>
          <th class="px-4 py-3 text-left font-medium">Длительность</th>
          <th class="px-4 py-3 text-right font-medium">Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="booking in props.bookings"
          :key="booking.id"
          class="border-b last:border-0"
        >
          <td class="px-4 py-3 font-medium">{{ booking.event_type_title }}</td>
          <td class="px-4 py-3">
            <div>{{ booking.guest_name }}</div>
            <div class="text-xs text-muted-foreground">{{ booking.guest_email }}</div>
          </td>
          <td class="px-4 py-3">{{ formatDateTime(booking.start_time) }}</td>
          <td class="px-4 py-3">{{ formatDuration(booking.duration_minutes) }}</td>
          <td class="px-4 py-3 text-right">
            <Button
              variant="destructive"
              size="sm"
              :disabled="isDeleting === booking.id"
              @click="emit('cancel', booking.id)"
            >
              <Trash2 class="mr-1 h-4 w-4" />
              Отменить
            </Button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
