#!/bin/bash
set -e

# Variables d'environnement Railway disponibles ici (DATABASE_URL, APP_SECRET, etc.)

# Migrations Doctrine (safe grâce aux IF NOT EXISTS)
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Cache Symfony prod
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

# Assets
php bin/console assets:install public --env=prod

exec apache2-foreground
