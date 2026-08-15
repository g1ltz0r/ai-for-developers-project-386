<script setup lang="ts">
import { useToast } from '@/composables/useToast'
import { X } from 'lucide-vue-next'

const { toasts, remove } = useToast()
</script>

<template>
  <Teleport to="body">
    <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'relative rounded-lg border p-4 shadow-lg transition-all duration-300 min-w-[300px] max-w-md',
          toast.variant === 'destructive'
            ? 'border-destructive bg-destructive text-destructive-foreground'
            : 'border-border bg-background text-foreground',
        ]"
      >
        <button
          class="absolute right-2 top-2 rounded-sm opacity-70 transition-opacity hover:opacity-100"
          @click="remove(toast.id)"
        >
          <X class="h-4 w-4" />
        </button>
        <div v-if="toast.title" class="pr-6 text-sm font-semibold">
          {{ toast.title }}
        </div>
        <div v-if="toast.description" class="pr-6 text-sm opacity-90">
          {{ toast.description }}
        </div>
      </div>
    </div>
  </Teleport>
</template>
