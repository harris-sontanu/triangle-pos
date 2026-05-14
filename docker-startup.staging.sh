#!/bin/sh

echo "Starting deployment sync..."

# Sync public files
echo "Syncing public directory..."
cp -rT /app/public-docker /app/public

# Sync storage files (needed if volume is empty)
echo "Syncing storage directory..."
cp -rT /app/storage-docker /app/storage

# Fix permissions
echo "Fixing permissions..."
chown -R www-data:www-data /app/public /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Verify if index.php exists
if [ -f "/app/public/index.php" ]; then
    echo "SUCCESS: /app/public/index.php found."
else
    echo "ERROR: /app/public/index.php NOT FOUND!"
fi

echo "Running migrations..."
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

echo "Starting PHP-FPM..."
php-fpm
