FROM php:7.4-fpm

LABEL description="A supervisor configured to run with artisan horizon command"

RUN apt-get update

RUN apt-get install -y \
    mariadb-client \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libbz2-dev \
    libzip-dev \
    zip \
    libbz2-dev \
    && pecl channel-update pecl.php.net \
    && pecl install apcu \
    && pecl install redis

RUN docker-php-ext-install zip bz2 mbstring pdo pdo_pgsql pcntl \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    pdo_mysql \
    mbstring \
    pcntl \
    posix \
    exif \
    && docker-php-ext-enable redis

# Imagick
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick

# For wkhtmltopdf
RUN apt-get update
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get install -yq build-essential checkinstall
RUN apt-get install -yq xorg libssl-dev libxrender-dev wget gdebi

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

RUN apt update
RUN apt-get install -y -u python-pip
RUN pip install supervisor

WORKDIR /etc/supervisor/conf.d

COPY horizon.conf.tpl /etc/supervisor/conf.d/horizon.conf.tpl

COPY init.sh /usr/local/bin/init.sh

ENTRYPOINT ["/bin/sh", "/usr/local/bin/init.sh"]
