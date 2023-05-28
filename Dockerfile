
FROM php:8.1-fpm as fpm_server

WORKDIR /var/www/smstemplate

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    iputils-ping \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zlib1g-dev \
    libicu-dev \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    libxml2-dev \
    libbz2-dev \
    libjpeg62-turbo-dev \
    librabbitmq-dev \
    libzip-dev \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    git \
    curl \
    lua-zlib-dev \
    nano \
    htop \
    wget \
    procps \
    supervisor \
    gnupg \
    gosu  \
    cron \
    ca-certificates  \
    libcap2-bin  \
    python2 \
    && docker-php-ext-configure bcmath --enable-bcmath \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql \
    && docker-php-ext-configure soap --enable-soap \
    && docker-php-ext-install \
    bcmath \
    intl \
    mysqli \
    pcntl \
    pdo_mysql \
    pdo \
    soap \
    zip \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev \
#    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
#    && docker-php-ext-install -j$(nproc) gd
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd



RUN apt-get update -yqq && \
    apt-get install -y --no-install-recommends openssl && \
    sed -i 's,^\(MinProtocol[ ]*=\).*,\1'TLSv1.0',g' /etc/ssl/openssl.cnf && \
    sed -i 's,^\(CipherString[ ]*=\).*,\1'DEFAULT@SECLEVEL=1',g' /etc/ssl/openssl.cnf && rm -rf /var/lib/apt/lists/*

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY --chown=www:www-data . /var/www/smstemplate

COPY ./docker/composer.phar /usr/local/bin/composer
RUN chmod 777 /usr/local/bin/composer

COPY docker/php.ini /etc/php/8.0/cli/conf.d/php.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/

RUN cd /usr/local/etc/php/conf.d/ && \
    echo 'memory_limit=-1' >>/usr/local/etc/php/conf.d/docker-php-ram-limit.ini

EXPOSE 9000

FROM nginx:alpine as web_server

COPY docker/nginx/nginx.conf /etc/nginx/

RUN apk update \
    && apk upgrade \
    && apk --update add logrotate \
    && apk add --no-cache openssl \
    && apk add --no-cache bash

RUN apk add --no-cache curl

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

ARG PHP_UPSTREAM_CONTAINER=app
ARG PHP_UPSTREAM_PORT=9000

# ------------------------------------------------------------------------------------------------------------------
COPY docker/nginx/conf.d/app.conf /etc/nginx/conf.d/default.conf
#COPY docker/nginx/ssl/default.key /etc/nginx/ssl/default.key
#COPY docker/nginx/ssl/default.crt /etc/nginx/ssl/default.crt

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Create 'messages' file used from 'logrotate'
RUN touch /var/log/messages

# Copy 'logrotate' config file
COPY docker/nginx/logrotate/nginx /etc/logrotate.d/

EXPOSE 80 81 443
