FROM dunglas/frankenphp:php8.2-bookworm

# Extensions PHP nécessaires pour Symfony + MySQL
RUN install-php-extensions \
    mbstring \
    intl \
    pdo_mysql \
    ctype \
    iconv \
    zip \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Fichier .env minimal requis par Symfony Runtime au démarrage
# Les vraies valeurs (DATABASE_URL, APP_SECRET...) sont injectées par Railway via variables d'environnement
RUN printf 'APP_ENV=prod\nAPP_DEBUG=0\nAPP_SECRET=placeholder\n' > /app/.env

# Composer install : COMPOSER_ALLOW_SUPERUSER pour autoriser les plugins (symfony/runtime)
# --no-scripts car les scripts Symfony (cache:clear, assets:install) nécessitent la BDD
RUN COMPOSER_ALLOW_SUPERUSER=1 APP_ENV=prod composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

CMD ["/usr/local/bin/docker-entrypoint.sh"]
