FROM trafex/alpine-nginx-php7:latest

# Supervisord settings
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# NGINX settings
COPY nginx.default.conf /etc/nginx/nginx.conf
COPY nginx.server.conf /etc/nginx/conf.d/server.conf

# PHP-FPM settings
COPY php-fpm-settings.conf /etc/php7/php-fpm.d/www.conf

COPY --chown=nobody . /var/www/html

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
#RUN adduser -G www-data,root -u 1000 -h /var/www/html www
#RUN mkdir -p /var/www/html/.composer && \
#    chown -R www:www /var/www/html

RUN cp .env.example .env && \
    php artisan key:generate

USER nobody

EXPOSE 8900

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:9000/fpm-ping
curl -http://127.0.0.1:9000/fpm-ping
