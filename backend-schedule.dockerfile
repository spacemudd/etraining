FROM php:7.4-fpm

LABEL description="A supervisor configured to run with artisan horizon command"

RUN apt-get update

RUN apt-get install -y \
    mariadb-client \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    zip \
    libbz2-dev

RUN pecl channel-update pecl.php.net \
    && pecl install redis

RUN docker-php-ext-install zip bz2 pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    pdo_mysql \
    pcntl \
    posix \
    exif \
    && docker-php-ext-enable redis

# For wkhtmltopdf
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get install -yq build-essential xorg libssl-dev libxrender-dev wget gdebi

# Install Windows fonts.
RUN wget http://ftp.br.debian.org/debian/pool/contrib/m/msttcorefonts/ttf-mscorefonts-installer_3.6_all.deb
RUN apt-get -y install cabextract
RUN dpkg -i ttf-mscorefonts-installer_3.6_all.deb

# 15 minutes execution time.
RUN echo "memory_limit=2048M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "max_execution_time=900" >> $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "date.timezone=${PHP_TIMEZONE:-UTC}" > $PHP_INI_DIR/conf.d/date_timezone.ini
RUN echo "cgi.fix_pathinfo=0" > $PHP_INI_DIR/conf.d/path-info.ini
RUN echo "expose_php=0" > $PHP_INI_DIR/conf.d/path-info.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY backend/ /var/www

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/www www
RUN mkdir -p /home/www/.composer && \
    chown -R www:www /home/www

# Set working directory
WORKDIR /var/www

USER $user

#RUN cp .env.example .env && \
#    composer install --no-dev && \
#    php artisan key:generate && \
#    chgrp -R www-data storage bootstrap/cache && \
#    chmod -R ug+rwx storage bootstrap/cache

RUN composer install --no-dev && \
    php artisan key:generate --force && \
    chgrp -R www-data storage bootstrap/cache && \
    chmod -R ug+rwx storage bootstrap/cache

RUN apt update
RUN apt-get install -y -u python3-pip
RUN pip install supervisor

WORKDIR /etc/supervisor/conf.d

COPY backend-schedule/scheduler.conf /etc/supervisor/conf.d/supervisord.conf

COPY backend-schedule/init.sh /usr/local/bin/init.sh

ENTRYPOINT ["/bin/sh", "/usr/local/bin/init.sh"]
