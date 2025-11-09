FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip \
 && docker-php-ext-install pdo_mysql zip \
 && pecl install redis \
 && docker-php-ext-enable redis


COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

COPY . .


RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
 && composer dump-autoload


RUN chown -R www-data:www-data /var/www/backend \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
