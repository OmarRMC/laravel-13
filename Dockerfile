# syntax=docker/dockerfile:1
ARG PHP_VERSION=8.4
FROM php:${PHP_VERSION}-cli-alpine

ARG UID=1000
ARG GID=1000

# Extensiones que Laravel 13 necesita para PostgreSQL, mas Node/npm para Vite.
# Nota: docker-php-ext-install compila, por eso hace falta $PHPIZE_DEPS.
RUN apk add --no-cache \
        bash git unzip nodejs npm \
        $PHPIZE_DEPS icu-dev libzip-dev libpq-dev \
 && docker-php-ext-install -j"$(nproc)" pdo_pgsql bcmath intl zip \
 && printf "memory_limit=512M\nupload_max_filesize=100M\npost_max_size=100M\n" \
        > /usr/local/etc/php/conf.d/99-app.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Usuario con el mismo UID/GID del host: lo que genere artisan te pertenece a ti
RUN addgroup -g ${GID} app \
 && adduser -u ${UID} -G app -s /bin/bash -D app

WORKDIR /var/www/html
USER app

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
