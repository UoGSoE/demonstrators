#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

set -xe

# Install Composer and project dependencies.
#curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ping the mysql container
# ping -c 3 mysql

# Composer install parallel install plugin
# composer -q global require "hirak/prestissimo:^0.3"

# Install php code sniffer
# curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar

# Copy over testing configuration.
cp -f .env.gitlab .env
cp -f phpunit.xml.gitlab phpunit.xml

# Composer install project dependencies
composer -q install --no-progress --no-interaction

# Generate an application key. Re-cache.
php artisan key:generate

# Run database migrations.
php artisan migrate

# Run database seed.
#php artisan db:seed

#

