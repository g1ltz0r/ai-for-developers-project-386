import { randomBytes } from 'node:crypto';
import { dbPath } from './paths.js';

export function generateAppKey(): string {
  return 'base64:' + randomBytes(32).toString('base64');
}

export const baseEnv = {
  APP_ENV: 'local',
  APP_DEBUG: 'true',
  APP_URL: 'http://127.0.0.1:8010',
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: dbPath,
  CACHE_STORE: 'database',
  SESSION_DRIVER: 'database',
  QUEUE_CONNECTION: 'database',
  LOG_LEVEL: 'debug',
} as const;

export function getBackendEnv(): NodeJS.ProcessEnv {
  return {
    ...process.env,
    ...baseEnv,
    APP_KEY: generateAppKey(),
  };
}
