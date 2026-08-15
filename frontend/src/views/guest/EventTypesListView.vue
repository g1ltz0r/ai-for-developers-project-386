<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { useEventTypes } from '@/composables/useEventTypes'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import Skeleton from '@/components/ui/Skeleton.vue'
import Button from '@/components/ui/Button.vue'
import { Clock, ArrowRight } from 'lucide-vue-next'

const { data: eventTypes, isLoading, error } = useEventTypes()
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-2">
      <h1 class="text-3xl font-bold tracking-tight">Типы событий</h1>
      <p class="text-muted-foreground">Выберите тип события, чтобы забронировать встречу.</p>
    </div>

    <div v-if="isLoading" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <Skeleton v-for="i in 3" :key="i" class="h-40" />
    </div>

    <div v-else-if="error" class="rounded-md border border-destructive p-4 text-destructive">
      {{ error.message }}
    </div>

    <div v-else-if="!eventTypes?.length" class="text-center text-muted-foreground">
      Нет доступных типов событий
    </div>

    <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <Card v-for="eventType in eventTypes" :key="eventType.id">
        <CardHeader>
          <CardTitle>{{ eventType.title }}</CardTitle>
          <CardDescription v-if="eventType.description">
            {{ eventType.description }}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <Clock class="h-4 w-4" />
            <span>{{ eventType.duration_minutes }} минут</span>
          </div>
          <Button as="div" class="mt-4 w-full">
            <RouterLink
              :to="{ name: 'book', params: { id: eventType.id } }"
              class="flex w-full items-center justify-center gap-2 text-primary-foreground"
            >
              Забронировать
              <ArrowRight class="h-4 w-4" />
            </RouterLink>
          </Button>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
