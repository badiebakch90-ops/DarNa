# ════════════════════════════════════════════════════════════
#   Dockerfile pour DarNa Laravel sur Render
#   Base : PHP 8.2 + Apache (la plus stable et simple)
# ════════════════════════════════════════════════════════════

FROM php:8.2-apache

# ─── 1. Installer les dependances systeme et extensions PHP ───
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ─── 2. Activer mod_rewrite (necessaire pour Laravel) ───
RUN a2enmod rewrite

# ─── 3. Configurer Apache pour pointer vers le dossier public/ ───
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Autoriser .htaccess (pour le routage Laravel)
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# ─── 4. Installer Composer ───
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ─── 5. Definir le dossier de travail ───
WORKDIR /var/www/html

# ─── 6. Copier d'abord composer.json pour profiter du cache Docker ───
COPY composer.json composer.lock ./

# Installer les dependances PHP en mode production (sans dev, optimise)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# ─── 7. Copier tout le code de l'application ───
COPY . .

# Generer l'autoloader optimise
RUN composer dump-autoload --optimize --no-dev

# ─── 8. Permissions correctes pour Laravel ───
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─── 9. Port HTTP standard ───
EXPOSE 80

# ─── 10. Commande de demarrage ───
# - Cree les liens de stockage
# - Lance les migrations en production
# - Cache les configs/routes/views pour la performance
# - Demarre Apache
CMD php artisan storage:link --force || true \
    && php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && apache2-foreground
