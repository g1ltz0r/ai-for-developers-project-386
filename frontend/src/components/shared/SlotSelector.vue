<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { format, parseISO, isSameDay, addDays, startOfDay } from 'date-fns'
import { ru } from 'date-fns/locale'
import type { components } from '@/api/openapi'
import Button from '@/components/ui/Button.vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

type Slot = components['schemas']['Slot']
type SlotsResponse = components['schemas']['SlotsResponse']

interface Props {
  slotsResponse: SlotsResponse | null | undefined
  modelValue: string | null
  loading?: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  'update:modelValue': [value: string | null]
}>()

const selectedDate = ref<Date | null>(null)

const allDates = computed(() => {
  const dates: Date[] = []
  if (!props.slotsResponse?.slots.length) return dates

  const start = props.slotsResponse.window.start
    ? parseISO(props.slotsResponse.window.start)
    : startOfDay(new Date())
  const end = props.slotsResponse.window.end
    ? parseISO(props.slotsResponse.window.end)
    : addDays(start, 14)

  let current = startOfDay(start)
  const endDay = startOfDay(end)

  while (current <= endDay) {
    dates.push(new Date(current))
    current = addDays(current, 1)
  }

  return dates
})

const slotsByDate = computed(() => {
  const map = new Map<string, Slot[]>()
  if (!props.slotsResponse?.slots) return map

  for (const slot of props.slotsResponse.slots) {
    const date = format(parseISO(slot.start), 'yyyy-MM-dd')
    if (!map.has(date)) {
      map.set(date, [])
    }
    map.get(date)!.push(slot)
  }

  for (const [, slots] of map) {
    slots.sort((a, b) => a.start.localeCompare(b.start))
  }

  return map
})

const selectedDateSlots = computed(() => {
  if (!selectedDate.value) return []
  const dateKey = format(selectedDate.value, 'yyyy-MM-dd')
  return slotsByDate.value.get(dateKey) || []
})

watch(
  () => props.slotsResponse,
  () => {
    if (allDates.value.length && !selectedDate.value) {
      const first = allDates.value[0]
      if (first) {
        selectedDate.value = first
      }
    }
  },
  { immediate: true },
)

function selectDate(date: Date) {
  selectedDate.value = date
  emit('update:modelValue', null)
}

function selectSlot(slot: Slot) {
  emit('update:modelValue', slot.start)
}

function previousDate() {
  if (selectedDate.value) {
    selectedDate.value = addDays(selectedDate.value, -1)
  }
}

function nextDate() {
  if (selectedDate.value) {
    selectedDate.value = addDays(selectedDate.value, 1)
  }
}

function formatDate(date: Date) {
  return format(date, 'd MMMM', { locale: ru })
}

function formatWeekday(date: Date) {
  return format(date, 'EEE', { locale: ru })
}

function formatTime(slot: Slot) {
  return format(parseISO(slot.start), 'HH:mm')
}

function isSelected(slot: Slot) {
  return props.modelValue === slot.start
}
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center gap-2">
      <button
        class="rounded-md p-1 hover:bg-accent"
        :disabled="!selectedDate"
        aria-label="Предыдущий день"
        @click="previousDate"
      >
        <ChevronLeft class="h-4 w-4" />
      </button>
      <div class="flex flex-1 gap-2 overflow-x-auto pb-2">
        <button
          v-for="date in allDates"
          :key="date.toISOString()"
          :class="[
            'flex min-w-[80px] flex-col items-center rounded-md border p-2 text-sm transition-colors',
            selectedDate && isSameDay(date, selectedDate)
              ? 'border-primary bg-primary text-primary-foreground'
              : 'border-input bg-background hover:bg-accent',
          ]"
          @click="selectDate(date)"
        >
          <span class="text-xs uppercase">{{ formatWeekday(date) }}</span>
          <span class="font-semibold">{{ formatDate(date) }}</span>
        </button>
      </div>
      <button
        class="rounded-md p-1 hover:bg-accent"
        :disabled="!selectedDate"
        aria-label="Следующий день"
        @click="nextDate"
      >
        <ChevronRight class="h-4 w-4" />
      </button>
    </div>

    <div>
      <p v-if="loading" class="text-sm text-muted-foreground">Загрузка слотов...</p>
      <p v-else-if="!selectedDateSlots.length" class="text-sm text-muted-foreground">
        Нет доступных слотов на выбранную дату
      </p>
      <div v-else class="flex flex-wrap gap-2">
        <Button
          v-for="slot in selectedDateSlots"
          :key="slot.start"
          :variant="isSelected(slot) ? 'default' : 'outline'"
          size="sm"
          @click="selectSlot(slot)"
        >
          {{ formatTime(slot) }}
        </Button>
      </div>
    </div>
  </div>
</template>
