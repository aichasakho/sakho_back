FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git curl libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code source
WORKDIR /var/www
COPY . .

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Donner les permissions nécessaires
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port
EXPOSE 8080

# Lancer migrations avant le démarrage
#CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080

CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8080
