# Tahap 1: Builder
FROM php:8.2-cli as builder

# Install dependency sistem
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /app

# Copy file composer & install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy semua source code
COPY . .

# Build cache config, route, dan view
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache


# Tahap 2: Runtime
FROM php:8.2-cli

# Install dependency sistem
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

WORKDIR /app

# Copy hasil build dari tahap builder
COPY --from=builder /app /app

# Expose port default Laravel
EXPOSE 8000

# Start Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
