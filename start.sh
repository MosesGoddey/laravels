#!/bin/bash

echo "=== Starting Laravel Application ==="

# Fix permissions
echo "Setting permissions..."
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Clear all caches
echo "Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Run AdminSeeder (THIS IS THE KEY LINE!)
echo "Seeding admin user..."
php artisan db:seed --class=AdminSeeder --force

# Cache configuration for performance
echo "Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start PHP-FPM
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx and tail logs
echo "Starting Nginx..."
tail -f /var/www/html/storage/logs/laravel.log &
nginx -g 'daemon off;'
