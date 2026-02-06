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

FROM node:20-alpine AS webpack_build

WORKDIR /app
COPY package.json ./
RUN npm i
COPY webpack ./webpack
COPY assets ./assets
RUN npm run build


FROM php:8.5-apache

WORKDIR /var/www

RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install libyaml-dev -y
RUN pecl install yaml
RUN echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini
RUN docker-php-ext-enable yaml
RUN a2enmod rewrite


COPY . /var/www
COPY --from=composer_deps /app/vendor /var/www/vendor
COPY --from=webpack_build /app/html/assets /var/www/html/assets
COPY prod-php.ini /usr/local/etc/php/conf.d/50-prod.ini



EXPOSE 80
