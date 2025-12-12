FROM php:8.4-fpm-alpine

# Dependencies
RUN apk update && apk add --no-cache \
    git curl unzip zip bash shadow icu-dev oniguruma-dev libxml2-dev libzip-dev postgresql-dev \
    libpng-dev freetype-dev libjpeg-turbo-dev libwebp-dev libxpm-dev \
    autoconf make g++ gcc musl-dev libtool pkgconf build-base

# Configure GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --with-xpm

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    intl \
    soap

# Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Enable OPcache
RUN docker-php-ext-install opcache

# Cleanup
RUN apk del build-base autoconf make g++ gcc musl-dev libtool pkgconf \
    && rm -rf /tmp/pear /var/cache/apk/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
