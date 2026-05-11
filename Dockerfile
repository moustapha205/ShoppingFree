FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN echo "APP_ENV=prod\nAPP_DEBUG=0\nAPP_SECRET=placeholder" > /var/www/html/.env

RUN APP_ENV=prod composer install --no-dev --optimize-autoloader --no-scripts

RUN echo "DocumentRoot /var/www/html/public" >> /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["/usr/local/bin/docker-entrypoint.sh"]