import { execSync, spawn } from 'node:child_process';
import { mkdirSync, rmSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { randomBytes } from 'node:crypto';

const __dirname = dirname(fileURLToPath(import.meta.url));
const backendDir = resolve(__dirname, '..', '..', 'backend');
const dbPath = resolve(backendDir, 'database', 'e2e.sqlite');

const baseEnv = {
  APP_ENV: 'local',
  APP_DEBUG: 'true',
  APP_URL: 'http://127.0.0.1:8010',
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: dbPath,
  CACHE_STORE: 'database',
  SESSION_DRIVER: 'database',
  QUEUE_CONNECTION: 'database',
  LOG_LEVEL: 'debug',
};

const env = {
  ...process.env,
  ...baseEnv,
  APP_KEY: 'base64:' + randomBytes(32).toString('base64'),
};

rmSync(dbPath, { force: true });
rmSync(dbPath + '-journal', { force: true });
mkdirSync(resolve(backendDir, 'database'), { recursive: true });

execSync('php artisan migrate:fresh --force', { cwd: backendDir, env, stdio: 'inherit' });
execSync('php artisan db:seed --class=E2eSeeder --force', { cwd: backendDir, env, stdio: 'inherit' });

const server = spawn('php', ['artisan', 'serve', '--host=127.0.0.1', '--port=8010'], {
  cwd: backendDir,
  env,
  stdio: 'inherit',
});

server.on('exit', (code) => {
  process.exit(code ?? 0);
});
