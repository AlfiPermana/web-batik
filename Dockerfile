# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY . .
RUN npm ci && npm run build

# Stage 2: Main Application
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and php extensions
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

# Get composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Copy compiled frontend assets from frontend-builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Handle flux license arguments if provided (Flux UI components require authentication)
ARG FLUX_USERNAME
ARG FLUX_LICENSE_KEY
RUN if [ -n "$FLUX_USERNAME" ] && [ -n "$FLUX_LICENSE_KEY" ]; then \
        composer config http-basic.composer.fluxui.dev "$FLUX_USERNAME" "$FLUX_LICENSE_KEY"; \
    fi

# Install production dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

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
