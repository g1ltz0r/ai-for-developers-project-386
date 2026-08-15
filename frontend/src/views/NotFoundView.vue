<script setup lang="ts">
import { RouterLink } from 'vue-router'
import Button from '@/components/ui/Button.vue'
import { Home } from 'lucide-vue-next'
</script>

<template>
  <div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
    <h1 class="text-6xl font-bold text-primary">404</h1>
    <p class="mt-4 text-2xl font-semibold">Страница не найдена</p>
    <p class="mt-2 text-muted-foreground">
      Запрашиваемая страница не существует.
    </p>
    <Button as="div" class="mt-6">
      <RouterLink to="/" class="flex items-center gap-2 text-primary-foreground">
        <Home class="h-4 w-4" />
        На главную
      </RouterLink>
    </Button>
  </div>
</template>
