<script setup lang="ts">
import { RouterLink } from 'vue-router'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import Button from '@/components/ui/Button.vue'
import { CalendarDays, List, ArrowRight } from 'lucide-vue-next'
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-2">
      <h1 class="text-3xl font-bold tracking-tight">Панель управления</h1>
      <p class="text-muted-foreground">Управление бронированиями и типами событий.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <CalendarDays class="h-5 w-5" />
            Бронирования
          </CardTitle>
          <CardDescription>Просмотр и отмена предстоящих встреч.</CardDescription>
        </CardHeader>
        <CardContent>
          <Button as="div" class="w-full">
            <RouterLink
              to="/admin/bookings"
              class="flex w-full items-center justify-center gap-2 text-primary-foreground"
            >
              Перейти
              <ArrowRight class="h-4 w-4" />
            </RouterLink>
          </Button>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <List class="h-5 w-5" />
            Типы событий
          </CardTitle>
          <CardDescription>Создание, редактирование и удаление типов событий.</CardDescription>
        </CardHeader>
        <CardContent>
          <Button as="div" class="w-full">
            <RouterLink
              to="/admin/event-types"
              class="flex w-full items-center justify-center gap-2 text-primary-foreground"
            >
              Перейти
              <ArrowRight class="h-4 w-4" />
            </RouterLink>
          </Button>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
