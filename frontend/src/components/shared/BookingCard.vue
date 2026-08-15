<script setup lang="ts">
import { format, parseISO } from 'date-fns'
import { ru } from 'date-fns/locale'
import type { components } from '@/api/openapi'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Badge from '@/components/ui/Badge.vue'

type Booking = components['schemas']['Booking'] | components['schemas']['BookingConfirmation']

interface Props {
  booking: Booking
  eventTypeTitle?: string
}

const props = defineProps<Props>()

function formatDateTime(iso: string) {
  return format(parseISO(iso), 'd MMMM yyyy HH:mm', { locale: ru })
}

const title = props.eventTypeTitle || ('event_type_title' in props.booking ? props.booking.event_type_title : '')
</script>

<template>
  <Card>
    <CardContent class="pt-6">
      <div class="flex items-start justify-between">
        <div>
          <h3 class="font-semibold">{{ title || 'Бронирование' }}</h3>
          <p class="text-sm text-muted-foreground">{{ booking.guest_name }}</p>
          <p class="text-sm text-muted-foreground">{{ booking.guest_email }}</p>
        </div>
        <Badge variant="outline">{{ booking.duration_minutes }} мин</Badge>
      </div>
      <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
        <div>
          <p class="text-muted-foreground">Начало</p>
          <p class="font-medium">{{ formatDateTime(booking.start_time) }}</p>
        </div>
        <div>
          <p class="text-muted-foreground">Окончание</p>
          <p class="font-medium">{{ formatDateTime(booking.end_time) }}</p>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
