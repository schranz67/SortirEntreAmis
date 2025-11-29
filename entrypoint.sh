#!/bin/sh
set -e

# Définir DB_HOST si non défini
DB_HOST=${DB_HOST:-database}

# Attendre MySQL (timeout de 60s)
for i in $(seq 1 30); do
    if mysqladmin ping -h"$DB_HOST" --silent; then
        echo "MySQL is ready !"
        break
    fi
    echo "Waiting for MySQL..."
    sleep 30
done

# Créer la base si nécessaire
echo "=== Vérification MySQL ==="
php bin/console doctrine:database:create --if-not-exists

# Appliquer les migrations
echo "=== Application des migrations ==="
php bin/console doctrine:migrations:migrate --no-interaction

# Charger les fixtures
echo "=== Fixtures ==="
php bin/console doctrine:fixtures:load --no-interaction

# Installer les dépendances front et compiler les assets
#npm install
#npm run dev # ou npm run build pour prod

# Lancer le serveur PHP interne
echo "=== Démarrage du serveur PHP ==="
php -S 0.0.0.0:80 -t public
