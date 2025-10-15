# Stage 1: Build MS Core Fonts in Debian (more reliable)
FROM debian:bullseye-slim as fonts
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    echo "ttf-mscorefonts-installer msttcorefonts/accepted-mscorefonts-eula select true" | debconf-set-selections && \
    apt-get install -y wget cabextract fontconfig && \
    wget -q http://ftp.nl.debian.org/debian/pool/contrib/m/msttcorefonts/ttf-mscorefonts-installer_3.8.1_all.deb && \
    dpkg -i ttf-mscorefonts-installer_3.8.1_all.deb || apt-get install -f -y && \
    fc-cache -f -v

# Stage 2: Main application image
FROM trafex/php-nginx:3.4.0

# Supervisord settings
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# NGINX settings
ARG CACHE_BUST=1
ARG CACHE_BUST=1
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
        
        xvfb \
        ttf-dejavu ttf-droid ttf-freefont ttf-liberation \
        
        fontconfig \
        libgcc \
        libstdc++ \
        musl \
        qt5-qtbase \
        qt5-qtbase-x11 \
        qt5-qtsvg \
        
        openssl1.1-compat

# Copy Microsoft Core Fonts (Arial, Times New Roman, etc.) from Debian stage
COPY --from=fonts /usr/share/fonts/truetype/msttcorefonts /usr/share/fonts/truetype/msttcorefonts

# Also copy to Qt fonts directory for wkhtmltopdf and set up fontconfig
RUN mkdir -p /usr/lib/qt5/fonts /etc/fonts/conf.d && \
    cp -r /usr/share/fonts/truetype/msttcorefonts/* /usr/lib/qt5/fonts/ || true && \
    fc-cache -f -v && \
    fc-list | grep -i arial && \
    echo "Fonts installed successfully"

# Create fontconfig file to prioritize Arial
RUN echo '<?xml version="1.0"?>' > /etc/fonts/local.conf && \
    echo '<!DOCTYPE fontconfig SYSTEM "fonts.dtd">' >> /etc/fonts/local.conf && \
    echo '<fontconfig>' >> /etc/fonts/local.conf && \
    echo '  <dir>/usr/share/fonts/truetype/msttcorefonts</dir>' >> /etc/fonts/local.conf && \
    echo '  <dir>/usr/lib/qt5/fonts</dir>' >> /etc/fonts/local.conf && \
    echo '  <alias>' >> /etc/fonts/local.conf && \
    echo '    <family>sans-serif</family>' >> /etc/fonts/local.conf && \
    echo '    <prefer><family>Arial</family></prefer>' >> /etc/fonts/local.conf && \
    echo '  </alias>' >> /etc/fonts/local.conf && \
    echo '</fontconfig>' >> /etc/fonts/local.conf && \
    fc-cache -f -v

#download wkhtmltopdf
COPY --from=madnight/alpine-wkhtmltopdf-builder:0.12.5-alpine3.10-606718795 /bin/wkhtmltopdf /usr/bin/wkhtmltopdf2

RUN ln -s /usr/bin/wkhtmltopdf2 /usr/local/bin/wkhtmltopdf2
RUN chmod +x /usr/local/bin/wkhtmltopdf2
RUN chmod 1777 /usr/local/bin/wkhtmltopdf2

#RUN wget https://github.com/h4cc/wkhtmltopdf-amd64/blob/master/bin/wkhtmltopdf-amd64?raw=true -O /usr/local/bin/
#    && chmod +x /usr/local/bin/wkhtmltopdf

USER nobody

EXPOSE 8900

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:9000/fpm-ping
