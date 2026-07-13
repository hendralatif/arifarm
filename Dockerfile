FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions
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
    libzip-dev

RUN docker-php-ext-install pdo_mysql bcmath gd zip opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install node dependencies and build assets
RUN apk add --no-cache nodejs npm && \
    npm install && \
    npm run build

# Configure Nginx & Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Setup permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
