# =========================
# Stage 1: Build frontend (Vite)
# =========================
FROM node:22-alpine AS build

WORKDIR /app

# Copy dependency files dari src/
COPY src/package*.json ./
COPY src/vite.config.* ./

# Install dependencies
RUN npm install --legacy-peer-deps

# Copy seluruh source frontend
COPY src/ ./

# Build Vite
RUN npm run build


# =========================
# Stage 2: PHP + Nginx + Laravel
# =========================
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    bash \
    wget \
    git \
    zip unzip \
    libjpeg-turbo-dev \
    libpng-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd pdo_mysql zip exif

RUN mkdir -p /run/nginx /app /app/public

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/startup.sh /startup.sh
RUN chmod +x /startup.sh

WORKDIR /app

# Copy seluruh Laravel project
COPY src/ /app

# Copy hasil build frontend ke public/build
COPY --from=build /app/public/build /app/public/build

# Install composer
RUN wget https://getcomposer.org/composer.phar \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage /app/bootstrap/cache

EXPOSE 8080
CMD ["/startup.sh"]
