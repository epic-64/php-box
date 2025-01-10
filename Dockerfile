FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git zip unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

RUN git config --global --add safe.directory /var/www

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www