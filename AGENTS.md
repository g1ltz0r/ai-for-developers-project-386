# AI agent instructions

## Project overview

This is a calendar booking project: Laravel 12 backend + Vue 3 frontend. The backend exposes a REST API (see `openapi.yaml`), the frontend is a Vite SPA that proxies `/api` to the backend.

Key directories:
- `backend/` — Laravel application (PHP 8.2, SQLite, Pest/PHPUnit).
- `frontend/` — Vue 3 + TypeScript + Pinia + TanStack Vue Query.
- `e2e/` — Playwright end-to-end tests covering the main booking flow.

## Commits

All commits, including agent commits, must follow [Conventional Commits](https://www.conventionalcommits.org/) and the present tense.

Allowed types:

- `feat:` — new feature or user-visible capability.
- `fix:` — bug fix.
- `test:` — tests, test infrastructure.
- `ci:` — CI/CD, GitHub Actions.
- `docs:` — documentation only.
- `chore:` — routine maintenance, dependency updates, tooling.
- `refactor:` — code change that neither fixes a bug nor adds a feature.
- `build:` — build system or external dependencies.
- `style:` — formatting, missing semicolons, etc.

Examples:

```text
feat: add release-please workflow
fix: correct timezone in e2e slot selection
test: cover booking cancellation in admin
ci: run e2e tests on pull requests and main
```

Do not use emojis in commit messages unless the user explicitly asked.

## Commands

```bash
# Backend
npm --prefix backend run dev          # php artisan serve + vite
composer --working-dir=backend test   # run Pest/PHPUnit

# Frontend
npm --prefix frontend run dev         # Vite dev server
npm --prefix frontend run test:unit   # Vitest
npm --prefix frontend run type-check  # vue-tsc
npm --prefix frontend run lint        # ESLint

# E2E
npm run test:e2e                      # Playwright tests (starts backend + frontend)
npm run test:e2e:ui                   # Playwright UI mode
```

## E2E testing notes

- `e2e/playwright.config.ts` starts both servers on isolated ports (`8010` and `5273`) and seeds a fresh SQLite database.
- The browser is forced to `Europe/Moscow` timezone and `ru-RU` locale.
- Do not edit `hexlet-check.yml`.
