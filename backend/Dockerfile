#FROM composer AS composer
#
## copying the source directory and install the dependencies with composer
#COPY . /app
#
## run composer install to install the dependencies
#RUN composer install \
#  --optimize-autoloader \
#  --no-interaction \
#  --no-progress

FROM trafex/alpine-nginx-php7:latest
#COPY --chown=nginx --from=composer /app /var/www/html
COPY nginx.conf /etc/nginx/conf.d/server.conf

COPY . /var/www/html
# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

USER root
RUN apk update

RUN apk add \
    mariadb-client \
    zip \
    coreutils \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-gd \
    php7-iconv \
    php7-imagick \
    php7-json \
    php7-intl \
    php7-mcrypt \
    php7-fileinfo\
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_mysql \
    php7-mysqli \
    php7-xml \
    php7-zlib \
    php7-phar \
    php7-tokenizer \
    php7-session \
    php7-simplexml \
    php7-xmlwriter \
    php7-zip

RUN composer install --optimize-autoloader --no-interaction --no-progress

#RUN apt-get install -y php7.4-dev
#
#RUN pecl channel-update pecl.php.net \
#    && pecl install redis
#
# # Clean the image \
#RUN apt-get remove -qq -y php7.4-dev pkg-config libmagickwand-dev build-essential \
#     && apt-get auto-remove -qq -y \
#     && apt-get clean \
#     && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
# Create system user to run Composer and Artisan Commands
#RUN useradd -G www-data,root -u 1000 -d /app www
#RUN mkdir -p /app/.composer && \
#    chown -R www:www /app

EXPOSE 8080

# CMD ["/bin/bash", "-c", "cp .env.example .env && composer install --no-dev && php artisan key:generate && chgrp -R www-data storage bootstrap/cache && "]
RUN cp .env.example .env && \
    composer install --no-dev && \
    php artisan key:generate

USER nobody
#    chgrp -R www-data storage bootstrap/cache && \
#    chmod -R ug+rwx storage bootstrap/cache
