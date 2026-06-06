# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Copy all files (including local vendor directory now that it's allowed in .dockerignore)
COPY . .

# Debug: Print the folder structure to verify vendor files are copied
RUN ls -la && ls -la vendor/ || true

# Build assets (Vite can now resolve @import '../../vendor/livewire/flux/dist/flux.css')
RUN npm ci && npm run build


# Stage 2: Production Application Image
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install production system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    dos2unix

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Copy application files (including local vendor folder)
COPY . .

# Copy compiled assets from frontend-builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Setup Supervisor log directory
RUN mkdir -p /var/log/supervisor

# Copy configuration files
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Fix line endings of entrypoint.sh in case built from Windows
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Configure storage and bootstrap permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Configure entrypoint and start supervisor
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
