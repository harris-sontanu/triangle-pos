#!/bin/sh

# Set the correct permissions for the required directories
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear and cache Laravel configurations (adding || true so it continues even if APP_KEY or DB is not ready yet)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true

# Run database migrations
php artisan migrate --force || true

# Create storage link if it doesn't exist
php artisan storage:link || true

# Start supervisord to manage Nginx and PHP-FPM
exec "$@"
