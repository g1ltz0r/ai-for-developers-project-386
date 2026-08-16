#!/bin/sh
set -e

PORT=${PORT:-8080}

if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php /var/www/html/backend/artisan key:generate --show)
fi

export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}
export APP_URL=${APP_URL:-http://localhost:${PORT}}
export DB_CONNECTION=${DB_CONNECTION:-sqlite}
export DB_DATABASE=${DB_DATABASE:-/var/www/html/backend/database/storage/database.sqlite}
export CACHE_STORE=${CACHE_STORE:-array}
export SESSION_DRIVER=${SESSION_DRIVER:-array}
export QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}

mkdir -p /var/www/html/backend/database/storage
touch "$DB_DATABASE"

mkdir -p /var/www/html/backend/storage/app
mkdir -p /var/www/html/backend/storage/framework/cache/data
mkdir -p /var/www/html/backend/storage/framework/sessions
mkdir -p /var/www/html/backend/storage/framework/testing
mkdir -p /var/www/html/backend/storage/framework/views
mkdir -p /var/www/html/backend/storage/logs

chown -R www-data:www-data /var/www/html/backend/storage
chmod -R 775 /var/www/html/backend/storage

chown -R www-data:www-data /var/www/html/backend/bootstrap/cache
chmod -R 775 /var/www/html/backend/bootstrap/cache

php /var/www/html/backend/artisan migrate --force
php /var/www/html/backend/artisan config:cache

envsubst '$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
