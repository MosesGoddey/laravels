# Use PHP 8.2 with FPM (FastCGI Process Manager)
# FPM is optimized for handling multiple PHP requests efficiently
FROM php:8.2-fpm

# Set where all commands will run inside the container
WORKDIR /var/www/html

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y \
    git \                    # For Composer packages from Git
    unzip \                  # To extract Composer packages
    libonig-dev \           # Required for mbstring extension
    libzip-dev \            # Required for zip extension
    zip \                    # To create zip files
    curl \                   # To make HTTP requests
    nginx \                  # Web server (THIS IS THE KEY!)
    && apt-get clean && rm -rf /var/lib/apt/lists/*  # Clean up to reduce image size

# Install PHP extensions that Laravel needs
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

# Copy Composer from the official Composer image
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy all your Laravel project files into the container
COPY . .

# Install all PHP dependencies (no dev dependencies for production)
RUN composer install --no-dev --optimize-autoloader

# Set proper file permissions
# www-data is the user that Nginx and PHP-FPM run as
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \      # Laravel needs to write logs here
    && chmod -R 755 /var/www/html/bootstrap/cache # Laravel caches files here

# Copy our custom Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy our startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh  # Make it executable

# Tell Docker which port this container will use
EXPOSE 8000

# Run our startup script when the container starts
CMD ["/usr/local/bin/start.sh"]
