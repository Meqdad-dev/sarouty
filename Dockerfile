# ── Stage 1: Build frontend assets ───────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --prefer-offline
COPY . .
RUN npm run build

# ── Stage 2: PHP Application ──────────────────────────────────────────────────
FROM php:8.4-fpm-alpine

# System dependencies (gettext pour envsubst)
RUN apk add --no-cache \
    bash \
    nginx \
    supervisor \
    gettext \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    postgresql-dev

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml \
    ctype \
    fileinfo \
    intl \
    opcache

# OPcache configuration
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Uploads and Memory configuration
RUN echo "upload_max_filesize=50M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project files
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=node-builder /app/public/build ./public/build

# PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Storage directories + permissions
RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && chown -R www-data:www-data /var/www

# Fix Nginx permissions for large file uploads
RUN mkdir -p /var/lib/nginx/tmp/client_body \
 && chown -R www-data:www-data /var/lib/nginx

# Copy Docker configs
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf    /etc/supervisor/conf.d/supervisord.conf
COPY docker/php-fpm.conf        /usr/local/etc/php-fpm.d/zzz-render.conf

# Startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/bin/sh", "/start.sh"]
