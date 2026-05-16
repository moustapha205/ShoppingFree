FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# COMPOSER_ALLOW_SUPERUSER : autorise les plugins (symfony/runtime génère vendor/autoload_runtime.php)
# --no-scripts : ignore les auto-scripts (cache:clear, assets:install) qui nécessitent la BDD
RUN COMPOSER_ALLOW_SUPERUSER=1 APP_ENV=prod composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction

# Apache : DocumentRoot sur public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
        /etc/apache2/sites-enabled/000-default.conf \
    && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
        >> /etc/apache2/sites-enabled/000-default.conf \
    && a2enmod rewrite

EXPOSE 80

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["/usr/local/bin/docker-entrypoint.sh"]
