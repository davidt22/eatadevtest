#!/bin/bash

# Install vendors
php -d memory_limit=-1 composer.phar install -o --no-interaction --prefer-dist


# Install bower dependencies
bower install --allow-root

# Create database
php bin/console doctrine:database:create

# Apply schema changes
php bin/console doctrine:schema:create

# Install assets
php bin/console assets:install web
php bin/console assetic:dump

# Change user and group of files
chown -R www-data:www-data /var/www/

# Grant permissions to cache and logs folders
chmod -R 777 var/cache/ var/logs var/sessions
