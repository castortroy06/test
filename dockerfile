FROM php:8.1-fpm

# Install PostgreSQL PDO
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql
