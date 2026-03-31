#!/bin/bash
set -e

cd /app

# Créer un .env vide si absent (Railway injecte les vraies variables)
if [ ! -f .env ]; then
    touch .env
fi

# Cache Symfony
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Migrations
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Démarrer FrankenPHP
exec /start-container.sh
