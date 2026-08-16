import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const currentDir = dirname(fileURLToPath(import.meta.url));

export const rootDir = resolve(currentDir, '..', '..');
export const backendDir = resolve(rootDir, 'backend');
export const frontendDir = resolve(rootDir, 'frontend');
export const dbPath = resolve(backendDir, 'database', 'e2e.sqlite');
