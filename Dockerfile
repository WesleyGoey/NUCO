FROM php:8.4-cli

# Force rebuild - PHP 8.4 required
RUN php -v

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (including zip)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Configure PHP for file uploads
RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/php.ini-production && \
    echo "post_max_size = 12M" >> /usr/local/etc/php/php.ini-production && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini-production && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini-production && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory to NUCO subdirectory
WORKDIR /app/NUCO

# Copy composer files from NUCO directory
COPY NUCO/composer.json NUCO/composer.lock ./

# Install dependencies (production only) - skip scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copy package files from NUCO directory
COPY NUCO/package.json NUCO/package-lock.json ./

# Install npm dependencies
RUN npm ci

# Copy all NUCO application files
COPY NUCO/ .

# Build assets with Vite
RUN npm run build && \
    echo "=== Vite Build Complete ===" && \
    ls -la public/build/ && \
    cat public/build/manifest.json

# Run composer scripts after files are copied
RUN composer dump-autoload --optimize

# Run Laravel package discovery
RUN php artisan package:discover --ansi

# Clear Laravel caches
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Set permissions
RUN chmod -R 755 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Start the Laravel development server using Railway's PORT variable
CMD echo "=== Starting Laravel Server ===" && \
    echo "PORT: ${PORT:-8000}" && \
    echo "APP_KEY: ${APP_KEY:0:20}..." && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000} --no-reload
