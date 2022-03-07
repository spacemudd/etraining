FROM trafex/alpine-nginx-php7:latest

# Supervisord settings
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# NGINX settings
COPY nginx.default.conf /etc/nginx/nginx.conf
COPY nginx.server.conf /etc/nginx/conf.d/server.conf

# PHP-FPM settings
COPY php-fpm-settings.conf /etc/php7/php-fpm.d/www.conf

# PHP settings
COPY php-settings.ini /etc/php7/conf.d/settings.ini

COPY --chown=nobody . /var/www/html
RUN mkdir -p /var/run/php && \
    chown -R nobody.nobody /var/run/php

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
    php7-sqlite3 \
    php7-pdo_sqlite \
    php7-xml \
    php7-zlib \
    php7-phar \
    php7-tokenizer \
    php7-session \
    php7-simplexml \
    php7-xmlwriter \
    php7-zip \
    php7-exif

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# For wkhtmltopdf
ENV DEBIAN_FRONTEND=noninteractive
RUN apk add -yq build-essential xorg libssl-dev libxrender-dev wget gdebi

USER nobody

EXPOSE 8900

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:9000/fpm-ping
