#!/bin/bash

echo "=== ShoppingFree entrypoint ==="

# Retry migrations (MySQL Railway peut prendre quelques secondes)
for i in 1 2 3; do
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration && break
    echo "Migration attempt $i failed, retrying in 5s..."
    sleep 5
done

# Cache Symfony
php bin/console cache:clear --env=prod --no-warmup || echo "cache:clear failed, continuing..."
php bin/console cache:warmup --env=prod || echo "cache:warmup failed, continuing..."

echo "=== Starting FrankenPHP ==="
exec frankenphp run --config /app/Caddyfile
