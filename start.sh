#!/bin/bash
set -e

cd /app

# Cache Symfony
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Migrations
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Démarrer FrankenPHP
exec /start-container.sh
