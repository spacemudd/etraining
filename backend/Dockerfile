FROM trafex/php-nginx:3.4.0

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
    chown -R nobody:nobody /var/run/php

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

USER root
RUN apk update

RUN apk add \
    mariadb-client \
    zip \
    coreutils \
    php82-ctype \
    php82-curl \
    php82-dom \
    php82-gd \
    php82-iconv \
    php82-json \
    php82-intl \
    php82-fileinfo\
    php82-mbstring \
    php82-opcache \
    php82-openssl \
    php82-pdo \
    php82-pdo_mysql \
    php82-mysqli \
    php82-sqlite3 \
    php82-pdo_sqlite \
    php82-xml \
    php82-zlib \
    php82-phar \
    php82-tokenizer \
    php82-session \
    php82-simplexml \
    php82-xmlwriter \
    php82-zip \
    php82-exif \
    php82-xmlreader

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# For wkhtmltopdf
RUN apk add --no-cache \
        wkhtmltopdf \
        xvfb \
        wget \
        cabextract \
        ttf-dejavu ttf-droid ttf-freefont ttf-liberation \
        ttf-ubuntu-font-family \
        fontconfig \
        libgcc \
        libstdc++ \
        musl \
        qt5-qtbase \
        qt5-qtbase-x11 \
        qt5-qtsvg \
        qt5-qtwebkit \
        openssl1.1-compat

# Install Microsoft Core Fonts (Arial, Times New Roman, etc.)
RUN mkdir -p /usr/share/fonts/truetype/msttcorefonts && \
    cd /tmp && \
    wget -q http://downloads.sourceforge.net/corefonts/arial32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/arialb32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/comic32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/courie32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/georgi32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/impact32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/times32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/trebuc32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/verdan32.exe && \
    wget -q http://downloads.sourceforge.net/corefonts/webdin32.exe && \
    cabextract -q -d /usr/share/fonts/truetype/msttcorefonts *.exe && \
    rm -f *.exe && \
    fc-cache -f -v

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
