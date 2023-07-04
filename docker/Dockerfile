FROM php:8.1-cli

WORKDIR /var/www/html

USER root:root

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && apt-get install git zip unzip vim -y \
    && apt-get clean

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/*

ARG UID=1000
ARG GID=1000

RUN groupmod -g ${GID} www-data \
  && usermod -u ${UID} -g www-data www-data \
  && chown -hR www-data:www-data \
    /var/www \
    /usr/local/

COPY custom.ini /usr/local/etc/php/conf.d/custom.ini

USER www-data:www-data
