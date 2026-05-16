#!/bin/bash

echo "=== ShoppingFree entrypoint ==="

# Migrations (avec retry car MySQL peut mettre quelques secondes à être prêt)
for i in 1 2 3; do
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration && break
    echo "Migration attempt $i failed, retrying in 5s..."
    sleep 5
done

# Cache
php bin/console cache:clear --env=prod --no-warmup || echo "cache:clear failed, continuing..."
php bin/console cache:warmup --env=prod || echo "cache:warmup failed, continuing..."

echo "=== Starting Apache ==="
exec apache2-foreground
