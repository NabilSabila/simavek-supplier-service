#!/bin/sh
set -e

echo "Installing dependencies..."
composer update --no-interaction --no-dev --optimize-autoloader

echo "Generating APP_KEY if empty..."
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

echo "Menjalankan Laravel migration..."
php artisan migrate --force

echo "Menjalankan Supplier Service..."
php -S 0.0.0.0:8000 -t public