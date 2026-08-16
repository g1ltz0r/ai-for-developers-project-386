import { defineConfig, devices } from '@playwright/test';
import { backendDir, frontendDir } from './lib/paths.js';
import { baseEnv, generateAppKey } from './lib/env.js';

export default defineConfig({
  testDir: './tests',
  fullyParallel: false,
  workers: 1,
  timeout: 60_000,
  expect: {
    timeout: 10_000,
  },
  reporter: [
    ['list'],
    ['html', { open: 'never' }],
  ],
  use: {
    baseURL: 'http://127.0.0.1:5273',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    timezoneId: 'Europe/Moscow',
    locale: 'ru-RU',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  globalSetup: './global-setup.ts',
  webServer: [
    {
      command: 'php artisan serve --host=127.0.0.1 --port=8010',
      cwd: backendDir,
      url: 'http://127.0.0.1:8010/api/event-types',
      timeout: 120_000,
      reuseExistingServer: false,
      env: {
        ...baseEnv,
        APP_KEY: generateAppKey(),
      },
    },
    {
      command: 'npm run dev -- --port 5273 --host 127.0.0.1',
      cwd: frontendDir,
      url: 'http://127.0.0.1:5273',
      timeout: 120_000,
      reuseExistingServer: false,
      env: {
        PROXY_TARGET: 'http://127.0.0.1:8010',
      },
    },
  ],
});
