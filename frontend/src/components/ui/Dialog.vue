<script setup lang="ts">
import { cn } from '@/lib/utils'
import { X } from 'lucide-vue-next'

interface Props {
  open: boolean
  title?: string
  description?: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
  'update:open': [value: boolean]
}>()

function close() {
  emit('update:open', false)
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="props.open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click="close"
    >
      <div
        :class="cn('relative w-full max-w-lg rounded-lg border bg-background p-6 shadow-lg', $attrs.class as string)"
        @click.stop
      >
        <button
          class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
          @click="close"
        >
          <X class="h-4 w-4" />
        </button>
        <div v-if="props.title || props.description" class="mb-4">
          <h3 v-if="props.title" class="text-lg font-semibold leading-none tracking-tight">
            {{ props.title }}
          </h3>
          <p v-if="props.description" class="mt-2 text-sm text-muted-foreground">
            {{ props.description }}
          </p>
        </div>
        <slot />
      </div>
    </div>
  </Teleport>
</template>
