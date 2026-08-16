import { execSync } from 'node:child_process';
import { existsSync, mkdirSync, rmSync } from 'node:fs';
import { dirname } from 'node:path';
import { backendDir } from './lib/paths.js';
import { getBackendEnv } from './lib/env.js';

export default async function globalSetup() {
  console.log('[globalSetup] Preparing test database...');
  const env = getBackendEnv();
  const dbPath = env.DB_DATABASE as string;

  if (existsSync(dbPath)) {
    rmSync(dbPath, { force: true });
  }

  const journalPath = dbPath + '-journal';
  if (existsSync(journalPath)) {
    rmSync(journalPath, { force: true });
  }

  mkdirSync(dirname(dbPath), { recursive: true });

  execSync('php artisan migrate:fresh --force', {
    cwd: backendDir,
    env,
    stdio: 'inherit',
  });

  execSync('php artisan db:seed --class=E2eSeeder --force', {
    cwd: backendDir,
    env,
    stdio: 'inherit',
  });
}
