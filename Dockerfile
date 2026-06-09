# Stage 1: Build PHP dependencies (Public Packagist)
FROM php:8.2-fpm-alpine AS php-builder

WORKDIR /app

# Install system dependencies needed for Composer
RUN apk add --no-cache \
    git \
    unzip \
    libpng-dev \
    libxml2-dev \
    zip \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev

# Install PHP extensions required for Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Get Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy only dependency files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Support optional GitHub token for bypassing GitHub API rate limit if encountered
ARG GITHUB_TOKEN
RUN if [ -n "$GITHUB_TOKEN" ]; then \
        composer config --global github-oauth.github.com "$GITHUB_TOKEN"; \
    fi

# Run composer install to generate the vendor directory
RUN composer install --no-interaction --no-dev --no-scripts --optimize-autoloader

# Copy the rest of the application files to generate final autoload mappings
COPY . .
RUN composer dump-autoload --optimize --no-dev


# Stage 2: Build frontend assets
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Copy package files
COPY package.json package-lock.json ./
RUN npm ci

# Copy source files
COPY . .

# Copy the vendor directory from php-builder stage so Vite can find the Flux CSS/JS files
COPY --from=php-builder /app/vendor ./vendor

# Build assets
RUN npm run build


# Stage 3: Production Application Image
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

# Copy application files (vendor is ignored in host COPY, we copy it from builder)
COPY . .

# Copy production vendor directory from php-builder stage
COPY --from=php-builder /app/vendor ./vendor

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
