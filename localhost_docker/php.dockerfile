FROM php:7.4-fpm

# Set working directory
WORKDIR /var/www/html

RUN apt-get update \
    # Install dependencies for the operating system software
    && apt-get install -y \
    build-essential libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    jpegoptim libzip-dev libonig-dev \
    unzip zip locales vim git curl optipng pngquant gifsicle \
    # Clear cache
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install general extensions for php
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install php-INTL extension
RUN apt-get -y update \
    && apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# # Install composer (php package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Expose port 9000 and start php-fpm server (for FastCGI Process Manager)
EXPOSE 9000
CMD ["php-fpm"]