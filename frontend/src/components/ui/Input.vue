<script setup lang="ts">
import { cn } from '@/lib/utils'

interface Props {
  modelValue?: string | number
}

defineProps<Props>()
const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

defineOptions({
  inheritAttrs: false,
})

function handleInput(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.type === 'number') {
    emit('update:modelValue', target.valueAsNumber)
  } else {
    emit('update:modelValue', target.value)
  }
}
</script>

<template>
  <input
    :value="modelValue"
    @input="handleInput"
    v-bind="$attrs"
    :class="
      cn(
        'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        $attrs.class as string,
      )
    "
  />
</template>
