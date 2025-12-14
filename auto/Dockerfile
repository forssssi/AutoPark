### Production multi-stage Dockerfile

# Build stage: composer install
FROM php:8.3-cli AS build
WORKDIR /srv/app
RUN apt-get update && apt-get install -y --no-install-recommends git unzip zip libzip-dev libicu-dev libxml2-dev libonig-dev && rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-progress --optimize-autoloader
COPY . /srv/app

# Production image
FROM php:8.3-fpm-alpine
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev libxml2-dev icu && docker-php-ext-install intl zip opcache
COPY --from=build /srv/app /srv/app
WORKDIR /srv/app
RUN addgroup -S www && adduser -S www -G www || true
RUN chown -R www:www /srv/app/var || true
USER www
EXPOSE 9000
CMD ["php-fpm"]
