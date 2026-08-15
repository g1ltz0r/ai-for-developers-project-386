import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { VueQueryPlugin, QueryClient } from '@tanstack/vue-query'

import App from './App.vue'
import router from './router'
import { addToast } from './composables/useToast'
import { getErrorMessage } from './api/client'

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 5,
      retry: 1,
    },
  },
})

queryClient.getQueryCache().subscribe((event) => {
  if (event?.query) {
    const query = event.query
    const state = query.state
    if (state.error && state.fetchStatus === 'idle') {
      addToast({
        title: 'Ошибка загрузки данных',
        description: getErrorMessage(state.error),
        variant: 'destructive',
      })
    }
  }
})

const app = createApp(App)

app.use(createPinia())
app.use(VueQueryPlugin, { queryClient })
app.use(router)

app.mount('#app')
