#!/bin/bash
# This tells the system to run this script with bash

# Check if APP_KEY environment variable exists
if [ -z "$APP_KEY" ]; then
    # If empty, generate a new one
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run database migrations automatically
echo "Running migrations..."
php artisan migrate --force

# Cache Laravel configuration for better performance
echo "Caching configuration..."
php artisan config:cache   # Cache config files
php artisan route:cache    # Cache routes
php artisan view:cache     # Cache Blade templates

# Start PHP-FPM in the background (-D = daemon mode)
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in the foreground (keeps container running)
echo "Starting Nginx..."
nginx -g 'daemon off;'
