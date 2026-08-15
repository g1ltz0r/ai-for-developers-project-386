<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { z } from 'zod'
import { useCreateEventType, useUpdateEventType } from '@/composables/useEventTypes'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Textarea from '@/components/ui/Textarea.vue'
import { Loader2 } from 'lucide-vue-next'
import type { components } from '@/api/openapi'

type EventType = components['schemas']['EventType']

interface Props {
  eventType?: EventType | null
  onSuccess?: () => void
}

const props = withDefaults(defineProps<Props>(), {
  eventType: null,
})

const emit = defineEmits<{
  success: []
}>()

const schema = toTypedSchema(
  z.object({
    title: z.string().min(1, 'Название обязательно'),
    description: z.string().optional(),
    duration_minutes: z.coerce.number().min(1, 'Длительность должна быть не менее 1 минуты'),
  }),
)

const { handleSubmit, errors, defineField, resetForm } = useForm({
  validationSchema: schema,
  initialValues: props.eventType
    ? {
        title: props.eventType.title,
        description: props.eventType.description || '',
        duration_minutes: props.eventType.duration_minutes,
      }
    : {
        title: '',
        description: '',
        duration_minutes: 30,
      },
})

const [title, titleAttrs] = defineField('title')
const [description, descriptionAttrs] = defineField('description')
const [durationMinutes, durationMinutesAttrs] = defineField('duration_minutes')

const { mutate: createEventType, isPending: isCreating, error: createError } = useCreateEventType()
const { mutate: updateEventType, isPending: isUpdating, error: updateError } = useUpdateEventType()

const onSubmit = handleSubmit((values) => {
  const body = {
    title: values.title,
    description: values.description || undefined,
    duration_minutes: values.duration_minutes,
  }

  if (props.eventType) {
    updateEventType(
      { id: props.eventType.id, body },
      {
        onSuccess: () => {
          emit('success')
        },
      },
    )
  } else {
    createEventType(body, {
      onSuccess: () => {
        resetForm()
        emit('success')
      },
    })
  }
})

const isPending = isCreating || isUpdating
const error = createError || updateError
</script>

<template>
  <form class="space-y-4" @submit="onSubmit">
    <div class="space-y-2">
      <Label for="title">Название</Label>
      <Input id="title" v-model="title" v-bind="titleAttrs" placeholder="Например, Консультация" />
      <p v-if="errors.title" class="text-sm text-destructive">{{ errors.title }}</p>
    </div>

    <div class="space-y-2">
      <Label for="description">Описание</Label>
      <Textarea
        id="description"
        v-model="description"
        v-bind="descriptionAttrs"
        placeholder="Описание типа события"
      />
    </div>

    <div class="space-y-2">
      <Label for="duration_minutes">Длительность (минуты)</Label>
      <Input
        id="duration_minutes"
        v-model="durationMinutes"
        v-bind="durationMinutesAttrs"
        type="number"
        min="1"
      />
      <p v-if="errors.duration_minutes" class="text-sm text-destructive">{{ errors.duration_minutes }}</p>
    </div>

    <p v-if="error" class="text-sm text-destructive">{{ error.message }}</p>

    <Button type="submit" :disabled="isPending" class="w-full">
      <Loader2 v-if="isPending" class="mr-2 h-4 w-4 animate-spin" />
      {{ eventType ? 'Сохранить' : 'Создать' }}
    </Button>
  </form>
</template>
