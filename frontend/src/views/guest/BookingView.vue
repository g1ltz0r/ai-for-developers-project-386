<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useSlots } from '@/composables/useSlots'
import { useEventTypes } from '@/composables/useEventTypes'
import { useUiStore } from '@/stores/ui'
import SlotSelector from '@/components/shared/SlotSelector.vue'
import BookingForm from '@/components/shared/BookingForm.vue'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import Skeleton from '@/components/ui/Skeleton.vue'
import Button from '@/components/ui/Button.vue'
import { ArrowLeft, Globe } from 'lucide-vue-next'

const route = useRoute()
const uiStore = useUiStore()

const eventTypeId = computed(() => route.params.id as string)
const timezone = computed(() => uiStore.timezone)

const { data: eventTypes, isLoading: isLoadingEventTypes } = useEventTypes()
const { data: slotsResponse, isLoading: isLoadingSlots, error: slotsError } = useSlots(
  computed(() => eventTypeId.value),
  timezone,
)

const selectedSlot = ref<string | null>(null)

const eventType = computed(() => {
  return eventTypes.value?.find((et) => et.id === eventTypeId.value)
})

const timezones = (Intl as unknown as { supportedValuesOf: (name: string) => string[] }).supportedValuesOf('timeZone')
</script>

<template>
  <div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-2">
      <Button variant="ghost" size="sm" as="div">
        <RouterLink to="/" class="flex items-center gap-1 text-foreground">
          <ArrowLeft class="h-4 w-4" />
          Назад
        </RouterLink>
      </Button>
    </div>

    <div class="space-y-2">
      <h1 class="text-3xl font-bold tracking-tight">Бронирование</h1>
      <p v-if="isLoadingEventTypes" class="text-muted-foreground">Загрузка...</p>
      <p v-else-if="eventType" class="text-muted-foreground">
        {{ eventType.title }} · {{ eventType.duration_minutes }} минут
      </p>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>Выберите дату и время</CardTitle>
        <CardDescription>
          Доступные слоты на ближайшие 14 дней в вашем часовом поясе
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex items-center gap-2">
          <Globe class="h-4 w-4 text-muted-foreground" />
          <select
            v-model="uiStore.timezone"
            class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
          >
            <option v-for="tz in timezones" :key="tz" :value="tz">
              {{ tz }}
            </option>
          </select>
        </div>

        <div v-if="isLoadingSlots" class="space-y-2">
          <Skeleton class="h-20" />
          <Skeleton class="h-10" />
        </div>

        <div v-else-if="slotsError" class="text-sm text-destructive">
          {{ slotsError.message }}
        </div>

        <SlotSelector
          v-else
          v-model="selectedSlot"
          :slots-response="slotsResponse"
          :loading="isLoadingSlots"
        />
      </CardContent>
    </Card>

    <Card v-if="selectedSlot">
      <CardHeader>
        <CardTitle>Ваши данные</CardTitle>
      </CardHeader>
      <CardContent>
        <BookingForm :event-type-id="eventTypeId" :selected-slot="selectedSlot" />
      </CardContent>
    </Card>
  </div>
</template>
