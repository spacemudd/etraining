FROM php:7.4-fpm

RUN apt-get update

RUN apt-get install -y \
    mariadb-client \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    zip \
    libicu-dev \
    libbz2-dev \
    git

RUN pecl channel-update pecl.php.net \
    && pecl install redis

RUN docker-php-ext-install zip bz2 pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql exif \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Imagick
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick

# For wkhtmltopdf
RUN apt update
RUN apt install -y software-properties-common ca-certificates wget curl ssh
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get install -yq xorg libssl-dev libxrender-dev wget gdebi

# Install Windows fonts.
RUN wget http://ftp.br.debian.org/debian/pool/contrib/m/msttcorefonts/ttf-mscorefonts-installer_3.6_all.deb
RUN apt-get -y install cabextract
RUN dpkg -i ttf-mscorefonts-installer_3.6_all.deb


# APCU
RUN pecl install apcu && docker-php-ext-enable apcu
RUN echo "extension=apcu.so" > $PHP_INI_DIR/conf.d/apcu.ini
RUN echo "apc.enable_cli=1" > $PHP_INI_DIR/conf.d/apcu.ini
RUN echo "apc.enable=1" > $PHP_INI_DIR/conf.d/apcu.ini

# Memory Limit
RUN echo "memory_limit=2048M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "max_execution_time=900" >> $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "post_max_size=20M" >> $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "upload_max_filesize=20M" >> $PHP_INI_DIR/conf.d/memory-limit.ini

# Time Zone
RUN echo "date.timezone=${PHP_TIMEZONE:-UTC}" > $PHP_INI_DIR/conf.d/date_timezone.ini

# Display errors in stderr
RUN echo "display_errors=stderr" > $PHP_INI_DIR/conf.d/display-errors.ini

# Disable PathInfo
RUN echo "cgi.fix_pathinfo=0" > $PHP_INI_DIR/conf.d/path-info.ini

# Disable expose PHP
RUN echo "expose_php=0" >> $PHP_INI_DIR/conf.d/path-info.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["/bin/sh", "-c", "php-fpm -D | tail -f storage/logs/laravel.log"]
