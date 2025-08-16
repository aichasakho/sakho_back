# Utiliser une image PHP officielle avec les extensions nécessaires
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git curl libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code source
WORKDIR /var/www
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Donner les permissions à Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port
EXPOSE 8080

# Lancer Laravel avec le serveur intégré de PHP
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
