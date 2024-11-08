FROM php:fpm

COPY php.ini /usr/local/etc/php

RUN apt update \
    && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql zip gd

RUN curl -sS http://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update

WORKDIR /var/www