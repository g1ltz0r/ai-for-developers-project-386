<script setup lang="ts">
import { RouterView, RouterLink, useRoute } from 'vue-router'
import { Calendar, Menu, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'

const route = useRoute()
const mobileMenuOpen = ref(false)

watch(
  () => route.path,
  () => {
    mobileMenuOpen.value = false
  },
)

const navItems = [
  { name: 'Главная', path: '/' },
  { name: 'Админка', path: '/admin' },
]
</script>

<template>
  <div class="min-h-screen bg-background">
    <header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div class="container mx-auto flex h-14 items-center px-4">
        <RouterLink to="/" class="mr-6 flex items-center gap-2 text-lg font-bold">
          <Calendar class="h-6 w-6 text-primary" />
          <span>Booking</span>
        </RouterLink>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
          <RouterLink
            v-for="item in navItems"
            :key="item.path"
            :to="item.path"
            :class="[
              'transition-colors hover:text-primary',
              route.path === item.path ? 'text-primary' : 'text-muted-foreground',
            ]"
          >
            {{ item.name }}
          </RouterLink>
        </nav>

        <div class="flex flex-1 items-center justify-end gap-2">
          <button
            class="inline-flex items-center justify-center rounded-md p-2 text-sm font-medium md:hidden"
            @click="mobileMenuOpen = !mobileMenuOpen"
          >
            <Menu v-if="!mobileMenuOpen" class="h-5 w-5" />
            <X v-else class="h-5 w-5" />
          </button>
        </div>
      </div>
    </header>

    <div
      v-if="mobileMenuOpen"
      class="container mx-auto border-b px-4 py-4 md:hidden"
    >
      <nav class="flex flex-col gap-4 text-sm font-medium">
        <RouterLink
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          :class="[
            'transition-colors hover:text-primary',
            route.path === item.path ? 'text-primary' : 'text-muted-foreground',
          ]"
          @click="mobileMenuOpen = false"
        >
          {{ item.name }}
        </RouterLink>
      </nav>
    </div>

    <main class="container mx-auto px-4 py-6">
      <RouterView />
    </main>

    <ToastContainer />
  </div>
</template>
