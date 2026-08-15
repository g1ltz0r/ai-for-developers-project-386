import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/guest/EventTypesListView.vue'),
    },
    {
      path: '/event-types/:id/book',
      name: 'book',
      component: () => import('../views/guest/BookingView.vue'),
    },
    {
      path: '/bookings/:id/confirmation',
      name: 'booking-confirmation',
      component: () => import('../views/guest/BookingConfirmationView.vue'),
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/admin/AdminDashboardView.vue'),
    },
    {
      path: '/admin/bookings',
      name: 'admin-bookings',
      component: () => import('../views/admin/BookingsListView.vue'),
    },
    {
      path: '/admin/event-types',
      name: 'admin-event-types',
      component: () => import('../views/admin/EventTypesManageView.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFoundView.vue'),
    },
  ],
})

export default router
