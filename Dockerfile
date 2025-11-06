# Use the official PHP image with Composer and necessary extensions
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

# Copy Composer from official image
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose the port Laravel will run on
EXPOSE 8000

# Start Laravel at runtime, not build time
CMD php artisan key:generate --force && \
    php artisan migrate --force && \
    php artisan config:cache && \
    php artisan serve --host=0.0.0.0 --port=8000
