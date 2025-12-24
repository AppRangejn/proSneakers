FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip curl \
 && docker-php-ext-install pdo_mysql zip \
 && pecl install redis \
 && docker-php-ext-enable redis

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
 && php artisan key:generate \
 && chown -R www-data:www-data /var/www/backend \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
