<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { z } from 'zod'
import { useCreateBooking } from '@/composables/useBooking'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import { Loader2 } from 'lucide-vue-next'

const bookingSchema = toTypedSchema(
  z.object({
    guest_name: z.string().min(1, 'Имя обязательно'),
    guest_email: z.string().email('Некорректный email'),
  }),
)

interface Props {
  eventTypeId: string
  selectedSlot: string | null
}

const props = defineProps<Props>()

const { handleSubmit, errors, defineField } = useForm({
  validationSchema: bookingSchema,
})

const [guestName, guestNameAttrs] = defineField('guest_name')
const [guestEmail, guestEmailAttrs] = defineField('guest_email')

const { mutate: createBooking, isPending, error } = useCreateBooking()

const onSubmit = handleSubmit((values) => {
  if (!props.selectedSlot) return
  createBooking({
    eventTypeId: props.eventTypeId,
    body: {
      ...values,
      start_time: props.selectedSlot,
    },
  })
})
</script>

<template>
  <form class="space-y-4" @submit="onSubmit">
    <div class="space-y-2">
      <Label for="guest_name">Имя</Label>
      <Input
        id="guest_name"
        v-model="guestName"
        v-bind="guestNameAttrs"
        placeholder="Введите ваше имя"
      />
      <p v-if="errors.guest_name" class="text-sm text-destructive">{{ errors.guest_name }}</p>
    </div>

    <div class="space-y-2">
      <Label for="guest_email">Email</Label>
      <Input
        id="guest_email"
        v-model="guestEmail"
        v-bind="guestEmailAttrs"
        type="email"
        placeholder="email@example.com"
      />
      <p v-if="errors.guest_email" class="text-sm text-destructive">{{ errors.guest_email }}</p>
    </div>

    <p v-if="error" class="text-sm text-destructive">
      {{ error.message }}
    </p>

    <Button type="submit" :disabled="!selectedSlot || isPending" class="w-full">
      <Loader2 v-if="isPending" class="mr-2 h-4 w-4 animate-spin" />
      Забронировать
    </Button>
  </form>
</template>
