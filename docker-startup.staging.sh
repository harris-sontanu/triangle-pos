#!/bin/sh

# Sync public files (including Vite-built assets) to shared volume
cp -rT /app/public-docker /app/public

# Fix permissions for PHP-FPM
chown -R www-data:www-data /app/public /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

php-fpm
