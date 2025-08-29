# PHP latest stable (8.4) на Alpine
FROM php:8.4-fpm-alpine

# Базові runtime-залежності
RUN apk add --no-cache \
    bash git icu-dev libzip-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    zlib-dev curl-dev

# Build deps для phpize/pecl (Xdebug) + заголовки ядра
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers

# Розширення PHP з ядра
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" intl pdo_mysql zip gd opcache

# Xdebug (через PECL)
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Налаштування PHP
COPY conf/php.ini /usr/local/etc/php/conf.d/zzz-dev.ini
COPY conf/xdebug.ini /usr/local/etc/php/conf.d/zzz-xdebug.ini

WORKDIR /var/www/html

# Нерутовий користувач (зручно для кешів vendor/var)
RUN addgroup -g 1000 app && adduser -D -G app -u 1000 app \
 && chown -R app:app /var/www/html

# (Опційно) прибираємо build-deps, щоб зменшити образ
RUN apk del .build-deps

USER app
