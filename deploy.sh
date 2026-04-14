#!/bin/bash
# Sellwinar — Deploy script
# Spusti tento script na serveri po git pull

set -e

echo "=== Sellwinar Deploy ==="

echo "1/5 Inštalujem závislosti..."
composer install --optimize-autoloader --no-dev --quiet

echo "2/5 Spúšťam migrácie..."
php artisan migrate --force

echo "3/5 Čistím cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "4/5 Nastavujem permissions..."
chmod -R 775 storage bootstrap/cache

echo "5/5 Reštartujem queue worker..."
php artisan queue:restart

echo ""
echo "=== Deploy hotový! ==="
echo ""
