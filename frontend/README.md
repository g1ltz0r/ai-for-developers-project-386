# Calendar Booking Frontend

Vue 3 + TypeScript + Vite frontend для Calendar Booking API.

## Запуск

### 1. Установка зависимостей

```bash
npm install
```

### 2. Мок-сервер Prism

В отдельном терминале:

```bash
npx prism mock ../openapi.yaml -p 4010
```

### 3. Dev-сервер

```bash
npm run dev
```

Приложение доступно по адресу: http://localhost:5173

API проксируется через `/api` на http://localhost:4010.

## Скрипты

- `npm run dev` — запуск dev-сервера
- `npm run build` — production-сборка
- `npm run type-check` — проверка TypeScript
- `npm run lint` — проверка ESLint
- `npm run test:unit` — unit-тесты

## Маршруты

- `/` — список типов событий
- `/event-types/:id/book` — бронирование
- `/bookings/:id/confirmation` — подтверждение брони
- `/admin` — панель управления
- `/admin/bookings` — предстоящие бронирования
- `/admin/event-types` — управление типами событий
