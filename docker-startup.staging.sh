#!/bin/sh

# Sync public files (including Vite-built assets) to shared volume
cp -rT /app/public-docker /app/public

php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

php-fpm
