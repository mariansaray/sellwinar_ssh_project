#!/bin/bash
# =====================================================
# Sellwinar — Deploy script
# Spusti na serveri: bash deploy.sh
# =====================================================

set -e
cd "$(dirname "$0")"

echo ""
echo "=========================================="
echo "  SELLWINAR — Deploy"
echo "=========================================="
echo ""

# 1. Závislosti
echo "[1/7] Inštalujem závislosti..."
composer install --optimize-autoloader --no-dev --quiet 2>/dev/null || composer install --optimize-autoloader --no-dev

# 2. Kľúč (len ak neexistuje)
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "[2/7] Generujem APP_KEY..."
    php artisan key:generate --force
else
    echo "[2/7] APP_KEY už existuje, preskakujem."
fi

# 3. Migrácie
echo "[3/7] Spúšťam migrácie..."
php artisan migrate --force

# 4. Seedery (len ak nie sú dáta)
echo "[4/7] Kontrolujem seedery..."
php artisan db:seed --force 2>/dev/null || echo "    Seedery už boli spustené."

# 5. Cache
echo "[5/7] Optimalizujem cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Permissions
echo "[6/7] Nastavujem permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 7. Queue
echo "[7/7] Reštartujem queue..."
php artisan queue:restart 2>/dev/null || true

echo ""
echo "=========================================="
echo "  HOTOVO!"
echo ""
echo "  Web: https://sellwinar.com"
echo "  Admin: https://sellwinar.com/login"
echo "  Login: admin@sellwinar.com / admin123"
echo "=========================================="
echo ""
