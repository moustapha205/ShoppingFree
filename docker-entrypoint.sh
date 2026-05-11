#!/bin/bash
set -e

# Run Symfony post-install scripts now that env vars are available
php bin/console cache:clear --no-warmup
php bin/console cache:warmup

exec apache2-foreground
