FROM php:8.2-apache

RUN apt-get update

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt-get update -y && apt-get install -y libpng-dev libfreetype6-dev libyaml-dev

RUN  docker-php-ext-configure gd --with-freetype
RUN  docker-php-ext-install gd

RUN apt-get update && apt-get install -y \
    npm

RUN npm install -g sass

# Installer et activer l'extension YAML
RUN pecl install yaml && docker-php-ext-enable yaml

RUN usermod -u 1000 www-data

RUN a2enmod rewrite
