<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAdminBookings, useCancelBooking } from '@/composables/useAdminBookings'
import BookingsTable from '@/components/shared/BookingsTable.vue'
import Button from '@/components/ui/Button.vue'
import Skeleton from '@/components/ui/Skeleton.vue'
import { useToast } from '@/composables/useToast'
import { Loader2, ChevronLeft } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const { add: toast } = useToast()

const cursor = ref<string | undefined>(
  route.query.cursor ? String(route.query.cursor) : undefined,
)

const { data: bookingPage, isLoading, error } = useAdminBookings(cursor)
const { mutate: cancelBooking, isPending: isDeleting, error: cancelError } = useCancelBooking()

const isDeletingId = ref<string | null>(null)

function handleCancel(id: string) {
  isDeletingId.value = id
  cancelBooking(id, {
    onSuccess: () => {
      toast({ title: 'Бронирование отменено' })
      isDeletingId.value = null
    },
    onError: (err) => {
      toast({ title: 'Ошибка', description: err.message, variant: 'destructive' })
      isDeletingId.value = null
    },
  })
}

function loadNext() {
  if (bookingPage.value?.next_cursor) {
    cursor.value = bookingPage.value.next_cursor
    router.push({ query: { cursor: cursor.value } })
  }
}

function loadPrevious() {
  cursor.value = undefined
  router.push({ query: {} })
}

const hasNext = computed(() => Boolean(bookingPage.value?.next_cursor))
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-2">
      <h1 class="text-3xl font-bold tracking-tight">Предстоящие бронирования</h1>
      <p class="text-muted-foreground">Список всех предстоящих встреч.</p>
    </div>

    <div v-if="isLoading" class="space-y-2">
      <Skeleton class="h-12" />
      <Skeleton class="h-12" />
      <Skeleton class="h-12" />
    </div>

    <div v-else-if="error" class="rounded-md border border-destructive p-4 text-destructive">
      {{ error.message }}
    </div>

    <div v-else-if="!bookingPage?.items.length" class="text-center text-muted-foreground">
      Нет предстоящих бронирований
    </div>

    <template v-else>
      <BookingsTable
        :bookings="bookingPage.items"
        :is-deleting="isDeletingId"
        @cancel="handleCancel"
      />

      <div v-if="cancelError" class="text-sm text-destructive">
        {{ cancelError.message }}
      </div>

      <div class="flex items-center justify-between">
        <Button
          variant="outline"
          :disabled="!cursor"
          @click="loadPrevious"
        >
          <ChevronLeft class="mr-2 h-4 w-4" />
          Назад
        </Button>
        <Button
          :disabled="!hasNext || isDeleting"
          @click="loadNext"
        >
          <Loader2 v-if="isDeleting" class="mr-2 h-4 w-4 animate-spin" />
          Загрузить ещё
        </Button>
      </div>
    </template>
  </div>
</template>
