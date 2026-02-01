FROM composer:2 AS composer_deps

WORKDIR /app
COPY composer.json ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader
COPY . .
RUN composer dump-autoload --optimize


FROM php:8.5-apache

WORKDIR /var/www
COPY . /var/www
COPY --from=composer_deps /app/vendor /var/www/vendor
COPY prod-php.ini /usr/local/etc/php/conf.d/50-prod.ini

RUN a2enmod rewrite

EXPOSE 80
