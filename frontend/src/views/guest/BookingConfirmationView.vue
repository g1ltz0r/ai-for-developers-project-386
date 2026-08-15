<script setup lang="ts">
import { useRoute, RouterLink } from 'vue-router'
import { useUiStore } from '@/stores/ui'
import BookingCard from '@/components/shared/BookingCard.vue'
import Button from '@/components/ui/Button.vue'
import { Calendar, Home } from 'lucide-vue-next'

useRoute()
const uiStore = useUiStore()

const confirmation = uiStore.bookingConfirmation
</script>

<template>
  <div class="mx-auto max-w-2xl space-y-6">
    <div class="space-y-2 text-center">
      <div class="flex justify-center">
        <div class="rounded-full bg-primary/10 p-4">
          <Calendar class="h-8 w-8 text-primary" />
        </div>
      </div>
      <h1 class="text-3xl font-bold tracking-tight">Бронирование подтверждено</h1>
      <p class="text-muted-foreground">
        Встреча успешно забронирована. Подробности ниже.
      </p>
    </div>

    <BookingCard v-if="confirmation" :booking="confirmation" :event-type-title="confirmation.event_type_title" />

    <div v-else class="rounded-md border border-destructive p-4 text-destructive">
      Информация о бронировании недоступна. Возможно, страница была открыта напрямую.
    </div>

    <div class="flex justify-center">
      <Button as="div">
        <RouterLink to="/" class="flex items-center gap-2 text-primary-foreground">
          <Home class="h-4 w-4" />
          На главную
        </RouterLink>
      </Button>
    </div>
  </div>
</template>
