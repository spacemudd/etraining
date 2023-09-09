FROM trafex/php-nginx:latest

# Supervisord settings
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# NGINX settings
COPY nginx.default.conf /etc/nginx/nginx.conf
COPY nginx.server.conf /etc/nginx/conf.d/server.conf

# PHP-FPM settings
COPY php-fpm-settings.conf /etc/php82/php-fpm.d/server.conf

# PHP settings
COPY php-settings.ini /etc/php82/conf.d/settings.ini

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
    php81-ctype \
    php81-curl \
    php81-dom \
    php81-gd \
    php81-iconv \
    php81-json \
    php81-intl \
    php81-fileinfo\
    php81-mbstring \
    php81-opcache \
    php81-openssl \
    php81-pdo \
    php81-pdo_mysql \
    php81-mysqli \
    php81-sqlite3 \
    php81-pdo_sqlite \
    php81-xml \
    php81-zlib \
    php81-phar \
    php81-tokenizer \
    php81-session \
    php81-simplexml \
    php81-xmlwriter \
    php81-zip \
    php81-exif \
    php81-xmlreader

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# For wkhtmltopdf
RUN apk add --no-cache \
        wkhtmltopdf \
        xvfb \
        ttf-dejavu ttf-droid ttf-freefont ttf-liberation \
        msttcorefonts-installer ttf-dejavu ttf-droid ttf-ubuntu-font-family fontconfig update-ms-fonts ttf-liberation \
        libgcc \
        libstdc++ \
        musl \
        qt5-qtbase \
        qt5-qtbase-x11 \
        qt5-qtsvg \
        qt5-qtwebkit \
        ttf-freefont \
        ttf-ubuntu-font-family \
        fc-cache -f

#download wkhtmltopdf
COPY --from=madnight/alpine-wkhtmltopdf-builder:0.12.5-alpine3.10-606718795 /bin/wkhtmltopdf /usr/bin/wkhtmltopdf2

RUN ln -s /usr/bin/wkhtmltopdf2 /usr/local/bin/wkhtmltopdf2
RUN chmod +x /usr/local/bin/wkhtmltopdf2
RUN chmod 1777 /usr/local/bin/wkhtmltopdf2

#RUN wget https://github.com/h4cc/wkhtmltopdf-amd64/blob/master/bin/wkhtmltopdf-amd64?raw=true -O /usr/local/bin/wkhtmltopdf \
#    && chmod +x /usr/local/bin/wkhtmltopdf

USER nobody

EXPOSE 8900

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:9000/fpm-ping
