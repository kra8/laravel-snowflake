FROM php:8.1-fpm-alpine
SHELL ["/bin/ash", "-oeux", "pipefail", "-c"]

ENV COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apk update && \
  apk add --update --no-cache --virtual=.build-deps \
    autoconf \
    gcc \
    g++ \
    make && \
  apk add --update --no-cache \
    icu-dev \
    libzip-dev \
    oniguruma-dev && \
  apk del .build-deps && \
  docker-php-ext-install intl pdo_mysql mbstring zip bcmath && \
  composer config -g process-timeout 3600 && \
  composer config -g repos.packagist composer https://packagist.org

