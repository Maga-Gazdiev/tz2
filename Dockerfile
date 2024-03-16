# syntax=docker/dockerfile:1

FROM composer:lts as deps
WORKDIR /app
COPY composer.json composer.lock /app/

RUN touch artisan
RUN --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction

FROM php:8.2-apache as final

COPY --from=deps /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y git unzip

RUN apt-get update && apt-get install -y mariadb-client

RUN docker-php-ext-install pdo pdo_mysql
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=deps /app/vendor/ /var/www/html/vendor
COPY . /var/www/html

RUN composer install

COPY .env.example /var/www/html/.env


RUN php artisan key:generate


COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/html/storage/logs \
    && touch /var/www/html/storage/logs/laravel.log \
    && chmod -R 775 /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage

USER www-data
