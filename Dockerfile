# Indication du travail avec PHP v
FROM php:8.2-apache

# Installation des extensions nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Activation du mode rewrite
RUN a2enmod rewrite

# Définition du répertoire de travail dans le répertoire où le conteneur va résider
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY ./ /var/www/html

# Configuration Apache pour Symfony
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Donner les droits à Apache
RUN chown -R www-data:www-data /var/www/html/var

# Exposition du port
EXPOSE 80

CMD ["apache2-foreground"]
