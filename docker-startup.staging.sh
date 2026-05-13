#!/bin/sh

php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php-fpm
