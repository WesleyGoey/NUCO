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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app/NUCO

# Copy composer files
COPY NUCO/composer.json NUCO/composer.lock ./

# ✅ FIX: Install ALL dependencies (including faker)
RUN composer install --optimize-autoloader --no-interaction --prefer-dist

# Copy package files
COPY NUCO/package.json NUCO/package-lock.json ./

# Install Node dependencies
RUN npm ci

# Copy application
COPY NUCO/ .

# Build frontend assets
RUN npm run build && \
    echo "=== Vite Build Complete ===" && \
    ls -la public/build/ && \
    cat public/build/manifest.json

# Optimize Laravel
RUN composer dump-autoload --optimize
RUN php artisan package:discover --ansi
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Set permissions
RUN chmod -R 755 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php artisan inspire || exit 1

# ✅ FIXED: Migrate fresh untuk Railway deployment
CMD echo "=== Starting Deployment ===" && \
    echo "PORT: ${PORT:-8000}" && \
    echo "DB_HOST: ${DB_HOST:-not-set}" && \
    echo "=== Running Migrations ===" && \
    php artisan migrate:fresh --force --seed && \
    echo "=== Creating Storage Link ===" && \
    php artisan storage:link && \
    echo "=== Starting Laravel Server ===" && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

# ✅ Verify Faker is installed
RUN echo "=== Verifying Faker Installation ===" && \
    php -r "require 'vendor/autoload.php'; echo class_exists('Faker\\Factory') ? 'Faker OK' : 'Faker MISSING';" && \
    echo ""
