# syntax=docker/dockerfile:1

# Stage 1: Build the Vue frontend
FROM node:20-alpine AS frontend-builder

WORKDIR /app/frontend

COPY frontend/package*.json ./
RUN npm ci

COPY frontend/ ./
RUN npm run build

# Stage 2: Install PHP backend dependencies
FROM php:8.2-cli AS php-builder

WORKDIR /app/backend

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite mbstring xml zip curl bcmath openssl fileinfo sockets pcntl

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files and install production dependencies
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy backend source and optimize autoloader
COPY backend/ ./
RUN composer dump-autoload --optimize

# Stage 3: Runtime image
FROM php:8.2-cli

WORKDIR /var/www/html/backend

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    gettext \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite mbstring xml zip curl bcmath openssl fileinfo sockets pcntl

# Copy backend from builder
COPY --from=php-builder /app/backend /var/www/html/backend

# Copy frontend dist
COPY --from=frontend-builder /app/frontend/dist /var/www/html/frontend/dist

# Copy Nginx config template, supervisord config and entrypoint
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Download the RoadRunner binary for Octane
RUN php /var/www/html/backend/vendor/bin/rr get-binary -n \
    --os=linux \
    --arch=amd64 \
    --location=/usr/local/bin

# Make the entrypoint executable and set permissions
RUN chmod +x /entrypoint.sh && \
    mkdir -p /var/www/html/backend/database/storage && \
    chown -R www-data:www-data /var/www/html/backend/storage \
        /var/www/html/backend/bootstrap/cache \
        /var/www/html/backend/database/storage

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
