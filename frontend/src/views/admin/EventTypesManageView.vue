<script setup lang="ts">
import { ref } from 'vue'
import { useEventTypes, useDeleteEventType } from '@/composables/useEventTypes'
import EventTypeForm from '@/components/shared/EventTypeForm.vue'
import Dialog from '@/components/ui/Dialog.vue'
import Button from '@/components/ui/Button.vue'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import Skeleton from '@/components/ui/Skeleton.vue'
import { useToast } from '@/composables/useToast'
import { Plus, Pencil, Trash2 } from 'lucide-vue-next'
import type { components } from '@/api/openapi'

type EventType = components['schemas']['EventType']

const { data: eventTypes, isLoading, error } = useEventTypes()
const { mutate: deleteEventType } = useDeleteEventType()
const { add: toast } = useToast()

const dialogOpen = ref(false)
const editingEventType = ref<EventType | null>(null)
const isDeletingId = ref<string | null>(null)

function openCreateDialog() {
  editingEventType.value = null
  dialogOpen.value = true
}

function openEditDialog(eventType: EventType) {
  editingEventType.value = eventType
  dialogOpen.value = true
}

function closeDialog() {
  dialogOpen.value = false
  editingEventType.value = null
}

function handleDelete(eventType: EventType) {
  if (!confirm(`Удалить тип события "${eventType.title}"?`)) return
  isDeletingId.value = eventType.id
  deleteEventType(eventType.id, {
    onSuccess: () => {
      toast({ title: 'Тип события удалён' })
      isDeletingId.value = null
    },
    onError: (err) => {
      toast({ title: 'Ошибка', description: err.message, variant: 'destructive' })
      isDeletingId.value = null
    },
  })
}

function handleSuccess() {
  closeDialog()
  toast({ title: 'Тип события сохранён' })
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div class="space-y-2">
        <h1 class="text-3xl font-bold tracking-tight">Типы событий</h1>
        <p class="text-muted-foreground">Управление типами событий.</p>
      </div>
      <Button @click="openCreateDialog">
        <Plus class="mr-2 h-4 w-4" />
        Создать
      </Button>
    </div>

    <div v-if="isLoading" class="grid gap-4 md:grid-cols-2">
      <Skeleton v-for="i in 3" :key="i" class="h-32" />
    </div>

    <div v-else-if="error" class="rounded-md border border-destructive p-4 text-destructive">
      {{ error.message }}
    </div>

    <div v-else-if="!eventTypes?.length" class="text-center text-muted-foreground">
      Нет типов событий
    </div>

    <div v-else class="grid gap-4 md:grid-cols-2">
      <Card v-for="eventType in eventTypes" :key="eventType.id">
        <CardHeader>
          <CardTitle>{{ eventType.title }}</CardTitle>
          <CardDescription v-if="eventType.description">
            {{ eventType.description }}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <p class="text-sm text-muted-foreground">{{ eventType.duration_minutes }} минут</p>
          <div class="mt-4 flex gap-2">
            <Button variant="outline" size="sm" @click="openEditDialog(eventType)">
              <Pencil class="mr-1 h-4 w-4" />
              Изменить
            </Button>
            <Button
              variant="destructive"
              size="sm"
              :disabled="isDeletingId === eventType.id"
              @click="handleDelete(eventType)"
            >
              <Trash2 class="mr-1 h-4 w-4" />
              Удалить
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <Dialog
      v-model:open="dialogOpen"
      :title="editingEventType ? 'Изменить тип события' : 'Создать тип события'"
    >
      <EventTypeForm
        :event-type="editingEventType"
        @success="handleSuccess"
      />
    </Dialog>
  </div>
</template>
